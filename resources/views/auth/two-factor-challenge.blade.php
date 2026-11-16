@extends('layouts.app')

@section('title', __('messages.two_factor_title', ['default' => 'Autenticação de Dois Fatores']) . ' — PawSafe')

@section('content')
<div class="container" style="padding: 4.5rem 1.5rem;">
    <div style="max-width: 440px; margin: 0 auto; background: #fff; padding: 2.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-md);">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="font-size: 2.8rem; margin-bottom: 0.5rem;">🔐</div>
            <h2>{{ __('messages.two_factor_heading', ['default' => 'Autenticação de Dois Fatores']) }}</h2>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.5rem;" id="challenge-instructions">
                {{ __('messages.two_factor_instructions', ['default' => 'Introduza o código de 6 dígitos gerado pela sua aplicação de autenticação (Google Authenticator, Authy, etc.).']) }}
            </p>
        </div>

        <form action="{{ route('two-factor.challenge') }}" method="POST">
            @csrf

            <!-- 6-digit TOTP Code Input -->
            <div class="form-group" id="totp-group">
                <label for="code" class="form-label">{{ __('messages.two_factor_code_label', ['default' => 'Código de Autenticação']) }}</label>
                <input type="text" name="code" id="code" class="form-control" style="font-size: 1.4rem; letter-spacing: 0.25em; text-align: center; font-weight: 700;" placeholder="000000" maxlength="6" autofocus autocomplete="one-time-code">
                @error('code') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <!-- Recovery Code Input (hidden by default) -->
            <div class="form-group" id="recovery-group" style="display: none;">
                <label for="recovery_code" class="form-label">{{ __('messages.recovery_code_label', ['default' => 'Código de Recuperação de Emergência']) }}</label>
                <input type="text" name="recovery_code" id="recovery_code" class="form-control" style="font-size: 1.1rem; letter-spacing: 0.1em; text-align: center; font-family: monospace;" placeholder="XXXX-XXXX">
                @error('recovery_code') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 0.5rem;">{{ __('messages.two_factor_verify_btn', ['default' => 'Confirmar e Entrar']) }}</button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border); font-size: 0.9rem;">
            <button type="button" id="toggle-recovery-btn" style="background: none; border: none; color: var(--primary); font-weight: 600; cursor: pointer; text-decoration: underline;">
                {{ __('messages.use_recovery_code', ['default' => 'Usar um código de recuperação de emergência']) }}
            </button>
        </div>

        <div style="text-align: center; margin-top: 1rem;">
            <a href="{{ route('login') }}" style="color: var(--text-muted); font-size: 0.85rem; text-decoration: none;">← {{ __('messages.back_to_login', ['default' => 'Voltar ao Login']) }}</a>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const toggleBtn = document.getElementById('toggle-recovery-btn');
    const totpGroup = document.getElementById('totp-group');
    const recoveryGroup = document.getElementById('recovery-group');
    const codeInput = document.getElementById('code');
    const recoveryInput = document.getElementById('recovery_code');
    const instructions = document.getElementById('challenge-instructions');

    let usingRecovery = false;

    toggleBtn.addEventListener('click', function() {
        usingRecovery = !usingRecovery;
        if (usingRecovery) {
            totpGroup.style.display = 'none';
            recoveryGroup.style.display = 'block';
            codeInput.value = '';
            recoveryInput.focus();
            toggleBtn.textContent = '{{ __("messages.use_auth_app", ["default" => "Usar código da aplicação autenticadora"]) }}';
            instructions.textContent = '{{ __("messages.recovery_instructions", ["default" => "Introduza um dos seus códigos de recuperação de emergência guardados."]) }}';
        } else {
            totpGroup.style.display = 'block';
            recoveryGroup.style.display = 'none';
            recoveryInput.value = '';
            codeInput.focus();
            toggleBtn.textContent = '{{ __("messages.use_recovery_code", ["default" => "Usar um código de recuperação de emergência"]) }}';
            instructions.textContent = '{{ __("messages.two_factor_instructions", ["default" => "Introduza o código de 6 dígitos gerado pela sua aplicação de autenticação."]) }}';
        }
    });
})();
</script>
@endpush
@endsection

