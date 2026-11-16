<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TotpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TwoFactorController extends Controller
{
    /**
     * Show the two-factor authentication challenge during login.
     */
    public function showChallenge(Request $request)
    {
        if (!$request->session()->has('2fa:user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    /**
     * Verify the two-factor challenge (TOTP or recovery code) to complete login.
     */
    public function verifyChallenge(Request $request, TotpService $totp)
    {
        $userId = $request->session()->get('2fa:user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);
        if (!$user || !$user->is_active) {
            $request->session()->forget(['2fa:user_id', '2fa:remember']);
            return redirect()->route('login')->withErrors(['email' => __('messages.account_deactivated')]);
        }

        $code = trim($request->input('code', ''));
        $recoveryCode = trim($request->input('recovery_code', ''));
        $verified = false;

        if ($code !== '') {
            $verified = $totp->verify($user->two_factor_secret, $code);
        } elseif ($recoveryCode !== '') {
            $codes = $user->two_factor_recovery_codes ?? [];
            $normRecovery = strtoupper(str_replace(' ', '', $recoveryCode));

            foreach ($codes as $idx => $storedCode) {
                if (strtoupper(str_replace(' ', '', $storedCode)) === $normRecovery) {
                    $verified = true;
                    // Invalidate used recovery code
                    unset($codes[$idx]);
                    $user->two_factor_recovery_codes = array_values($codes);
                    $user->save();
                    break;
                }
            }
        }

        if (!$verified) {
            return back()->withErrors([
                'code' => __('messages.two_factor_invalid_code'),
            ]);
        }

        $remember = $request->session()->pull('2fa:remember', false);
        $request->session()->forget('2fa:user_id');

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('home'))
            ->with('success', __('messages.two_factor_login_success', ['name' => $user->name]));
    }

    /**
     * Show the 2FA setup screen with QR code and secret.
     */
    public function showSetup(TotpService $totp)
    {
        $user = Auth::user();

        if ($user->hasTwoFactorEnabled()) {
            return redirect()->route('profile')
                ->with('info', __('messages.two_factor_already_active'));
        }

        // Generate or reuse pending secret
        if (!$user->two_factor_secret) {
            $user->two_factor_secret = $totp->generateSecret();
            $user->two_factor_recovery_codes = $totp->generateRecoveryCodes();
            $user->two_factor_confirmed_at = null;
            $user->save();
        }

        $qrCodeUrl = $totp->getQrCodeImageUrl('PawSafe', $user->email, $user->two_factor_secret);
        $otpAuthUri = $totp->getOtpAuthUri('PawSafe', $user->email, $user->two_factor_secret);

        return view('auth.two-factor-setup', [
            'secret' => $user->two_factor_secret,
            'qrCodeUrl' => $qrCodeUrl,
            'otpAuthUri' => $otpAuthUri,
            'recoveryCodes' => $user->two_factor_recovery_codes ?? [],
        ]);
    }

    /**
     * Confirm activation of 2FA by verifying the first code.
     */
    public function confirm(Request $request, TotpService $totp)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();

        if (!$totp->verify($user->two_factor_secret, $request->code)) {
            return back()->withErrors([
                'code' => __('messages.two_factor_invalid_code'),
            ]);
        }

        $user->two_factor_confirmed_at = now();
        $user->save();

        return redirect()->route('profile')
            ->with('success', __('messages.two_factor_enabled_success'))
            ->with('show_recovery_codes', true);
    }

    /**
     * Disable 2FA for the authenticated user.
     */
    public function disable(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => __('messages.password_incorrect'),
            ]);
        }

        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        return redirect()->route('profile')
            ->with('success', __('messages.two_factor_disabled_success'));
    }

    /**
     * Regenerate new recovery codes.
     */
    public function regenerateRecoveryCodes(Request $request, TotpService $totp)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => __('messages.password_incorrect'),
            ]);
        }

        $user->two_factor_recovery_codes = $totp->generateRecoveryCodes();
        $user->save();

        return redirect()->route('profile')
            ->with('success', __('messages.two_factor_codes_regenerated'))
            ->with('show_recovery_codes', true);
    }
}

