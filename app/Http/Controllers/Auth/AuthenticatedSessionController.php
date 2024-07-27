<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Mail\OtpEmail;
use App\Models\LoginOtp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse //this return type must be made JsonResponse, not Response! Otherwise this will fail!
    {

        $request->authenticate();

        $data = $this->generateAndSaveOtp($request);

        $this->sendOtpEmail($data['otp'], $request->email);

        return response()->json([
            "message" => "Otp sent to email. Please verify.",
            "email" => $request->email,
            "key" => $data['key'],
        ]);

        // $request->session()->regenerate();

        // return response()->noContent();
    }
    function getRandomAlphabet()
    {
        // ASCII ranges for uppercase A-Z (65-90) and lowercase a-z (97-122)
        $ranges = [
            [65, 90],   // Uppercase A-Z
            [97, 122],  // Lowercase a-z
        ];

        // Randomly select a range (0 for uppercase, 1 for lowercase)
        $rangeIndex = random_int(0, 1);
        $range = $ranges[$rangeIndex];

        // Generate a random ASCII code within the selected range
        $randomAscii = random_int($range[0], $range[1]);

        // Convert the ASCII code to a character
        return chr($randomAscii);
    }

    private function generateAndSaveOtp(LoginRequest $request)
    {
        // Fetch the user
        $user = User::where('email', $request->email)->first();

        // Retrieve the latest OTP record for the user
        $loginOtp = LoginOtp::where('user_id', $user->id)->first();


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
        $otp = $this->getRandomAlphabet() . rand(config("custom.otp_values.start"), config("custom.otp_values.end")) . $this->getRandomAlphabet();

        // Store OTP and expiration time in the login_otps table
        $loginOtp = LoginOtp::create([
            'user_id' => $user->id,
            'otp' => Hash::make($otp),  //encrypt the otp while saving in database
            //the otp gets hashed while the key doesn't is because the key is just a temporary identifier to identify which user it was that gave correct credentials and now is submitting the otp. The otp gets hashed because that is the critical value, but this key field doesn't get hashed, its just an identifier (hashing it would just needlessly make searching for it in db more difficult and its entire point was to be searched effectively in the first place), arguably more secure than email, which could also have been used to identify the user at /verify-otp, but that could've lead to spams from an attacker if the attacker somehow got to know the user's email, which is pretty easy to do. With randomly generated, short-lived, temporary uuids, even if the attacker knows it, he can't do much damage
            'expires_at' => Carbon::now()->addSeconds(30),

        ]); //if ::create() fails, an exception is thrown which prompts Laravel to automatically handle it and send a response

        return [
            "otp" => $otp,
            "key" => $loginOtp->key,
        ];
    }
    private function sendOtpEmail(String $otp, String $email)
    {
        Mail::to($email)->send(new OtpEmail($otp));
    }
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
