<?php

// Strangely enough, I'm able to access even guest routes after logging in as tutor, which is abnormal and shouldn't be. Like if I log in as user, I can't access the guest route, which is exactly what is expected. But not when logging in as tutor

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\Tutor\GenerateTutorLoginOtpController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\Tutor\VerifyTutorLoginOtpController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\VerifyLoginOtpController;
use Illuminate\Support\Facades\Route;

// Route::post('/register', [RegisteredUserController::class, 'store'])
//     ->middleware('guest')
//     ->name('register');

Route::post('/get-authenticated', [AuthenticatedSessionController::class, 'store']) //the route was originally: /login
    ->middleware('guest')
    ->name('login');

Route::post('/verify-login-otp', [VerifyLoginOtpController::class, 'verify'])
    ->middleware('guest')
    ->name('otp.verify');

// Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
//     ->middleware('guest')
//     ->name('password.email');

// Route::post('/reset-password', [NewPasswordController::class, 'store'])
//     ->middleware('guest')
//     ->name('password.store');

// Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
//     ->middleware(['auth', 'signed', 'throttle:6,1'])
//     ->name('verification.verify');

// Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
//     ->middleware(['auth', 'throttle:6,1'])
//     ->name('verification.send');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::prefix('tutors')->group(function () {
    Route::post('/get-authenticated', [GenerateTutorLoginOtpController::class, 'store']) //the route was originally: /login
        ->middleware('guest:tutor')
        ->name('tutor.login');

    Route::post('/verify-login-otp', [VerifyTutorLoginOtpController::class, 'verify'])
        ->middleware('guest')
        ->name('tutor.verify');

    Route::post('/logout', [VerifyTutorLoginOtpController::class, 'destroy'])
        ->middleware('auth:tutor')
        ->name('tutor.logout');
});
