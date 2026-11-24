@extends('layouts.admin')

@section('title', __('messages.admin_pets_new_title') . ' — PawSafe CMS')

@section('content')
<div class="admin-header">
    <div>
        <h1>{{ __('messages.admin_pets_new_title') }}</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">{{ __('messages.admin_pets_new_desc') }}</p>
    </div>
    <a href="{{ route('admin.pets.index') }}" class="btn btn-outline btn-sm">{{ __('messages.admin_btn_back_list') }}</a>
</div>

<div style="background: #fff; padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm); max-width: 860px;">
    <form action="{{ route('admin.pets.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label for="name" class="form-label">{{ __('messages.admin_pets_name_label') }}</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control" required placeholder="Ex: Bobi">
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="species" class="form-label">{{ __('messages.admin_pets_species_label') }}</label>
                <select name="species" id="species" class="form-select" required>
                    <option value="dog" {{ old('species') == 'dog' ? 'selected' : '' }}>{{ __('messages.dogs') }} (TheDogAPI)</option>
                    <option value="cat" {{ old('species') == 'cat' ? 'selected' : '' }}>{{ __('messages.cats') }} (TheCatAPI)</option>
                </select>
                @error('species') <div class="form-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label for="breed" class="form-label">{{ __('messages.admin_pets_breed_label') }}</label>
                <input type="text" name="breed" id="breed" value="{{ old('breed') }}" class="form-control" required placeholder="Ex: Golden Retriever, Siamese, etc.">
                <small style="color: var(--text-muted); font-size: 0.8rem;">{{ __('messages.admin_pets_breed_help') }}</small>
                @error('breed') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="gender" class="form-label">{{ __('messages.admin_pets_gender_label') }}</label>
                <select name="gender" id="gender" class="form-select" required>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ __('messages.male') }}</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ __('messages.female') }}</option>
                </select>
                @error('gender') <div class="form-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label for="size" class="form-label">{{ __('messages.admin_pets_size_label') }}</label>
                <select name="size" id="size" class="form-select" required>
                    <option value="small" {{ old('size') == 'small' ? 'selected' : '' }}>{{ __('messages.small') }}</option>
                    <option value="medium" {{ old('size', 'medium') == 'medium' ? 'selected' : '' }}>{{ __('messages.medium') }}</option>
                    <option value="large" {{ old('size') == 'large' ? 'selected' : '' }}>{{ __('messages.large') }}</option>
                </select>
                @error('size') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="age_years" class="form-label">{{ __('messages.admin_pets_age_years_label') }} *</label>
                <input type="number" name="age_years" id="age_years" value="{{ old('age_years', 1) }}" min="0" max="30" class="form-control" required>
                @error('age_years') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="age_months" class="form-label">{{ __('messages.admin_pets_age_months_label') }} *</label>
                <input type="number" name="age_months" id="age_months" value="{{ old('age_months', 0) }}" min="0" max="11" class="form-control" required>
                @error('age_months') <div class="form-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="status" class="form-label">{{ __('messages.admin_pets_status_label') }}</label>
            <select name="status" id="status" class="form-select" required>
                <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>{{ __('messages.admin_status_available') }}</option>
                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>{{ __('messages.admin_status_pending') }}</option>
                <option value="adopted" {{ old('status') == 'adopted' ? 'selected' : '' }}>{{ __('messages.admin_status_adopted') }}</option>
            </select>
            @error('status') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="description" class="form-label">{{ __('messages.admin_pets_desc_label') }} *</label>
            <textarea name="description" id="description" rows="4" class="form-control" required placeholder="Personalidade, convivência com outros animais...">{{ old('description') }}</textarea>
            @error('description') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="image" class="form-label">{{ __('messages.admin_pets_image_label') }}</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
            @error('image') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary btn-lg">{{ __('messages.admin_pets_btn_save') }}</button>
            <a href="{{ route('admin.pets.index') }}" class="btn btn-outline btn-lg">{{ __('messages.admin_btn_cancel') }}</a>
        </div>
    </form>
</div>
@endsection
