<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiNewsController;

/*
|--------------------------------------------------------------------------
| API Routes — /api/v1
|--------------------------------------------------------------------------
|
| Public endpoints: GET news, GET news/{id}, GET categories
| Protected endpoints: POST/PATCH/DELETE news (require auth via Sanctum or session)
|
*/

Route::prefix('v1')->group(function () {
    // Public endpoints
    Route::get('/news', [ApiNewsController::class, 'index']);
    Route::get('/news/{id}', [ApiNewsController::class, 'show']);
    Route::get('/categories', [ApiNewsController::class, 'categories']);
    Route::get('/schema', function () {
        $path = public_path('schemas/news-schema.json');
        return response(file_get_contents($path), 200, [
            'Content-Type' => 'application/schema+json',
        ]);
    });

    // Protected endpoints (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/news', [ApiNewsController::class, 'store'])->middleware('role:editor,admin');
        Route::patch('/news/{id}', [ApiNewsController::class, 'update'])->middleware('role:editor,admin');
        Route::delete('/news/{id}', [ApiNewsController::class, 'destroy'])->middleware('role:editor,admin');
    });
});
