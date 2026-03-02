<?php

use App\Http\Controllers\SurveyResponseController;
use App\Http\Middleware\AdminApiKey;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Co-Cooking API Routes
|--------------------------------------------------------------------------
|
| PUBLIC  → POST /api/responses (soumission du formulaire)
| ADMIN   → tout le reste (protégé par clé API dans le header X-Admin-Key)
|
*/

// -----------------------------------------------------------------------
// PUBLIC : soumission du formulaire (accessible à tous)
// -----------------------------------------------------------------------
Route::post('responses', [SurveyResponseController::class, 'store'])
    ->name('responses.store');

// -----------------------------------------------------------------------
// ADMIN : lecture, export, stats (protégé par X-Admin-Key)
// ⚠️  La route export/csv DOIT être déclarée AVANT responses/{id}
//     sinon Laravel interpréterait "export" comme un {id}.
// -----------------------------------------------------------------------
Route::middleware(AdminApiKey::class)->group(function () {

    // Export CSV – déclaré en premier pour éviter le conflit avec {id}
    Route::get('responses/export/csv', [SurveyResponseController::class, 'exportCsv'])
        ->name('responses.export.csv');

    // Liste paginée + filtres
    Route::get('responses', [SurveyResponseController::class, 'index'])
        ->name('responses.index');

    // Détail d'une réponse
    Route::get('responses/{response}', [SurveyResponseController::class, 'show'])
        ->name('responses.show');

    // Suppression
    Route::delete('responses/{response}', [SurveyResponseController::class, 'destroy'])
        ->name('responses.destroy');

    // Statistiques agrégées
    Route::get('stats', [SurveyResponseController::class, 'stats'])
        ->name('stats');
});