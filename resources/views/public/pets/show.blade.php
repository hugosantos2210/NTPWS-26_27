@extends('layouts.app')

@section('title', $pet->name . ' — PawSafe')

@section('content')
<div class="container" style="padding: 3.5rem 1.5rem;">
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('pets.index') }}" style="font-weight: 600; font-size: 0.9rem;">← Voltar ao Catálogo de Animais</a>
    </div>

    <div style="display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 3rem; align-items: flex-start;">
        <!-- Left: Image & Description -->
        <div>
            <div style="border-radius: var(--radius-lg); overflow: hidden; height: 420px; box-shadow: var(--shadow-md); margin-bottom: 2rem;">
                <img src="{{ $pet->image_url }}" alt="{{ $pet->name }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>

            <div style="background: #fff; padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1rem;">Sobre o(a) {{ $pet->name }}</h3>
                <p style="font-size: 1.05rem; line-height: 1.8; color: var(--text-main);">
                    {{ $pet->description }}
                </p>
            </div>

            <!-- External API Breed Info (TheDogAPI / TheCatAPI integration) -->
            <div style="background: #fff; padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <h3>Informações da Raça (API Externa)</h3>
                    <span class="badge badge-accent">{{ $pet->species == 'dog' ? 'TheDogAPI' : 'TheCatAPI' }}</span>
                </div>

                @if($breedInfo)
                    <div style="padding: 1.25rem; background: var(--bg-muted); border-radius: var(--radius-md); font-size: 0.95rem;">
                        <h4 style="color: var(--primary); margin-bottom: 0.75rem;">{{ $breedInfo['name'] ?? $pet->breed }}</h4>
                        
                        @if(!empty($breedInfo['temperament']))
                            <p><strong>🎭 Temperamento:</strong> {{ $breedInfo['temperament'] }}</p>
                        @endif
                        
                        @if(!empty($breedInfo['life_span']))
                            <p><strong>⏳ Esperança de Vida:</strong> {{ $breedInfo['life_span'] }}</p>
                        @endif

                        @if(!empty($breedInfo['weight']))
                            <p><strong>⚖️ Peso Médio:</strong> {{ $breedInfo['weight'] }} kg</p>
                        @endif

                        @if(!empty($breedInfo['origin']))
                            <p><strong>🌍 Origem:</strong> {{ $breedInfo['origin'] }}</p>
                        @endif
                    </div>
                @elseif($breedError)
                    <div style="padding: 1rem; background: var(--warning-bg); border-radius: var(--radius-md); font-size: 0.9rem; color: #b45309;">
                        ℹ️ <em>{{ $breedError }}</em>
                    </div>
                @else
                    <p style="color: var(--text-muted); font-size: 0.9rem;">Informações de raça mista ou não catalogadas na API externa.</p>
                @endif
            </div>
        </div>

        <!-- Right: Profile Details & Adoption Form -->
        <div>
            <div style="background: #fff; padding: 2.25rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-md);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h1 style="font-size: 2.5rem;">{{ $pet->name }}</h1>
                    <span class="badge {{ $pet->status == 'available' ? 'badge-success' : 'badge-warning' }}" style="font-size: 0.85rem;">
                        {{ $pet->status == 'available' ? 'Disponível para Adoção' : ucfirst($pet->status) }}
                    </span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem; padding: 1.25rem; background: var(--bg-muted); border-radius: var(--radius-md);">
                    <div>
                        <span style="font-size: 0.82rem; color: var(--text-muted);">Espécie</span>
                        <div style="font-weight: 700;">{{ $pet->species == 'dog' ? '🐶 Cão' : '🐱 Gato' }}</div>
                    </div>
                    <div>
                        <span style="font-size: 0.82rem; color: var(--text-muted);">Raça</span>
                        <div style="font-weight: 700;">{{ $pet->breed }}</div>
                    </div>
                    <div>
                        <span style="font-size: 0.82rem; color: var(--text-muted);">Idade</span>
                        <div style="font-weight: 700;">{{ $pet->age_years }} anos e {{ $pet->age_months }} meses</div>
                    </div>
                    <div>
                        <span style="font-size: 0.82rem; color: var(--text-muted);">Sexo</span>
                        <div style="font-weight: 700;">{{ $pet->gender == 'male' ? 'Macho' : 'Fêmea' }}</div>
                    </div>
                    <div>
                        <span style="font-size: 0.82rem; color: var(--text-muted);">Porte</span>
                        <div style="font-weight: 700;">{{ ucfirst($pet->size) }}</div>
                    </div>
                    <div>
                        <span style="font-size: 0.82rem; color: var(--text-muted);">Chegada ao Abrigo</span>
                        <div style="font-weight: 700;">{{ $pet->created_at->format('d/m/Y') }}</div>
                    </div>
                </div>

                <!-- Adoption Action Section -->
                <div style="border-top: 1px solid var(--border); padding-top: 1.75rem;">
                    @if($pet->status !== 'available')
                        <div class="alert alert-info">
                            Este animal encontra-se atualmente em processo de adoção ou já foi acolhido.
                        </div>
                    @else
                        @auth
                            @if($existingRequest)
                                <div style="background: var(--info-bg); border: 1px solid #bae6fd; padding: 1.25rem; border-radius: var(--radius-md);">
                                    <h4 style="color: #0369a1; margin-bottom: 0.5rem;">Pedido de Adoção Já Enviado</h4>
                                    <p style="font-size: 0.9rem; color: #075985;">
                                        Submeteu um pedido de adoção para o {{ $pet->name }} em <strong>{{ $existingRequest->created_at->format('d/m/Y H:i') }}</strong>.
                                    </p>
                                    <p style="font-size: 0.9rem; margin-top: 0.5rem;">
                                        Estado atual: <span class="badge badge-warning">{{ ucfirst($existingRequest->status) }}</span>
                                    </p>
                                    <a href="{{ route('profile') }}" class="btn btn-outline btn-sm" style="margin-top: 0.75rem;">Ver nos Meus Pedidos</a>
                                </div>
                            @else
                                <h3>Candidatar-se à Adoção</h3>
                                <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 1.25rem;">
                                    Conte-nos um pouco sobre a sua rotina, habitação e experiência com animais para avaliarmos a candidatura.
                                </p>

                                <form action="{{ route('pets.adopt', $pet) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="message" class="form-label">A sua mensagem de candidatura *</label>
                                        <textarea name="message" id="message" rows="4" class="form-control" placeholder="Ex: Moro num apartamento espaçoso perto de um parque, trabalho remotamente e procuro um companheiro para passeios diários..." required>{{ old('message') }}</textarea>
                                        @error('message')
                                            <div class="form-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                                        Submeter Pedido de Adoção 🐾
                                    </button>
                                </form>
                            @endif
                        @else
                            <div style="text-align: center; padding: 1.5rem; background: var(--bg-muted); border-radius: var(--radius-md);">
                                <h4>Interessado em adotar o {{ $pet->name }}?</h4>
                                <p style="font-size: 0.92rem; color: var(--text-muted); margin: 0.5rem 0 1.25rem;">
                                    Para submeter um pedido de adoção oficial, precisa de ter sessão iniciada na sua conta.
                                </p>
                                <div style="display: flex; gap: 0.75rem; justify-content: center;">
                                    <a href="{{ route('login') }}" class="btn btn-primary">Iniciar Sessão</a>
                                    <a href="{{ route('register') }}" class="btn btn-outline">Criar Conta</a>
                                </div>
                            </div>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
