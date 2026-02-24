<?php

use App\Http\Controllers\Api\ProductApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Product API Routes (Public)
|--------------------------------------------------------------------------
*/
Route::prefix('products')->group(function () {
    Route::get('/', [ProductApiController::class, 'index']);
    Route::get('/{id}', [ProductApiController::class, 'show']);
});

/*
|--------------------------------------------------------------------------
| Organization API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('organizations')->group(function () {
    Route::get('/{id}', function ($id) {
        $organization = app(\Webkul\Contact\Repositories\OrganizationRepository::class)->find($id);

        if (!$organization) {
            return response()->json(['message' => 'Organization not found'], 404);
        }

        return response()->json([
            'id'   => $organization->id,
            'name' => $organization->name,
        ]);
    });
});
