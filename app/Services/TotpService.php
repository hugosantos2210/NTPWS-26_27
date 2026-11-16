<?php

namespace App\Services;

class TotpService
{
    private const BASE32_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a random base32 secret.
     */
    public function generateSecret(int $length = 16): string
    {
        $secret = '';
        $max = strlen(self::BASE32_CHARS) - 1;
        for ($i = 0; $i < $length; $i++) {
            $secret .= self::BASE32_CHARS[random_int(0, $max)];
        }
        return $secret;
    }

    /**
     * Calculate 6-digit TOTP code for a secret at a given timestamp.
     */
    public function getCode(string $secret, ?int $timestamp = null): string
    {
        $timestamp = $timestamp ?? time();
        $timeSlice = (int) floor($timestamp / 30);

        $secretKey = $this->base32Decode($secret);

        // Pack timeSlice into 8-byte big-endian binary string
        $timeData = pack('N*', 0, $timeSlice);

        $hash = hash_hmac('sha1', $timeData, $secretKey, true);

        // Dynamic truncation (RFC 4226)
        $offset = ord(substr($hash, -1)) & 0x0F;
        $binary = (
            ((ord($hash[$offset]) & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) << 8) |
            (ord($hash[$offset + 3]) & 0xFF)
        );

        $otp = $binary % 1000000;

        return str_pad((string) $otp, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verify a 6-digit code against a secret within an allowed time window (drift).
     */
    public function verify(string $secret, string $code, int $window = 1): bool
    {
        $code = trim($code);
        if (strlen($code) !== 6 || !ctype_digit($code)) {
            return false;
        }

        $currentTime = time();
        for ($i = -$window; $i <= $window; $i++) {
            $expected = $this->getCode($secret, $currentTime + ($i * 30));
            if (hash_equals($expected, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate the otpauth:// URI for authenticator apps.
     */
    public function getOtpAuthUri(string $company, string $accountName, string $secret): string
    {
        $label = rawurlencode($company) . ':' . rawurlencode($accountName);
        $params = http_build_query([
            'secret'    => $secret,
            'issuer'    => $company,
            'algorithm' => 'SHA1',
            'digits'    => 6,
            'period'    => 30,
        ]);

        return "otpauth://totp/{$label}?{$params}";
    }

    /**
     * Generate QR code image URL for scanning in Google Authenticator, Authy, etc.
     */
    public function getQrCodeImageUrl(string $company, string $accountName, string $secret): string
    {
        $uri = $this->getOtpAuthUri($company, $accountName, $secret);
        return 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=10&data=' . urlencode($uri);
    }

    /**
     * Generate an array of one-time emergency recovery codes.
     */
    public function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $part1 = strtoupper(bin2hex(random_bytes(2)));
            $part2 = strtoupper(bin2hex(random_bytes(2)));
            $codes[] = "{$part1}-{$part2}";
        }
        return $codes;
    }

    /**
     * Decode a base32 string into binary.
     */
    private function base32Decode(string $b32): string
    {
        $b32 = strtoupper(trim($b32));
        $buffer = 0;
        $bitsLeft = 0;
        $output = '';

        for ($i = 0; $i < strlen($b32); $i++) {
            $char = $b32[$i];
            if ($char === '=' || $char === ' ' || $char === '-') {
                continue;
            }
            $val = strpos(self::BASE32_CHARS, $char);
            if ($val === false) {
                continue;
            }

            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $output .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $output;
    }
}

