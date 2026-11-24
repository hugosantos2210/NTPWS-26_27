@extends('layouts.admin')

@section('title', __('messages.admin_pets_title') . ' — PawSafe CMS')

@section('content')
<div class="admin-header">
    <div>
        <h1>{{ __('messages.admin_pets_title') }}</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">{{ __('messages.admin_pets_desc') }}</p>
    </div>
    <a href="{{ route('admin.pets.create') }}" class="btn btn-primary">➕ {{ __('messages.admin_btn_new_pet') }}</a>
</div>

<!-- Filters -->
<form action="{{ route('admin.pets.index') }}" method="GET" class="filter-bar">
    <div style="flex: 1; min-width: 200px;">
        <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('messages.admin_pets_search_placeholder') }}" class="form-control">
    </div>

    <div style="min-width: 140px;">
        <select name="species" class="form-select">
            <option value="">{{ __('messages.admin_all_species') }}</option>
            <option value="dog" {{ $species == 'dog' ? 'selected' : '' }}>{{ __('messages.dogs') }}</option>
            <option value="cat" {{ $species == 'cat' ? 'selected' : '' }}>{{ __('messages.cats') }}</option>
        </select>
    </div>

    <div style="min-width: 140px;">
        <select name="status" class="form-select">
            <option value="">{{ __('messages.admin_all_statuses') }}</option>
            <option value="available" {{ $status == 'available' ? 'selected' : '' }}>{{ __('messages.admin_status_available') }}</option>
            <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>{{ __('messages.admin_status_pending') }}</option>
            <option value="adopted" {{ $status == 'adopted' ? 'selected' : '' }}>{{ __('messages.admin_status_adopted') }}</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary btn-sm">{{ __('messages.admin_btn_filter') }}</button>
    @if($search || $species || $status)
        <a href="{{ route('admin.pets.index') }}" class="btn btn-outline btn-sm">{{ __('messages.admin_btn_clear') }}</a>
    @endif
</form>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('messages.admin_th_pet') }}</th>
                <th>{{ __('messages.admin_th_species') }}</th>
                <th>{{ __('messages.admin_th_breed') }}</th>
                <th>{{ __('messages.admin_th_age_size') }}</th>
                <th>{{ __('messages.admin_th_status') }}</th>
                <th>{{ __('messages.admin_th_adoptions') }}</th>
                <th style="text-align: right;">{{ __('messages.admin_th_actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pets as $pet)
                <tr>
                    <td style="font-weight: 700;">
                        <a href="{{ route('pets.show', $pet) }}" target="_blank">{{ $pet->name }} ↗</a>
                    </td>
                    <td>{{ $pet->species == 'dog' ? '🐶 ' . __('messages.dogs') : '🐱 ' . __('messages.cats') }}</td>
                    <td>{{ $pet->breed }}</td>
                    <td>{{ $pet->age_years }}{{ __('messages.years')[0] }} • {{ ucfirst($pet->size) }}</td>
                    <td>
                        <span class="badge {{ $pet->status == 'available' ? 'badge-success' : ($pet->status == 'pending' ? 'badge-warning' : 'badge-primary') }}">
                            {{ __('messages.admin_status_' . $pet->status) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-muted">{{ $pet->adoption_requests_count ?? $pet->adoptionRequests()->count() }}</span>
                    </td>
                    <td style="text-align: right;">
                        <div style="display: inline-flex; gap: 0.35rem;">
                            <a href="{{ route('admin.pets.edit', $pet) }}" class="btn btn-outline btn-sm">{{ __('messages.admin_btn_edit') }}</a>
                            <form action="{{ route('admin.pets.destroy', $pet) }}" method="POST" onsubmit="return confirm('{{ __('messages.admin_confirm_delete') }}');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">{{ __('messages.admin_btn_delete') }}</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        {{ __('messages.admin_pets_empty') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination-container">
    {{ $pets->withQueryString()->links() }}
</div>
@endsection
