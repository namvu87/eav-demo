<?php

use Illuminate\Support\Facades\Route;

// Entity Types
Route::get('/entity-types', [\App\Http\Controllers\Api\EntityTypeController::class, 'index']);
Route::post('/entity-types', [\App\Http\Controllers\Api\EntityTypeController::class, 'store']);
Route::get('/entity-types/{id}', [\App\Http\Controllers\Api\EntityTypeController::class, 'show']);
Route::put('/entity-types/{id}', [\App\Http\Controllers\Api\EntityTypeController::class, 'update']);
Route::delete('/entity-types/{id}', [\App\Http\Controllers\Api\EntityTypeController::class, 'destroy']);
Route::get('/entity-types/{id}/stats', [\App\Http\Controllers\Api\EntityTypeController::class, 'stats']);

// Attributes
Route::post('/attributes', [\App\Http\Controllers\Api\AttributeController::class, 'store']);
Route::get('/entity-types/{typeId}/attributes', [\App\Http\Controllers\Api\AttributeController::class, 'listByType']);
Route::get('/attributes/shared', [\App\Http\Controllers\Api\AttributeController::class, 'listShared']);
Route::get('/attributes/{id}', [\App\Http\Controllers\Api\AttributeController::class, 'show']);
Route::put('/attributes/{id}', [\App\Http\Controllers\Api\AttributeController::class, 'update']);
Route::delete('/attributes/{id}', [\App\Http\Controllers\Api\AttributeController::class, 'destroy']);
Route::post('/attributes/{id}/options', [\App\Http\Controllers\Api\AttributeController::class, 'addOption']);
Route::put('/attributes/reorder', [\App\Http\Controllers\Api\AttributeController::class, 'reorder']);

// Entities
Route::post('/entities', [\App\Http\Controllers\Api\EntityController::class, 'store']);
Route::get('/entity-types/{typeId}/entities', [\App\Http\Controllers\Api\EntityController::class, 'listByType']);
Route::get('/entities/{id}', [\App\Http\Controllers\Api\EntityController::class, 'show']);
Route::put('/entities/{id}', [\App\Http\Controllers\Api\EntityController::class, 'update']);
Route::delete('/entities/{id}', [\App\Http\Controllers\Api\EntityController::class, 'destroy']);
Route::post('/entities/bulk-create', [\App\Http\Controllers\Api\EntityController::class, 'bulkCreate']);

// Tree
Route::get('/entity-types/{typeId}/tree', [\App\Http\Controllers\Api\TreeController::class, 'treeByType']);
Route::get('/entities/{id}/children', [\App\Http\Controllers\Api\TreeController::class, 'children']);
Route::get('/entities/{id}/descendants', [\App\Http\Controllers\Api\TreeController::class, 'descendants']);
Route::get('/entities/{id}/ancestors', [\App\Http\Controllers\Api\TreeController::class, 'ancestors']);
Route::post('/entities/{id}/move', [\App\Http\Controllers\Api\TreeController::class, 'move']);

// Relations
Route::post('/relations', [\App\Http\Controllers\Api\RelationController::class, 'store']);
Route::get('/entities/{id}/relations', [\App\Http\Controllers\Api\RelationController::class, 'listByEntity']);
Route::get('/entities/{id}/relations/{type}', [\App\Http\Controllers\Api\RelationController::class, 'listByEntityAndType']);
Route::delete('/relations/{id}', [\App\Http\Controllers\Api\RelationController::class, 'destroy']);
Route::put('/relations/{id}', [\App\Http\Controllers\Api\RelationController::class, 'update']);
Route::post('/relations/bulk', [\App\Http\Controllers\Api\RelationController::class, 'bulkStore']);

// Search & Filter
Route::get('/entities/search', [\App\Http\Controllers\Api\SearchController::class, 'search']);
Route::post('/entities/filter', [\App\Http\Controllers\Api\SearchController::class, 'filter']);
