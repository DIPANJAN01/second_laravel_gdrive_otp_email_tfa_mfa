<?php

use App\Mail\OtpEmail;
use App\Mail\WelcomeEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
Route::middleware(['auth:web'])->get('/user', function (Request $request) {
    return $request->user();
});

// Route::middleware(['auth:sanctum'])->get('/secret', function (Request $request) {
Route::middleware(['auth:web'])->get('/secret', function (Request $request) {
    return response()->json([
        'secret' => 'This is a secret!'
    ]);
});

Route::middleware(['auth:tutor'])->get('/tutor/secret', function (Request $request) {
    return response()->json([
        'secret' => 'This is a Tutor secret!'
    ]);
});

Route::get('/send-email', function (Request $request) {
    $sentMessage = Mail::to('dipanjanghosal01@gmail.com')->send(new WelcomeEmail());

    return ["message" => "mail sent!", "sentMessage" => $sentMessage];
});

Route::get('/send-otp', function (Request $request) {
    $sentMessage = Mail::to('dipanjan01yt@gmail.com')->send(new OtpEmail('234568'));

    return ["message" => "otp sent to dipanjan01yt@gmail.com!", "sentMessage" => $sentMessage];
});
Route::get('/bcrypt', function (Request $request) {


    return [
        "original" => "1234",
        "bcrypted" => bcrypt("1234"),
    ];
});
