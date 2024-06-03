<?php

<<<<<<< HEAD
use App\Livewire\ManageServices;
=======
use App\Livewire\ManageCategories;
>>>>>>> 18b8ffd2c639edb97dbe2390bdbbb71499b9421b
use App\Livewire\ManageUsers;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/manage-users', ManageUsers::class)->name('manage-users')->middleware('role:admin');
    Route::get('/manage-services', ManageServices::class)->name('manage-services')->middleware('role:admin');
    Route::get('/manage-categories', ManageCategories::class)->name('manage-categories')->middleware('role:admin');
});

