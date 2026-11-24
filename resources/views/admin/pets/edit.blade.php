@extends('layouts.admin')

@section('title', __('messages.admin_pets_edit_title', ['name' => $pet->name]) . ' — PawSafe CMS')

@section('content')
<div class="admin-header">
    <div>
        <h1>{{ __('messages.admin_pets_edit_title', ['name' => $pet->name]) }}</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">{{ __('messages.admin_pets_edit_desc') }}</p>
    </div>
    <a href="{{ route('admin.pets.index') }}" class="btn btn-outline btn-sm">{{ __('messages.admin_btn_back_list') }}</a>
</div>

<div style="display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 2.5rem; align-items: flex-start;">
    <!-- Left: Pet Update Form -->
    <div style="background: #fff; padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
        <h3 style="margin-bottom: 1.5rem;">{{ __('messages.admin_pets_cadastral_data') }}</h3>

        <form action="{{ route('admin.pets.update', $pet) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label for="name" class="form-label">{{ __('messages.admin_pets_name_label') }}</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $pet->name) }}" class="form-control" required>
                    @error('name') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="species" class="form-label">{{ __('messages.admin_pets_species_label') }}</label>
                    <select name="species" id="species" class="form-select" required>
                        <option value="dog" {{ old('species', $pet->species) == 'dog' ? 'selected' : '' }}>{{ __('messages.dogs') }} (TheDogAPI)</option>
                        <option value="cat" {{ old('species', $pet->species) == 'cat' ? 'selected' : '' }}>{{ __('messages.cats') }} (TheCatAPI)</option>
                    </select>
                    @error('species') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label for="breed" class="form-label">{{ __('messages.admin_pets_breed_label') }}</label>
                    <input type="text" name="breed" id="breed" value="{{ old('breed', $pet->breed) }}" class="form-control" required>
                    @error('breed') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="gender" class="form-label">{{ __('messages.admin_pets_gender_label') }}</label>
                    <select name="gender" id="gender" class="form-select" required>
                        <option value="male" {{ old('gender', $pet->gender) == 'male' ? 'selected' : '' }}>{{ __('messages.male') }}</option>
                        <option value="female" {{ old('gender', $pet->gender) == 'female' ? 'selected' : '' }}>{{ __('messages.female') }}</option>
                    </select>
                    @error('gender') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label for="size" class="form-label">{{ __('messages.admin_pets_size_label') }}</label>
                    <select name="size" id="size" class="form-select" required>
                        <option value="small" {{ old('size', $pet->size) == 'small' ? 'selected' : '' }}>{{ __('messages.small') }}</option>
                        <option value="medium" {{ old('size', $pet->size) == 'medium' ? 'selected' : '' }}>{{ __('messages.medium') }}</option>
                        <option value="large" {{ old('size', $pet->size) == 'large' ? 'selected' : '' }}>{{ __('messages.large') }}</option>
                    </select>
                    @error('size') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="age_years" class="form-label">{{ __('messages.admin_pets_age_years_label') }} *</label>
                    <input type="number" name="age_years" id="age_years" value="{{ old('age_years', $pet->age_years) }}" min="0" max="30" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="age_months" class="form-label">{{ __('messages.admin_pets_age_months_label') }} *</label>
                    <input type="number" name="age_months" id="age_months" value="{{ old('age_months', $pet->age_months) }}" min="0" max="11" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">{{ __('messages.admin_pets_status_label') }}</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="available" {{ old('status', $pet->status) == 'available' ? 'selected' : '' }}>{{ __('messages.admin_status_available') }}</option>
                    <option value="pending" {{ old('status', $pet->status) == 'pending' ? 'selected' : '' }}>{{ __('messages.admin_status_pending') }}</option>
                    <option value="adopted" {{ old('status', $pet->status) == 'adopted' ? 'selected' : '' }}>{{ __('messages.admin_status_adopted') }}</option>
                </select>
                @error('status') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="description" class="form-label">{{ __('messages.admin_pets_desc_label') }} *</label>
                <textarea name="description" id="description" rows="4" class="form-control" required>{{ old('description', $pet->description) }}</textarea>
                @error('description') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="image" class="form-label">{{ __('messages.admin_pets_replace_image') }}</label>
                @if($pet->image_url)
                    <div style="margin-bottom: 0.75rem;">
                        <img src="{{ $pet->image_url }}" alt="Foto atual" style="height: 100px; border-radius: var(--radius-sm); object-fit: cover;">
                    </div>
                @endif
                <input type="file" name="image" id="image" class="form-control" accept="image/*">
                @error('image') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary btn-lg">{{ __('messages.admin_pets_btn_update') }}</button>
            </div>
        </form>
    </div>

    <!-- Right: Adoption Requests Management -->
    <div>
        <div style="background: #fff; padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3>{{ __('messages.admin_recent_requests_title') }}</h3>
                <span class="badge badge-primary">{{ $adoptionRequests->count() }}</span>
            </div>

            @forelse($adoptionRequests as $request)
                <div style="padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border); background: var(--bg-muted); margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <strong style="font-size: 1rem;">{{ $request->user->name }}</strong>
                        <span class="badge {{ $request->status == 'approved' ? 'badge-success' : ($request->status == 'pending' ? 'badge-warning' : 'badge-danger') }}">
                            {{ __('messages.admin_status_' . $request->status) }}
                        </span>
                    </div>

                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                        ✉️ {{ $request->user->email }} | 📞 {{ $request->user->phone ?? '—' }}<br>
                        📅 {{ $request->created_at->format('d/m/Y H:i') }}
                    </div>

                    <div style="padding: 0.75rem; background: #fff; border-radius: var(--radius-sm); font-size: 0.9rem; margin-bottom: 1rem;">
                        <em>"{{ $request->message }}"</em>
                    </div>

                    <!-- Moderate Form -->
                    <form action="{{ route('admin.adoption-requests.update', $request) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="form-group" style="margin-bottom: 0.75rem;">
                            <label class="form-label" style="font-size: 0.82rem;">{{ __('messages.admin_pets_admin_notes') }}</label>
                            <input type="text" name="admin_notes" value="{{ old('admin_notes', $request->admin_notes) }}" class="form-control" style="font-size: 0.85rem; padding: 0.4rem 0.65rem;" placeholder="{{ __('messages.admin_pets_decision_notes_placeholder') }}">
                        </div>

                        <div style="display: flex; gap: 0.5rem;">
                            @if($request->status !== 'approved')
                                <button type="submit" name="status" value="approved" class="btn btn-primary btn-sm" onclick="return confirm('{{ __('messages.admin_confirm_delete') }}');">
                                    ✓ {{ __('messages.admin_pets_btn_approve') }}
                                </button>
                            @endif

                            @if($request->status !== 'rejected')
                                <button type="submit" name="status" value="rejected" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('messages.admin_confirm_delete') }}');">
                                    ✗ {{ __('messages.admin_pets_btn_reject') }}
                                </button>
                            @endif

                            @if($request->status !== 'pending')
                                <button type="submit" name="status" value="pending" class="btn btn-outline btn-sm">
                                    ↺ {{ __('messages.admin_status_pending') }}
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            @empty
                <div style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted);">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">📭</div>
                    <p>{{ __('messages.admin_pets_requests_empty') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
