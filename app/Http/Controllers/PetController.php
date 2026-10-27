<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\AdoptionRequest;
use App\Services\BreedService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetController extends Controller
{
    /**
     * Display paginated list of available pets with filters.
     */
    public function index(Request $request)
    {
        $query = Pet::available();

        // Filter by species
        if ($species = $request->input('species')) {
            $query->where('species', $species);
        }

        // Filter by size
        if ($size = $request->input('size')) {
            $query->where('size', $size);
        }

        // Filter by gender
        if ($gender = $request->input('gender')) {
            $query->where('gender', $gender);
        }

        // Search by name or breed
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('breed', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $pets = $query->orderByDesc('created_at')->paginate(9)->withQueryString();

        return view('public.pets.index', compact('pets', 'species', 'size', 'gender', 'search'));
    }

    /**
     * Display a single pet with breed info from external API.
     */
    public function show(Pet $pet, BreedService $breedService)
    {
        $breedInfo = null;
        $breedError = null;

        if ($pet->breed && in_array($pet->species, ['dog', 'cat'])) {
            try {
                $breedInfo = $breedService->getBreedInfo($pet->species, $pet->breed);
            } catch (\Exception $e) {
                $breedError = 'Breed information is temporarily unavailable.';
            }
        }

        // Check if user already has an adoption request for this pet
        $existingRequest = null;
        if (Auth::check()) {
            $existingRequest = AdoptionRequest::where('pet_id', $pet->id)
                ->where('user_id', Auth::id())
                ->first();
        }

        return view('public.pets.show', compact('pet', 'breedInfo', 'breedError', 'existingRequest'));
    }

    /**
     * Submit an adoption request (authenticated users only).
     */
    public function submitAdoption(Request $request, Pet $pet)
    {
        if (!$pet->isAvailable()) {
            return back()->with('error', 'This pet is no longer available for adoption.');
        }

        // Check for duplicate request
        $existing = AdoptionRequest::where('pet_id', $pet->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            return back()->with('error', 'You have already submitted an adoption request for this pet.');
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'min:20', 'max:1000'],
        ]);

        AdoptionRequest::create([
            'pet_id' => $pet->id,
            'user_id' => Auth::id(),
            'message' => $validated['message'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Your adoption request has been submitted! We will contact you soon.');
    }
}
