<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

// Public routes
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// Admin routes - requires 'admin' role
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/settings', function () {
        return 'Admin Settings Page';
    })->name('admin.settings');
});

// Manager routes - requires 'manager' role
Route::middleware(['auth', 'role:manager'])->prefix('manager')->group(function () {
    Route::get('/reports', function () {
        return 'Manager Reports Page';
    })->name('manager.reports');
});

// Content management routes - requires 'manage content' permission
Route::middleware(['auth', 'permission:view content'])->prefix('content')->group(function () {
    Route::get('/', function () {
        return 'Content Management Page';
    })->name('content.index');
    
    // Nested route that requires additional permission
    Route::middleware(['permission:create content'])->group(function () {
        Route::get('/create', function () {
            return 'Create Content Page';
        })->name('content.create');
    });
});
