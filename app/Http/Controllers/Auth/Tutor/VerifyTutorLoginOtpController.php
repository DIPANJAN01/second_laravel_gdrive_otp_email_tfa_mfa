<?php

namespace App\Http\Controllers\Auth\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Tutor\VerifyTutorLoginOtpRequest;
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

class VerifyTutorLoginOtpController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function verify(VerifyTutorLoginOtpRequest $request): JsonResponse //this return type must be made JsonResponse, not Response! Otherwise this will fail!
    {
        // Log::info("Before authenticate with otp");
        $request->authenticateWithOtp();
        // Log::info("After authenticate with otp");
        $request->session()->regenerate();

        return response()->noContent();
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('tutor')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
