@extends('layouts.admin')

@section('title', __('messages.admin_dashboard_title'))

@section('content')
<div class="admin-header">
    <div>
        <h1>{{ __('messages.admin_dashboard_heading') }}</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">{{ __('messages.admin_dashboard_subtitle') }}</p>
    </div>
    <div>
        <a href="{{ route('admin.news.create') }}" class="btn btn-primary btn-sm">➕ {{ __('messages.admin_btn_new_news') }}</a>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.pets.create') }}" class="btn btn-accent btn-sm" style="margin-left: 0.5rem;">➕ {{ __('messages.admin_btn_new_pet') }}</a>
        @endif
    </div>
</div>

<!-- KPI Cards Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
    <div style="background: #fff; padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
        <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">{{ __('messages.admin_kpi_total_news') }}</div>
        <div style="font-size: 2.2rem; font-weight: 800; color: var(--primary); margin: 0.35rem 0;">{{ $stats['total_news'] ?? 0 }}</div>
        <div style="font-size: 0.8rem; color: var(--text-light);">{{ __('messages.admin_kpi_news_detail', ['published' => $stats['published_news'] ?? 0, 'draft' => $stats['draft_news'] ?? 0]) }}</div>
    </div>

    <div style="background: #fff; padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
        <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">{{ __('messages.admin_kpi_total_pets') }}</div>
        <div style="font-size: 2.2rem; font-weight: 800; color: var(--secondary); margin: 0.35rem 0;">{{ $stats['total_pets'] ?? 0 }}</div>
        <div style="font-size: 0.8rem; color: var(--text-light);">{{ __('messages.admin_kpi_pets_detail', ['available' => $stats['available_pets'] ?? 0]) }}</div>
    </div>

    <div style="background: #fff; padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
        <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">{{ __('messages.admin_kpi_total_requests') }}</div>
        <div style="font-size: 2.2rem; font-weight: 800; color: var(--accent); margin: 0.35rem 0;">{{ $stats['total_requests'] ?? 0 }}</div>
        <div style="font-size: 0.8rem; color: #b45309; font-weight: 600;">{{ __('messages.admin_kpi_requests_detail', ['pending' => $stats['pending_requests'] ?? 0]) }}</div>
    </div>

    @if(auth()->user()->isAdmin())
    <div style="background: #fff; padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
        <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">{{ __('messages.admin_kpi_total_users') }}</div>
        <div style="font-size: 2.2rem; font-weight: 800; color: var(--info); margin: 0.35rem 0;">{{ $stats['total_users'] ?? 0 }}</div>
        <div style="font-size: 0.8rem; color: var(--text-light);">{{ __('messages.admin_kpi_users_detail', ['active' => $stats['active_users'] ?? 0]) }}</div>
    </div>
    @endif
</div>

<!-- Two-column: Recent News & Recent Adoption Requests -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <!-- Recent News -->
    <div style="background: #fff; padding: 1.75rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3>{{ __('messages.admin_recent_news_title') }}</h3>
            <a href="{{ route('admin.news.index') }}" class="btn btn-outline btn-sm">{{ __('messages.admin_view_all') }}</a>
        </div>

        @if(isset($recentNews) && $recentNews->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.admin_th_title') }}</th>
                            <th>{{ __('messages.admin_th_category') }}</th>
                            <th>{{ __('messages.admin_th_status') }}</th>
                            <th>{{ __('messages.admin_th_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentNews as $article)
                            <tr>
                                <td style="font-weight: 600; max-width: 180px;">{{ Str::limit($article->title, 28) }}</td>
                                <td>{{ $article->category->name ?? __('messages.admin_general') }}</td>
                                <td>
                                    <span class="badge {{ $article->status == 'published' ? 'badge-success' : ($article->status == 'draft' ? 'badge-warning' : 'badge-danger') }}">
                                        {{ __('messages.admin_status_' . $article->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.news.edit', $article) }}" class="btn btn-outline btn-sm">{{ __('messages.admin_btn_edit') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="color: var(--text-muted); font-size: 0.9rem;">{{ __('messages.admin_no_recent_news') }}</p>
        @endif
    </div>

    <!-- Recent Adoption Requests -->
    <div style="background: #fff; padding: 1.75rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3>{{ __('messages.admin_recent_requests_title') }}</h3>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.pets.index') }}" class="btn btn-outline btn-sm">{{ __('messages.admin_btn_manage_pets') }}</a>
            @endif
        </div>

        @if(isset($recentRequests) && $recentRequests->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.admin_th_pet') }}</th>
                            <th>{{ __('messages.admin_th_applicant') }}</th>
                            <th>{{ __('messages.admin_th_status') }}</th>
                            <th>{{ __('messages.admin_th_date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentRequests as $request)
                            <tr>
                                <td style="font-weight: 600;">{{ $request->pet->name ?? 'N/A' }}</td>
                                <td>{{ $request->user->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $request->status == 'approved' ? 'badge-success' : ($request->status == 'pending' ? 'badge-warning' : 'badge-danger') }}">
                                        {{ __('messages.admin_status_' . $request->status) }}
                                    </span>
                                </td>
                                <td style="font-size: 0.8rem; color: var(--text-muted);">{{ $request->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="color: var(--text-muted); font-size: 0.9rem;">{{ __('messages.admin_no_recent_requests') }}</p>
        @endif
    </div>
</div>
@endsection
