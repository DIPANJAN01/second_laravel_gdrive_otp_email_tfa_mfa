<?php

namespace App\Http\Requests\Auth;

use App\Models\LoginOtp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
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
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
        //     RateLimiter::hit($this->throttleKey());

        //     throw ValidationException::withMessages([
        //         'email' => __('auth.failed'),
        //     ]);
        // }

        if (!Auth::validate($this->only('email', 'password'))) {
            // Log::info('In authenticate, inside  !Auth::validate()');
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }
    public function authenticateWithOtp(): void
    {
        $this->ensureIsNotRateLimited();


        $this->validate([
            'otp' => ['required', 'string', 'size:8'] //email and password are already covered by rules()
        ]);
        Log::info("After validate");
        $user = User::where('email', $this->email)->first();


        // Log::info("Client given Password: " . $this->otp);
        // Log::info("Hashed Password: " . $user->loginOtp->otp);


        if (
            !$user ||
            $user->loginOtp === null ||
            $user->loginOtp->expires_at <= Carbon::now() ||
            !Hash::check($this->otp, $user->loginOtp->otp)
        ) {
            // Log::info("Inside triple if");
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'otp' => __('auth.failed'),
            ]);
        }


        if (!Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        LoginOtp::where('user_id', $user->id)->delete();
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
