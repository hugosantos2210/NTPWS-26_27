@extends('layouts.app')

@section('title', __('messages.setup_two_factor_title', ['default' => 'Configurar Autenticação de Dois Fatores']) . ' — PawSafe')

@section('content')
<div class="container" style="padding: 3.5rem 1.5rem;">
    <div style="max-width: 640px; margin: 0 auto; background: #fff; padding: 2.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-md);">
        
        <div style="margin-bottom: 2rem;">
            <a href="{{ route('profile') }}" style="font-size: 0.9rem; font-weight: 600; text-decoration: none;">← {{ __('messages.back_to_profile', ['default' => 'Voltar ao Perfil']) }}</a>
            <h1 style="font-size: 1.9rem; margin-top: 0.75rem;">🔐 {{ __('messages.setup_two_factor_heading', ['default' => 'Ativar Autenticação de Dois Fatores (2FA)']) }}</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.35rem;">
                {{ __('messages.two_factor_setup_desc', ['default' => 'Aumente a segurança da sua conta exigindo um código temporário de 6 dígitos ao iniciar sessão.']) }}
            </p>
        </div>

        <!-- Step 1: Scan QR Code -->
        <div style="margin-bottom: 2rem; padding: 1.5rem; background: var(--bg-muted); border-radius: var(--radius-md); border: 1px solid var(--border);">
            <h3 style="font-size: 1.1rem; margin-bottom: 0.75rem;">1. {{ __('messages.step_scan_qr', ['default' => 'Ler o Código QR na aplicação']) }}</h3>
            <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 1.25rem;">
                {{ __('messages.scan_qr_desc', ['default' => 'Abra o Google Authenticator, Authy ou Microsoft Authenticator no seu telemóvel e digitalize o código abaixo:']) }}
            </p>

            <div style="text-align: center; margin: 1rem 0;">
                <img src="{{ $qrCodeUrl }}" alt="QR Code 2FA" style="border: 4px solid #fff; border-radius: var(--radius-sm); box-shadow: var(--shadow-sm); max-width: 200px;">
            </div>

            <div style="margin-top: 1rem; font-size: 0.85rem; color: var(--text-muted); text-align: center;">
                <span>{{ __('messages.manual_entry_key', ['default' => 'Não consegue ler o QR Code? Digite a chave manualmente:']) }}</span><br>
                <code style="font-size: 1rem; font-weight: 700; color: var(--primary); letter-spacing: 0.15em; background: #fff; padding: 0.35rem 0.75rem; border-radius: 4px; border: 1px solid var(--border); display: inline-block; margin-top: 0.35rem;">{{ $secret }}</code>
            </div>
        </div>

        <!-- Step 2: Emergency Recovery Codes -->
        <div style="margin-bottom: 2rem; padding: 1.5rem; background: #fffbeb; border-radius: var(--radius-md); border: 1px solid #fde68a;">
            <h3 style="font-size: 1.1rem; color: #92400e; margin-bottom: 0.5rem;">2. ⚠️ {{ __('messages.step_save_recovery', ['default' => 'Guarde os Códigos de Recuperação']) }}</h3>
            <p style="font-size: 0.85rem; color: #78350f; margin-bottom: 1rem;">
                {{ __('messages.recovery_notice', ['default' => 'Se perder o acesso ao seu telemóvel, estes códigos de uso único são a única forma de recuperar o acesso à sua conta. Guarde-os num local seguro.']) }}
            </p>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; background: #fff; padding: 1rem; border-radius: 4px; border: 1px solid #fde68a; font-family: monospace; font-size: 0.95rem; font-weight: 600; text-align: center;">
                @foreach($recoveryCodes as $code)
                    <div style="padding: 0.35rem 0.5rem; background: #fef3c7; border-radius: 3px;">{{ $code }}</div>
                @endforeach
            </div>
        </div>

        <!-- Step 3: Confirmation -->
        <div style="padding: 1.5rem; background: #fff; border-radius: var(--radius-md); border: 1px solid var(--border);">
            <h3 style="font-size: 1.1rem; margin-bottom: 0.75rem;">3. {{ __('messages.step_confirm_code', ['default' => 'Confirmar Ativação']) }}</h3>
            <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 1rem;">
                {{ __('messages.confirm_code_desc', ['default' => 'Introduza o código de 6 dígitos gerado pela sua aplicação para concluir a ativação:']) }}
            </p>

            <form action="{{ route('two-factor.confirm') }}" method="POST">
                @csrf
                <div class="form-group">
                    <input type="text" name="code" id="code" class="form-control" style="font-size: 1.3rem; letter-spacing: 0.2em; text-align: center; max-width: 240px; margin: 0 auto; font-weight: 700;" placeholder="000000" maxlength="6" required autocomplete="one-time-code">
                    @error('code') <div class="form-error" style="text-align: center; margin-top: 0.5rem;">{{ $message }}</div> @enderror
                </div>

                <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 1.25rem;">
                    <button type="submit" class="btn btn-primary btn-lg">{{ __('messages.confirm_and_activate', ['default' => 'Confirmar e Ativar 2FA']) }}</button>
                    <a href="{{ route('profile') }}" class="btn btn-outline btn-lg">{{ __('messages.cancel', ['default' => 'Cancelar']) }}</a>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

