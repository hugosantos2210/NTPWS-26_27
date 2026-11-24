@extends('layouts.admin')

@section('title', __('messages.admin_users_title') . ' — PawSafe CMS')

@section('content')
<div class="admin-header">
    <div>
        <h1>{{ __('messages.admin_users_title') }}</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">{{ __('messages.admin_users_desc') }}</p>
    </div>
</div>

<!-- Filters -->
<form action="{{ route('admin.users.index') }}" method="GET" class="filter-bar">
    <div style="flex: 1; min-width: 200px;">
        <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('messages.admin_users_search_placeholder') }}" class="form-control">
    </div>

    <div style="min-width: 160px;">
        <select name="role" class="form-select">
            <option value="">{{ __('messages.admin_all_roles') }}</option>
            @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ $roleFilter == $role->name ? 'selected' : '' }}>
                    {{ ucfirst($role->name) }}
                </option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn btn-primary btn-sm">{{ __('messages.admin_btn_filter') }}</button>
    @if($search || $roleFilter)
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm">{{ __('messages.admin_btn_clear') }}</a>
    @endif
</form>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('messages.admin_th_name') }}</th>
                <th>{{ __('messages.admin_th_email') }}</th>
                <th>{{ __('messages.admin_th_phone') }}</th>
                <th>{{ __('messages.admin_th_role') }}</th>
                <th>{{ __('messages.admin_th_status') }}</th>
                <th>{{ __('messages.admin_th_date') }}</th>
                <th style="text-align: right;">{{ __('messages.admin_th_actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td style="font-weight: 700;">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $user->isAdmin() ? 'badge-primary' : ($user->isEditor() ? 'badge-accent' : 'badge-muted') }}">
                            {{ $user->role->name ?? 'user' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }}">
                            {{ $user->is_active ? __('messages.admin_status_active') : __('messages.admin_status_inactive') }}
                        </span>
                    </td>
                    <td style="font-size: 0.85rem; color: var(--text-muted);">
                        {{ $user->created_at->format('d/m/Y') }}
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline btn-sm">{{ __('messages.admin_btn_edit') }}</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        {{ __('messages.admin_users_empty') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination-container">
    {{ $users->withQueryString()->links() }}
</div>
@endsection
