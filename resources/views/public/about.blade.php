@extends('layouts.app')

@section('title', __('messages.about_page_title') . ' — PawSafe')

@section('content')
<div class="container" style="padding: 4rem 1.5rem;">
    <div style="max-width: 860px; margin: 0 auto;">
        <span class="badge badge-primary">{{ __('messages.about_badge') }}</span>
        <h1 style="margin: 0.75rem 0 1.5rem;">{{ __('messages.about_mission_title') }}</h1>
        <p style="font-size: 1.15rem; color: var(--text-muted); margin-bottom: 2.5rem;">
            {!! __('messages.about_intro') !!}
        </p>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 3rem;">
            <div style="background: #fff; padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">🎯</div>
                <h3>{{ __('messages.about_objectives_title') }}</h3>
                <ul style="margin-top: 1rem; padding-left: 1.25rem; color: var(--text-muted); font-size: 0.95rem; line-height: 1.8;">
                    <li>{{ __('messages.about_obj_1') }}</li>
                    <li>{{ __('messages.about_obj_2') }}</li>
                    <li>{{ __('messages.about_obj_3') }}</li>
                    <li>{{ __('messages.about_obj_4') }}</li>
                </ul>
            </div>

            <div style="background: #fff; padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">⚡</div>
                <h3>{{ __('messages.about_arch_title') }}</h3>
                <ul style="margin-top: 1rem; padding-left: 1.25rem; color: var(--text-muted); font-size: 0.95rem; line-height: 1.8;">
                    <li>{!! __('messages.about_arch_backend') !!}</li>
                    <li>{!! __('messages.about_arch_database') !!}</li>
                    <li>{!! __('messages.about_arch_security') !!}</li>
                    <li>{!! __('messages.about_arch_frontend') !!}</li>
                </ul>
            </div>
        </div>

        <div style="background: #fff; padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm); margin-bottom: 3rem;">
            <h3>{{ __('messages.about_api_title') }}</h3>
            <p style="margin-top: 0.75rem; color: var(--text-muted);">
                {{ __('messages.about_api_subtitle') }}
            </p>
            <div style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                <div style="padding: 1rem; border-radius: var(--radius-md); background: var(--bg-muted); border-left: 4px solid var(--primary);">
                    <strong>{{ __('messages.about_api_own_title') }}</strong>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin: 0.25rem 0 0.5rem;">
                        {!! __('messages.about_api_own_desc', ['schema_link' => '<a href="' . url('/api/v1/schema') . '" target="_blank">' . __('messages.json_schema') . '</a>']) !!}
                    </p>
                    <a href="{{ url('/api/v1/news') }}" target="_blank" class="btn btn-outline btn-sm">{{ __('messages.about_api_own_btn') }}</a>
                </div>

                <div style="padding: 1rem; border-radius: var(--radius-md); background: var(--bg-muted); border-left: 4px solid var(--accent);">
                    <strong>{{ __('messages.about_api_ext_title') }}</strong>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin: 0.25rem 0 0.5rem;">
                        {!! __('messages.about_api_ext_desc', ['table' => '<code>breed_cache</code>']) !!}
                    </p>
                    <a href="{{ route('pets.index') }}" class="btn btn-outline btn-sm">{{ __('messages.about_api_ext_btn') }}</a>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="{{ route('pets.index') }}" class="btn btn-primary btn-lg">{{ __('messages.about_explore_btn') }}</a>
        </div>
    </div>
</div>
@endsection
