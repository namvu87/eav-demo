<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EavController;

Route::get('/', [App\Http\Controllers\DashboardController::class, 'index']);


Route::middleware(['web'])->group(function () {
    // EAV Routes
    Route::prefix('eav')->name('eav.')->group(function () {
        Route::get('/', [EavController::class, 'index'])->name('index');
        Route::get('/create', [EavController::class, 'create'])->name('create');
        Route::post('/', [EavController::class, 'store'])->name('store');
        Route::get('/{id}', [EavController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [EavController::class, 'edit'])->name('edit');
        Route::put('/{id}', [EavController::class, 'update'])->name('update');
        Route::delete('/{id}', [EavController::class, 'destroy'])->name('destroy');
        
        // API Routes
        Route::get('/api/search', [EavController::class, 'search'])->name('search');
        Route::get('/api/tree', [EavController::class, 'tree'])->name('tree');
    });

    // Entity Type Management Routes
    Route::prefix('entity-types')->name('entity-types.')->group(function () {
        Route::get('/', [App\Http\Controllers\EntityTypeController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\EntityTypeController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\EntityTypeController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\EntityTypeController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [App\Http\Controllers\EntityTypeController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\EntityTypeController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\EntityTypeController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/manage', [App\Http\Controllers\EntityTypeController::class, 'manage'])->name('manage');
    });

    // Attribute Management Routes
    Route::prefix('attributes')->name('attributes.')->group(function () {
        Route::get('/', [App\Http\Controllers\AttributeController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\AttributeController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\AttributeController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\AttributeController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [App\Http\Controllers\AttributeController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\AttributeController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\AttributeController::class, 'destroy'])->name('destroy');
    });

    // API routes for dynamic forms
    Route::prefix('api')->group(function () {
        Route::get('/entity-types/{id}/attributes', [App\Http\Controllers\EntityTypeController::class, 'getAttributes']);
        Route::get('/entity-types/{id}', [App\Http\Controllers\EntityTypeController::class, 'show']);
        Route::get('/entities/count', [EavController::class, 'count']);
    });

    // Hierarchy management routes
    Route::prefix('hierarchy')->name('hierarchy.')->group(function () {
        Route::get('/', [App\Http\Controllers\HierarchyController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\HierarchyController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\HierarchyController::class, 'store'])->name('store');
        Route::delete('/{id}', [App\Http\Controllers\HierarchyController::class, 'destroy'])->name('destroy');
        Route::put('/{id}/move', [App\Http\Controllers\HierarchyController::class, 'move'])->name('move');
        Route::get('/tree', [App\Http\Controllers\HierarchyController::class, 'getTree'])->name('tree');
    });
});
