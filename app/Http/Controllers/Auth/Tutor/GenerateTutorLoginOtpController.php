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
        $request->validateEmail(); //if email is invalid, a response will be sent otherwise will proceed to below

        // $request->authenticate();

        $otp = $this->generateAndSaveOtp($request);

        $this->sendOtpEmail($otp, $request->email);

        return response()->json([
            "message" => "Otp sent to email. Please verify.",
            "email" => $request->email,
        ]);

        // $request->session()->regenerate();

        // return response()->noContent();
    }


    private function generateAndSaveOtp(GenerateTutorLoginOtpRequest $request)
    {
        // Fetch the tutor
        $tutor = Tutor::where('email', $request->email)->first();

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
        $otp = Str::password(12);
        Log::info("$tutor->email = $otp");

        // Store OTP and expiration time in the login_otps table
        $loginOtp = TutorLoginOtp::create([
            'tutor_id' => $tutor->id,
            'otp' => Hash::make($otp),
            'expires_at' => Carbon::now()->addMinute(),

        ]); //if ::create() fails, an exception is thrown which prompts Laravel to automatically handle it and send a response

        return $otp;
    }
    private function sendOtpEmail(String $otp, String $email)
    {
        Mail::to($email)->send(new OtpEmail($otp));
    }
}
