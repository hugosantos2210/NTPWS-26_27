@extends('layouts.admin')

@section('title', __('messages.admin_news_title') . ' — PawSafe CMS')

@section('content')
<div class="admin-header">
    <div>
        <h1>{{ __('messages.admin_news_title') }}</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">{{ __('messages.admin_news_desc') }}</p>
    </div>
    <a href="{{ route('admin.news.create') }}" class="btn btn-primary">➕ {{ __('messages.admin_btn_new_news') }}</a>
</div>

<!-- Filters -->
<form action="{{ route('admin.news.index') }}" method="GET" class="filter-bar">
    <div style="flex: 1; min-width: 200px;">
        <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('messages.admin_news_search_placeholder') }}" class="form-control">
    </div>

    <div style="min-width: 160px;">
        <select name="status" class="form-select">
            <option value="">{{ __('messages.admin_all_statuses') }}</option>
            <option value="published" {{ $status == 'published' ? 'selected' : '' }}>{{ __('messages.admin_status_published') }}</option>
            <option value="draft" {{ $status == 'draft' ? 'selected' : '' }}>{{ __('messages.admin_status_draft') }}</option>
            <option value="withdrawn" {{ $status == 'withdrawn' ? 'selected' : '' }}>{{ __('messages.admin_status_withdrawn') }}</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary btn-sm">{{ __('messages.admin_btn_filter') }}</button>
    @if($search || $status)
        <a href="{{ route('admin.news.index') }}" class="btn btn-outline btn-sm">{{ __('messages.admin_btn_clear') }}</a>
    @endif
</form>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('messages.admin_th_title') }}</th>
                <th>{{ __('messages.admin_th_category') }}</th>
                <th>{{ __('messages.admin_th_author') }}</th>
                <th>{{ __('messages.admin_th_status') }}</th>
                <th>{{ __('messages.admin_th_date') }}</th>
                <th style="text-align: right;">{{ __('messages.admin_th_actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($news as $article)
                <tr>
                    <td style="font-weight: 600;">
                        <a href="{{ route('news.show', $article->slug) }}" target="_blank">{{ $article->title }} ↗</a>
                    </td>
                    <td>{{ $article->category->name ?? __('messages.admin_general') }}</td>
                    <td>{{ $article->author->name ?? 'N/A' }}</td>
                    <td>
                        <span class="badge {{ $article->status == 'published' ? 'badge-success' : ($article->status == 'draft' ? 'badge-warning' : 'badge-danger') }}">
                            {{ __('messages.admin_status_' . $article->status) }}
                        </span>
                    </td>
                    <td style="font-size: 0.85rem; color: var(--text-muted);">
                        {{ $article->created_at->format('d/m/Y') }}
                    </td>
                    <td style="text-align: right;">
                        <div style="display: inline-flex; gap: 0.35rem;">
                            <a href="{{ route('admin.news.edit', $article) }}" class="btn btn-outline btn-sm">{{ __('messages.admin_btn_edit') }}</a>
                            <form action="{{ route('admin.news.destroy', $article) }}" method="POST" onsubmit="return confirm('{{ __('messages.admin_confirm_delete') }}');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">{{ __('messages.admin_btn_delete') }}</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        {{ __('messages.admin_news_empty') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination-container">
    {{ $news->withQueryString()->links() }}
</div>
@endsection
