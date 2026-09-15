<?php

use App\Http\Controllers\Auth\DiscordAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PersonalTagController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('login');

Route::get('/auth/discord', [DiscordAuthController::class, 'redirect'])->name('auth.discord');
Route::get('/auth/discord/callback', [DiscordAuthController::class, 'callback'])->name('auth.discord.callback');
Route::post('/logout', [DiscordAuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/library', [UploadController::class, 'index'])->name('library');
    Route::post('/uploads', [UploadController::class, 'store'])->name('uploads.store');
    Route::post('/personal-tags', [PersonalTagController::class, 'store'])->name('personal-tags.store');
    Route::delete('/personal-tags/{tag}', [PersonalTagController::class, 'destroy'])->name('personal-tags.destroy');

    Route::middleware('role:admin,owner')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::post('/users/{user}/roles', [UserRoleController::class, 'store'])->name('users.roles.store');

        Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
        Route::post('/tags', [TagController::class, 'store'])->name('tags.store');
        Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');
    });
});
