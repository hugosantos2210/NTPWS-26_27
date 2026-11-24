@extends('layouts.admin')

@section('title', __('messages.admin_news_new_title') . ' — PawSafe CMS')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
@endpush

@section('content')
<div class="admin-header">
    <div>
        <h1>{{ __('messages.admin_news_new_title') }}</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">{{ __('messages.admin_news_new_desc') }}</p>
    </div>
    <a href="{{ route('admin.news.index') }}" class="btn btn-outline btn-sm">{{ __('messages.admin_btn_back_list') }}</a>
</div>

<div style="background: #fff; padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm); max-width: 860px;">
    <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="title" class="form-label">{{ __('messages.admin_news_form_title') }}</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" class="form-control" required placeholder="Ex: Campanha de Primavera">
            @error('title') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label for="category_id" class="form-label">{{ __('messages.admin_news_form_category') }}</label>
                <select name="category_id" id="category_id" class="form-select" required>
                    <option value="">{{ __('messages.admin_news_form_select_cat') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="status" class="form-label">{{ __('messages.admin_news_form_status') }}</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>{{ __('messages.admin_news_status_draft_opt') }}</option>
                    <option value="published" {{ old('status', 'published') == 'published' ? 'selected' : '' }}>{{ __('messages.admin_news_status_published_opt') }}</option>
                    <option value="withdrawn" {{ old('status') == 'withdrawn' ? 'selected' : '' }}>{{ __('messages.admin_news_status_withdrawn_opt') }}</option>
                </select>
                @error('status') <div class="form-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="summary" class="form-label">{{ __('messages.admin_news_form_summary') }}</label>
            <textarea name="summary" id="summary" rows="2" class="form-control" required placeholder="Breve resumo...">{{ old('summary') }}</textarea>
            @error('summary') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="content" class="form-label">{{ __('messages.admin_news_form_content') }}</label>
            <div id="quill-editor" style="min-height: 300px; background: white;">{!! old('content') !!}</div>
            <input type="hidden" name="content" id="content">
            @error('content') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="image" class="form-label">{{ __('messages.admin_news_form_image') }}</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
            @error('image') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary btn-lg">{{ __('messages.admin_news_btn_publish') }}</button>
            <a href="{{ route('admin.news.index') }}" class="btn btn-outline btn-lg">{{ __('messages.admin_btn_cancel') }}</a>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
<script>
const quill = new Quill('#quill-editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ 'header': [1, 2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['blockquote', 'code-block'],
            ['link', 'image'],
            ['clean']
        ]
    }
});
document.querySelector('form').addEventListener('submit', function() {
    document.getElementById('content').value = quill.root.innerHTML;
});
</script>
@endpush
@endsection
