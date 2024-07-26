<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class VerifyLoginOtpController extends Controller
{
    public function verify(LoginRequest $request): Response
    {
        // Log::info("Before authenticate with otp");
        $request->authenticateWithOtp();
        // Log::info("After authenticate with otp");
        $request->session()->regenerate();

        return response()->noContent();
    }
}
