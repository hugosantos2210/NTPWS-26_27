@extends('layouts.app')

@section('title', __('messages.pets') . ' — PawSafe')

@section('content')
<div class="container" style="padding: 3.5rem 1.5rem;">
    <div style="margin-bottom: 2rem;">
        <span class="badge badge-primary">{{ __('messages.pets_page_badge') }}</span>
        <h1 style="margin-top: 0.5rem;">{{ __('messages.meet_pets_title') }}</h1>
        <p style="color: var(--text-muted); font-size: 1.05rem;">{{ __('messages.pets_page_subtitle') }}</p>
    </div>

    <!-- Filter Bar -->
    <form action="{{ route('pets.index') }}" method="GET" class="filter-bar">
        <div style="flex: 1; min-width: 200px;">
            <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('messages.search_placeholder') }}" class="form-control">
        </div>

        <div style="min-width: 140px;">
            <select name="species" class="form-select">
                <option value="">{{ __('messages.all_species') }}</option>
                <option value="dog" {{ $species == 'dog' ? 'selected' : '' }}>{{ __('messages.dogs') }} 🐶</option>
                <option value="cat" {{ $species == 'cat' ? 'selected' : '' }}>{{ __('messages.cats') }} 🐱</option>
            </select>
        </div>

        <div style="min-width: 130px;">
            <select name="size" class="form-select">
                <option value="">{{ __('messages.all_sizes') }}</option>
                <option value="small" {{ $size == 'small' ? 'selected' : '' }}>{{ __('messages.small') }}</option>
                <option value="medium" {{ $size == 'medium' ? 'selected' : '' }}>{{ __('messages.medium') }}</option>
                <option value="large" {{ $size == 'large' ? 'selected' : '' }}>{{ __('messages.large') }}</option>
            </select>
        </div>

        <div style="min-width: 130px;">
            <select name="gender" class="form-select">
                <option value="">{{ __('messages.all_genders') }}</option>
                <option value="male" {{ $gender == 'male' ? 'selected' : '' }}>{{ __('messages.male') }}</option>
                <option value="female" {{ $gender == 'female' ? 'selected' : '' }}>{{ __('messages.female') }}</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">{{ __('messages.filter_btn') }}</button>
        @if($search || $species || $size || $gender)
            <a href="{{ route('pets.index') }}" class="btn btn-outline">{{ __('messages.clear_filters') }}</a>
        @endif
    </form>

    <!-- Pets Grid -->
    @if($pets->count() > 0)
        <div class="card-grid">
            @foreach($pets as $pet)
                <div class="card">
                    <div class="card-img-wrapper">
                        <img src="{{ $pet->image_url }}" alt="{{ $pet->name }}" class="card-img" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1543466835-00a7907e9de1?auto=format&fit=crop&w=600&q=80';">
                        <div class="card-badge">
                            <span class="badge {{ $pet->status == 'available' ? 'badge-success' : 'badge-warning' }}">
                                {{ $pet->status == 'available' ? __('messages.available') : ucfirst($pet->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-meta">
                            <span>{{ $pet->species == 'dog' ? '🐶 ' . __('messages.dogs') : '🐱 ' . __('messages.cats') }}</span>
                            <span>•</span>
                            <span>{{ $pet->gender == 'male' ? __('messages.male') : __('messages.female') }}</span>
                            <span>•</span>
                            <span>{{ $pet->age_years }} {{ __('messages.years') }}</span>
                        </div>
                        <h3 class="card-title"><a href="{{ route('pets.show', $pet) }}">{{ $pet->name }}</a></h3>
                        <p class="card-text">{{ Str::limit($pet->description, 100) }}</p>
                    </div>
                    <div class="card-footer">
                        <span style="font-size: 0.85rem; font-weight: 600; color: var(--primary);">
                            {{ $pet->breed }}
                        </span>
                        <a href="{{ route('pets.show', $pet) }}" class="btn btn-primary btn-sm">{{ __('messages.meet_pet') }}</a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pagination-container">
            {{ $pets->withQueryString()->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 4rem 1rem; background: #fff; border-radius: var(--radius-lg); border: 1px solid var(--border);">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🐾</div>
            <h3>{{ __('messages.no_pets_match') }}</h3>
            <p style="color: var(--text-muted); margin-top: 0.5rem;">{{ __('messages.adjust_search') }}</p>
            <a href="{{ route('pets.index') }}" class="btn btn-primary" style="margin-top: 1rem;">{{ __('messages.view_all_pets') }}</a>
        </div>
    @endif
</div>
@endsection
