<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\AdoptionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AdminPetController extends Controller
{
    /**
     * List all pets.
     */
    public function index(Request $request)
    {
        $query = Pet::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('breed', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($species = $request->input('species')) {
            $query->where('species', $species);
        }

        $pets = $query->withCount('adoptionRequests')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.pets.index', compact('pets', 'search', 'status', 'species'));
    }

    /**
     * Show form to create a new pet.
     */
    public function create()
    {
        return view('admin.pets.create');
    }

    /**
     * Store a new pet.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'species' => ['required', 'in:dog,cat,other'],
            'breed' => ['nullable', 'string', 'max:255'],
            'age_years' => ['required', 'integer', 'min:0', 'max:30'],
            'age_months' => ['required', 'integer', 'min:0', 'max:11'],
            'gender' => ['required', 'in:male,female,unknown'],
            'size' => ['required', 'in:small,medium,large,extra_large'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'status' => ['required', 'in:available,adopted,pending'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('pets', 'public');
        }
        unset($validated['image']);

        Pet::create($validated);

        return redirect()->route('admin.pets.index')
            ->with('success', 'Pet added successfully.');
    }

    /**
     * Show form to edit a pet.
     */
    public function edit(Pet $pet)
    {
        $adoptionRequests = $pet->adoptionRequests()->with('user')->get();
        return view('admin.pets.edit', compact('pet', 'adoptionRequests'));
    }

    /**
     * Update a pet.
     */
    public function update(Request $request, Pet $pet)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'species' => ['required', 'in:dog,cat,other'],
            'breed' => ['nullable', 'string', 'max:255'],
            'age_years' => ['required', 'integer', 'min:0', 'max:30'],
            'age_months' => ['required', 'integer', 'min:0', 'max:11'],
            'gender' => ['required', 'in:male,female,unknown'],
            'size' => ['required', 'in:small,medium,large,extra_large'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'status' => ['required', 'in:available,adopted,pending'],
            'remove_image' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('remove_image') && $pet->image_path) {
            Storage::disk('public')->delete($pet->image_path);
            $pet->image_path = null;
        }

        if ($request->hasFile('image')) {
            if ($pet->image_path) {
                Storage::disk('public')->delete($pet->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('pets', 'public');
        }
        unset($validated['image'], $validated['remove_image']);

        $pet->update($validated);

        return redirect()->route('admin.pets.index')
            ->with('success', 'Pet updated successfully.');
    }

    /**
     * Delete a pet.
     */
    public function destroy(Pet $pet)
    {
        if ($pet->image_path) {
            Storage::disk('public')->delete($pet->image_path);
        }

        $pet->delete();

        return redirect()->route('admin.pets.index')
            ->with('success', 'Pet removed successfully.');
    }

    /**
     * Update adoption request status.
     */
    public function updateAdoptionRequest(Request $request, AdoptionRequest $adoptionRequest)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'admin_notes' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($adoptionRequest, $validated) {
            $adoptionRequest->update($validated);

            // If approved, mark pet as pending/adopted and reject other requests
            if ($validated['status'] === 'approved') {
                $adoptionRequest->pet->update(['status' => 'pending']);

                // Reject other pending requests for the same pet
                AdoptionRequest::where('pet_id', $adoptionRequest->pet_id)
                    ->where('id', '!=', $adoptionRequest->id)
                    ->where('status', 'pending')
                    ->update([
                        'status' => 'rejected',
                        'admin_notes' => 'Another adoption request was approved for this pet.',
                    ]);
            }
        });

        return back()->with('success', 'Adoption request updated successfully.');
    }
}
