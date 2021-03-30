<?php

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

Route::post('/request-quote', [\App\Http\Controllers\ContactController::class, 'requestQuote']);

Route::post('/send-contact', [\App\Http\Controllers\ContactController::class, 'sendContact']);

Route::post('/send-farm-contact', [\App\Http\Controllers\ContactController::class, 'sendNipojFarmContact']);

Route::get('/all-documents', [\App\Http\Controllers\DTS::class, 'getAllDocuments']);

Route::post('/upload-document', [\App\Http\Controllers\DTS::class, 'uploadDocument']);

Route::post('request-password', [\App\Http\Controllers\DTS::class, 'sendPasswordRequest']);

Route::post('/upload-public-document', [\App\Http\Controllers\DTS::class, 'uploadPublicDocument']);

Route::post('/apply-career', [\App\Http\Controllers\ContactController::class, 'sendCareerEmail']);

Route::get('/view-document', [\App\Http\Controllers\DTS::class, 'getDocument']);

Route::get('/generate-password', [\App\Http\Controllers\DTS::class, 'generateOTP']);

Route::get('send-otp-mail', [\App\Http\Controllers\DTS::class, 'sendOTPMail'])->name('send_otp_mail');

Route::get('send-otp-downloadable-mail', [\App\Http\Controllers\DTS::class, 'sendOTPDownloadableMail'])
    ->name('send_otp_downloadable_mail');

Route::get('/generate-downloadable-password', [\App\Http\Controllers\DTS::class, 'generateDownloadableOTP']);

Route::post('check-password', [\App\Http\Controllers\DTS::class, 'checkPassword']);

Route::post('/invalidate-password', [\App\Http\Controllers\DTS::class, 'invalidatePasswordFromFrontEnd']);



Route::middleware(['auth.api'])->group(function() {
    Route::post('/set-password', [\App\Http\Controllers\DTS::class, 'setPassword']);

    Route::get('/delete-document/{id}', [\App\Http\Controllers\DTS::class, 'deleteDocument']);

    Route::get('/rename-document/{id', [\App\Http\Controllers\DTS::class, 'renameDocument']);
});



