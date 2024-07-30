<?php

namespace App\Http\Requests\Auth\Tutor;

use App\Models\LoginOtp;
use App\Models\Tutor;
use App\Models\TutorLoginOtp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class VerifyTutorLoginOtpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        //these rules automatically apply immediately (before anything else) to any request instantiated as VerifyTutorLoginOtpRequest, because rules() run immediately (before all other methods) against a request that's being instantiated to LoginRequest. This is why, in other methods of this class, you don't need to validate the fields of the request mentioned here again because they have already been validated, and had they found to be invalid, an error would've been thrown long before you get the chance to invoke the other methods
        return [
            'email' => ['required', 'string', 'email'],
            'otp' => ['required', 'string', 'size:16'],
        ];
    }

    /**
     * Attempt to authenticate the request's otp.
     *
     * @throws \Illuminate\Validation\ValidationException
     */

    public function authenticateWithOtp(): void
    {
        $this->ensureIsNotRateLimited();


        Log::info("remember value: " . $this->boolean('remember'));
        // if ($this->remember) {
        //     Log::info("remember is set. Its value: " . $this->boolean('remember'));
        // } else {
        //     Log::info("remember is NOT set." . $this->boolean('remember'));
        // }

        //use $this->input('field') instead of $this->field. Worry not, input() can retrieve any member from the request, not just input fields! Its just a good practice

        $tutor = Tutor::where('email', $this->input('email'))->first();
        if (!$tutor) {
            RateLimiter::hit($this->throttleKey());
            // return response()->json([
            //     'message' => 'Invalid credentials'
            // ], 401); // 401 Unauthorized
            throw ValidationException::withMessages([
                'credentials' => __('auth.failed'),
            ]);
        }

        $loginOtp = TutorLoginOtp::where('tutor_id', $tutor->id)->first();


        if (
            !$loginOtp ||
            $loginOtp->expires_at <= Carbon::now() ||
            !Hash::check($this->input('otp'), $loginOtp->otp)
        ) {

            // Log::info("Inside triple if");

            RateLimiter::hit($this->throttleKey());
            // throw ValidationException::withMessages([
            //     'otp' => __('auth.failed'),
            // ]);
            throw ValidationException::withMessages([
                'credentials' => __('auth.failed'), //the key can be any string but best is to not give anything specific like email or otp (especially when it comes to auth), just give a vague 'credentials' as key string for the client on which field was wrong, and in the _(), you give a code, such as auth.failed is a built-in code in laravel that has a nice associated string to it (which is "These credentials do not match our records").
            ]);
            // return response()->json([
            //     'message' => 'Invalid credentials'
            // ], 401); // 401 Unauthorized
        }


        // Log in the tutor with the 'tutor' guard (very important! If you don't specify the guard during login, the default guard will be used, which has been configured to be 'web' guard, then you'll be able to access web guarded routes but not tutor guarded routes! Setting appropriate guards during login is very important! Do the same when logging in using Auth::attempt() too like this: Auth::guard('tutor')->attempt($credentials)):
        Auth::guard('tutor')->login($tutor, $this->boolean('remember')); //the second argument of login() enables long lived sessions if given true (for the feature 'Remember me' checkbox in the client). We're checking if the client sent a key named 'remember' in the request and whether its true or false (by if doesn't exist, it'll be null and the default argument of login() will kick in, which is false for $remember)



        $loginOtp->delete();
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [ //the field name is 'email', because we're throttling based on email (or ip), look at the last line of the throttleKey() function below this function
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')) . '|' . $this->ip());
    }
}
