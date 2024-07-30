<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class VerifyLoginOtpController extends Controller
{
    public function verify(VerifyOtpRequest $request): Response
    {
        // Log::info("Before authenticate with otp");
        $request->authenticateWithOtp();
        // Log::info("After authenticate with otp");
        $request->session()->regenerate();

        return response()->noContent();
    }
}
