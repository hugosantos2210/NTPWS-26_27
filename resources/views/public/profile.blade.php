@extends('layouts.app')

@section('title', 'O Meu Perfil — PawSafe')

@section('content')
<div class="container" style="padding: 3.5rem 1.5rem;">
    <div style="margin-bottom: 2.5rem;">
        <span class="badge badge-primary">Área do Utilizador</span>
        <h1 style="margin-top: 0.5rem;">Olá, {{ $user->name }}!</h1>
        <p style="color: var(--text-muted); font-size: 1.05rem;">Faça a gestão dos seus dados pessoais e acompanhe os seus pedidos de adoção.</p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2.5rem; align-items: flex-start;">
        <!-- Left: Profile & Password update -->
        <div>
            <!-- Personal Info Card -->
            <div style="background: #fff; padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1.25rem;">Dados Pessoais</h3>

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name" class="form-label">Nome Completo</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                        @error('name') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Endereço de Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                        @error('email') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Contacto Telefónico</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" placeholder="+351 900 000 000" class="form-control">
                        @error('phone') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary" style="margin-top: 0.5rem;">Guardar Alterações</button>
                </form>
            </div>

            <!-- Password Card -->
            <div style="background: #fff; padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1.25rem;">Alterar Password</h3>

                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="current_password" class="form-label">Password Atual</label>
                        <input type="password" name="current_password" id="current_password" class="form-control" required>
                        @error('current_password') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="new_password" class="form-label">Nova Password</label>
                        <input type="password" name="new_password" id="new_password" class="form-control" required>
                        @error('new_password') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="new_password_confirmation" class="form-label">Confirmar Nova Password</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-outline" style="margin-top: 0.5rem;">Atualizar Password</button>
                </form>
            </div>

            <!-- Two-Factor Authentication Card -->
            <div style="background: #fff; padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <h3 style="margin: 0;">🔐 Autenticação de Dois Fatores (2FA)</h3>
                    @if($user->hasTwoFactorEnabled())
                        <span class="badge badge-success">Ativo</span>
                    @else
                        <span class="badge badge-warning">Desativado</span>
                    @endif
                </div>

                @if($user->hasTwoFactorEnabled())
                    <p style="color: var(--text-muted); font-size: 0.92rem; line-height: 1.6; margin-bottom: 1.25rem;">
                        A sua conta está protegida com autenticação de dois fatores. Cada início de sessão requer a introdução de um código de 6 dígitos gerado pela sua aplicação móvel de autenticação.
                    </p>

                    @if(session('show_recovery_codes') && !empty($user->two_factor_recovery_codes))
                        <div style="margin-bottom: 1.5rem; padding: 1.25rem; background: #fffbeb; border: 1px solid #fde68a; border-radius: var(--radius-md);">
                            <h4 style="color: #92400e; margin-top: 0; font-size: 0.95rem;">⚠️ Códigos de Recuperação de Emergência</h4>
                            <p style="font-size: 0.82rem; color: #78350f; margin-bottom: 0.75rem;">
                                Guarde estes códigos num local seguro. Se perder o acesso ao seu telemóvel, cada código pode ser usado uma única vez para iniciar sessão:
                            </p>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.4rem; font-family: monospace; font-size: 0.88rem; text-align: center;">
                                @foreach($user->two_factor_recovery_codes as $recCode)
                                    <div style="padding: 0.35rem; background: #fff; border: 1px solid #fde68a; border-radius: 3px; font-weight: 700;">{{ $recCode }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div style="display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem;">
                        <!-- Regenerate Recovery Codes -->
                        <form action="{{ route('two-factor.recovery-codes') }}" method="POST" style="display: flex; gap: 0.75rem; align-items: flex-end;">
                            @csrf
                            <div style="flex: 1;">
                                <label for="rec_current_password" class="form-label" style="font-size: 0.8rem;">Confirmar Password Atual</label>
                                <input type="password" name="current_password" id="rec_current_password" class="form-control" placeholder="••••••••" required>
                            </div>
                            <button type="submit" class="btn btn-outline btn-sm" style="white-space: nowrap;">Regenerar Códigos</button>
                        </form>

                        <!-- Disable 2FA -->
                        <form action="{{ route('two-factor.disable') }}" method="POST" onsubmit="return confirm('Tem a certeza que deseja desativar a autenticação de dois fatores?');" style="margin-top: 0.5rem; padding-top: 1rem; border-top: 1px solid var(--border);">
                            @csrf
                            <div style="display: flex; gap: 0.75rem; align-items: flex-end;">
                                <div style="flex: 1;">
                                    <label for="dis_current_password" class="form-label" style="font-size: 0.8rem;">Password para Desativar</label>
                                    <input type="password" name="current_password" id="dis_current_password" class="form-control" placeholder="••••••••" required>
                                </div>
                                <button type="submit" class="btn btn-outline btn-sm" style="color: var(--danger)!important; white-space: nowrap;">Desativar 2FA</button>
                            </div>
                        </form>
                    </div>
                @else
                    <p style="color: var(--text-muted); font-size: 0.92rem; line-height: 1.6; margin-bottom: 1.5rem;">
                        A autenticação de dois fatores adiciona uma camada extra de segurança à sua conta, evitando acessos não autorizados mesmo que a sua palavra-passe seja comprometida.
                    </p>
                    <a href="{{ route('two-factor.setup') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                        <span>🔐</span>
                        <span>Configurar e Ativar 2FA</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Right: Adoption Requests -->
        <div>
            <div style="background: #fff; padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
                <h3 style="margin-bottom: 1.25rem;">Os Meus Pedidos de Adoção</h3>

                @if($adoptionRequests->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        @foreach($adoptionRequests as $request)
                            <div style="padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border); background: var(--bg-muted);">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <h4 style="margin: 0;">
                                        <a href="{{ route('pets.show', $request->pet) }}">{{ $request->pet->name }}</a>
                                    </h4>
                                    <span class="badge {{ $request->status == 'approved' ? 'badge-success' : ($request->status == 'rejected' ? 'badge-danger' : 'badge-warning') }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </div>

                                <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                                    Submetido em: {{ $request->created_at->format('d/m/Y H:i') }}
                                </div>

                                <div style="font-size: 0.9rem; margin-bottom: 0.5rem;">
                                    <strong>A sua mensagem:</strong> "{{ $request->message }}"
                                </div>

                                @if($request->admin_notes)
                                    <div style="margin-top: 0.5rem; padding: 0.75rem; background: #fff; border-radius: var(--radius-sm); font-size: 0.85rem; border-left: 3px solid var(--primary);">
                                        <strong>Nota da Equipa:</strong> {{ $request->admin_notes }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted);">
                        <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🐕</div>
                        <p>Ainda não submeteu nenhum pedido de adoção.</p>
                        <a href="{{ route('pets.index') }}" class="btn btn-primary btn-sm" style="margin-top: 1rem;">Ver Animais Disponíveis</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
