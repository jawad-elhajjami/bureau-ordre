<?php

use App\Http\Controllers\DWTUploadController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\OtpVerificationController;
use App\Http\Livewire\SmtpSettings;
use App\Livewire\Courriel;
use App\Livewire\CreateDocument;
use App\Livewire\ManageServices;
use App\Livewire\ManageCategories;
use App\Livewire\ManageDocuments;
use App\Livewire\ManageUsers;
use App\Livewire\ScanDocuments;
use App\Livewire\UpdateDocument;
use App\Livewire\ViewDocumentComponent;
use App\Livewire\ViewDocuments;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

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
    Route::get('/document/create', CreateDocument::class)->name('create-document');
    Route::get('/inbox', ViewDocuments::class)->name('view-documents');
    Route::get('/files/{path}', [FilesController::class, 'show'])->where('path', '.*')->name('files.show');
    Route::get('/document/view/{id}', ViewDocumentComponent::class)->name('documents.view')->middleware('verify.otp');
    Route::get('/document/update/{id}', UpdateDocument::class)->name('documents.update');

    // Route::get(event(new App\Events\NotificationEvent('Hello World! I am an event 😄')));

    Route::get('locale/{locale}', function ($locale) {
        session(['locale' => $locale]);
        return redirect()->back();
    })->name('locale.switch');
    Route::get('/documents/view/all', ManageDocuments::class)->name('documents.all')->middleware('role:admin');

    // Route for OTP verification page
    Route::get('/document/otp-verify/{id}', [OtpVerificationController::class, 'show'])->name('otp.verify');
    Route::post('/document/otp-verify/{id}', [OtpVerificationController::class, 'verify'])->name('otp.verify.post');

    Route::get('/document/scan', ScanDocuments::class)->name('documents.scan');
    Route::post('/dwt_upload/upload', [DWTUploadController::class, 'upload'])->name('dwtupload.upload');
    Route::get('/courriel', Courriel::class)->name('courriel');
});
