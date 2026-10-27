<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminNewsController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminPetController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');

// Public News
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');

// Public Pets
Route::get('/pets', [PetController::class, 'index'])->name('pets.index');
Route::get('/pets/{pet}', [PetController::class, 'show'])->name('pets.show');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');

    // Adoption Request
    Route::post('/pets/{pet}/adopt', [PetController::class, 'submitAdoption'])->name('pets.adopt');
});

/*
|--------------------------------------------------------------------------
| Admin & Editor Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth', 'role:admin,editor'])->name('admin.')->group(function () {
    // Dashboard (admin only for full view, editors get limited view)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // News Management (editors & admins)
    Route::resource('news', AdminNewsController::class)->except(['show']);

    // Categories (admin only)
    Route::resource('categories', AdminCategoryController::class)
        ->except(['show'])
        ->middleware('role:admin');

    // Users (admin only)
    Route::resource('users', AdminUserController::class)
        ->only(['index', 'edit', 'update'])
        ->middleware('role:admin');

    // Pets (admin only)
    Route::resource('pets', AdminPetController::class)
        ->except(['show'])
        ->middleware('role:admin');

    // Adoption Requests (admin only)
    Route::patch('/adoption-requests/{adoptionRequest}', [AdminPetController::class, 'updateAdoptionRequest'])
        ->name('adoption-requests.update')
        ->middleware('role:admin');
});
