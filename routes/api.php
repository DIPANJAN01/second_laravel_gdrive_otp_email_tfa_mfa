<?php

use App\Http\Controllers\TutorController;
use App\Mail\OtpEmail;
use App\Mail\WelcomeEmail;
use App\Models\Tutor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

Route::middleware(['auth:tutor'])->get('/tutors/secret', function (Request $request) {
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

Route::get(
    '/reject',
    function (Request $request) {
        Log::info('env value: ' . config('services.google.web_api_test'));

        try {
            $response = Http::withHeaders([
                // 'secret' => 'YOUR_SECRET',//GAS doesn't give us any way to access request headers in its doPost(e) function, so we need to pass the auth keys via body itself, not headers
            ])->post(
                config('services.google.web_api_test'),
                // "asxc.conaaea",
                [
                    'secret' => config('services.google.web_api_test_secret'), //the secret key
                    'person' => [
                        'name' => 'John Doe',
                        'email' => 'john.doe@example.com'
                    ],
                    'rejected' => ['dipanjanghosal01@gmail.com',] //will contain all the rejected person's emails
                ]
            );
            Log::info("No Error Response " . $response->body());
            return response()->json(['status' => 'success', 'gas_response' => $response->body()]);

            // Print the response body
        } catch (\Exception $e) {

            Log::info("Error Response: " . $e->getMessage());
            return 'Request failed: ' . $e->getMessage();

            // $response->onError(function ($response) {

            //     Log::info("Error Response: " . $response->body());
            //     return 'Request failed: ' . $response->body();
            // });

            // Log::info("No error Response: " . $response->body());
            // return $response->body();
        }
    }
);


// Route::apiResource("tutors", TutorController::class)->except(["index", "store"]);//equivalent to:
// Route::apiResource("tutors", TutorController::class)->only(["show", "update", "destroy"])->middleware("auth:tutor");

function tutorRouteGroup()
{
    // Route::middleware(['multi-auth'])->group(function () {
    Route::apiResource('tutors', TutorController::class)->only([
        'update', 'destroy'
    ]);
    Route::get('tutors/', [TutorController::class, 'show'])->name('tutor.show'); //not using apiResource's url mapping because apiResource maps our show() method to url /tutors/tutor-id, but I want the user to just be able to make a request to show, without needing to provide id, and if the tutor is authenticated, the tutor row will be returned to him. This will help a lot when making the frontend since now you don't have to worry about remembering the id, especially when you get auto logged-in because of any active sessions in which case you won't even have a chance of getting the tutor id from the verify-login-otp route. Just make a request to show, and if you're authenticated, you'll get the details. As for why I'm not using this same approach of not needing id for the rest of the tutor routes like update, destroy, its because its extra work and typing when apiResource just nicely maps a url to them, and the laravel controller methods automatically fetch the resource from the provided id and gives it to you in the argument, which skips a lot of typing, and anytime the client wants to know the id, he can just make a request to /show, which is the reason I made it not require id in its url
    Route::get('tutors/{tutor}', [TutorController::class, 'showById'])->name('tutor.show-by-id');
};

// Route::middleware(['auth:tutor'])->group(function () {
// Route::middleware(['multi-auth'])->group(function () {
Route::middleware(['auth:web,tutor'])->group(function () {
    tutorRouteGroup();
});

// Route::middleware(['auth:web'])->group(function () {
//     tutorRouteGroup();
// });
