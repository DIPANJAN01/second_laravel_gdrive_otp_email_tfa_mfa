<?php

namespace App\Http\Controllers\Auth\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Tutor\GenerateTutorLoginOtpRequest;
use App\Http\Requests\Auth\Tutor\LoginRequest;
use App\Http\Requests\Auth\Tutor\TutorGenerateLoginOtpRequest;
use App\Mail\OtpEmail;
use App\Models\Tutor;
use App\Models\TutorLoginOtp;
use Illuminate\Support\Str;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GenerateTutorLoginOtpController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(GenerateTutorLoginOtpRequest $request): JsonResponse //this return type must be made JsonResponse, not Response! Otherwise this will fail!
    {
        $isValid = $request->validateEmail(); //if email is invalid in format or missing, a laravel will immediately send a response from there by throwing a validation error, and if email is valid in format but doesn't exist in db, will return false (simply returning a response from there doesn't stop the execution flow, it just returns the response to its caller (which would be here), stopping the control flow and returning response immediately from any point can only be done by throwing exceptions without handling them)

        if (!$isValid) { //email format was valid but wasn't found in db
            Log::info($request->input('email') . " doesn't exist in tutors table");

            return response()->json([
                "message" => "Otp sent to email. Please verify.",
                "email" => $request->input('email'),
            ], 200); //this is bad practice, I know. But I'm desperate to stop the brute-forcers
        }
        // $request->authenticate();

        $otp = $this->generateAndSaveOtp($request);

        Log::info("Reaching sendOtpEmail, Otp: $otp");

        $this->sendOtpEmail($otp, $request->email);

        return response()->json([
            "message" => "Otp sent to email. Please verify.",
            "email" => $request->email,
            "otp_duration" => 60,
        ]);

        // $request->session()->regenerate();

        // return response()->noContent();
    }


    private function generateAndSaveOtp(GenerateTutorLoginOtpRequest $request)
    {
        // Fetch the tutor
        $tutor = Tutor::where('email', $request->input('email'))->first();
        // if (!$tutor) {
        //     abort(404); //coming inside generateOtp means the tutor should already be in the db, but if the user is still not found for some reason (maybe it got deleted by the time this function executes), we'll abort this request from here, although an error would be thrown and err response would've been given anyways in the next line where you'd be trying to access member of null
        // }

        // Retrieve the latest OTP record for the tutor
        $loginOtp = TutorLoginOtp::where('tutor_id', $tutor->id)->first();


        if ($loginOtp !== null) {
            // if ($loginOtp->expires_at > Carbon::now()) {
            //     // OTP is still valid, return it
            //     return $loginOtp;
            // }
            // // OTP has expired, delete the record
            // $loginOtp->delete();

            // Just delete the previous otp
            $loginOtp->delete();
        }

        // Generate OTP
        // $otp = Str::password(16);
        $specialCharacters = ['$', '*', '#', '%'];
        $randomKey = array_rand($specialCharacters);

        $otp = Str::password(1, true, false, false) .  Str::password(6, false, true, false) . Str::password(1, true, false, false); //A123456c
        //  . $specialCharacters[$randomKey]; //A123456$ //characters doesn't get easily selected against letters or numbers, we should have easily copyable OTPs

        Log::info("$tutor->email = $otp");

        // Store OTP and expiration time in the login_otps table
        $loginOtp = TutorLoginOtp::create([
            'tutor_id' => $tutor->id,
            'otp' => Hash::make($otp),
            'expires_at' => Carbon::now()->addMinute(),

        ]); //if ::create() fails, an exception is thrown which prompts Laravel to automatically handle it and send a response
        // Log::info("Otp: $otp");
        // abort(500);
        return $otp;
    }
    private function sendOtpEmail(String $otp, String $email)
    {
        Mail::to($email)->send(new OtpEmail($otp));
    }
}
