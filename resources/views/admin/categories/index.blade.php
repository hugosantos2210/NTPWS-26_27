@extends('layouts.admin')

@section('title', __('messages.admin_cat_title') . ' — PawSafe CMS')

@section('content')
<div class="admin-header">
    <div>
        <h1>{{ __('messages.admin_cat_title') }}</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">{{ __('messages.admin_cat_desc') }}</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">➕ {{ __('messages.admin_btn_new_category') }}</a>
</div>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('messages.admin_cat_name_label') }}</th>
                <th>{{ __('messages.admin_th_slug') }}</th>
                <th>{{ __('messages.admin_th_description') }}</th>
                <th>{{ __('messages.admin_th_articles_count') }}</th>
                <th style="text-align: right;">{{ __('messages.admin_th_actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
                <tr>
                    <td style="font-weight: 700; color: var(--primary);">{{ $category->name }}</td>
                    <td><code>{{ $category->slug }}</code></td>
                    <td style="color: var(--text-muted); font-size: 0.9rem;">{{ $category->description ?? '—' }}</td>
                    <td>
                        <span class="badge badge-primary">{{ __('messages.admin_cat_articles_badge', ['count' => $category->news_count ?? $category->news()->count()]) }}</span>
                    </td>
                    <td style="text-align: right;">
                        <div style="display: inline-flex; gap: 0.35rem;">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-outline btn-sm">{{ __('messages.admin_btn_edit') }}</a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('{{ __('messages.admin_confirm_delete') }}');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">{{ __('messages.admin_btn_delete') }}</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        {{ __('messages.admin_cat_empty') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 1.5rem;">
    {{ $categories->links() }}
</div>
@endsection
