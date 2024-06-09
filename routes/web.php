<?php

use App\Http\Controllers\FilesController;
use App\Livewire\CreateDocument;
use App\Livewire\ManageServices;
use App\Livewire\ManageCategories;
use App\Livewire\ManageUsers;
use App\Livewire\UpdateDocument;
use App\Livewire\ViewDocumentComponent;
use App\Livewire\ViewDocuments;
use Illuminate\Bus\UpdatedBatchJobCounts;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;

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
    Route::get('/documents/create', CreateDocument::class)->name('create-document');
    Route::get('/documents/all', ViewDocuments::class)->name('view-documents');
    Route::get('/files/{path}', [FilesController::class, 'show'])->where('path', '.*')->name('files.show');
    Route::get('/documents/view/{id}', ViewDocumentComponent::class)->name('documents.view');
    Route::get('/documents/update/{id}', UpdateDocument::class)->name('documents.update');
    Route::get('locale/{locale}', function ($locale) {
        session(['locale' => $locale]);
        return redirect()->back();
    })->name('locale.switch');


});

