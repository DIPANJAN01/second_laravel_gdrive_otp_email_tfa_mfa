<?php

namespace App\Http\Requests\Auth\Tutor;

use App\Models\LoginOtp;
use App\Models\Tutor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class GenerateTutorLoginOtpRequest extends FormRequest
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
        //these rules automatically apply immediately (before anything else) to any request instantiated as LoginRequest, because rules() run immediately (before all other methods) against a request that's being instantiated to LoginRequest. This is why, in other methods of this class, you don't need to validate the fields of the request mentioned here again because they have already been validated, and had they found to be invalid, an error would've been thrown long before you get the chance to invoke the other methods
        return [
            'email' => ['required', 'string', 'email'],
            // 'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateEmail()
    {
        $this->ensureIsNotRateLimited();
        $emailExists = Tutor::where('email', $this->input('email'))->exists();
        if (!$emailExists) {
            RateLimiter::hit($this->throttleKey());
            return false;
        }

        RateLimiter::clear($this->throttleKey());
        return true;
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
            'email' => trans('auth.throttle', [
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
