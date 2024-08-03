<?php

namespace App\Http\Controllers;

use App\Mail\OtpEmail;
use App\Models\Tutor;
use App\Models\TutorUpdateOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class TutorController extends Controller
{
    private function generateAndMailNewUpdateOtp($tutor, $newEmail = null)
    {
        Log::info("newEmail: $newEmail");
        $newOtpValue = Str::password(6, false, true, false);
        $newOtpRow = TutorUpdateOtp::create([
            'tutor_id' => $tutor->id,
            'otp' => Hash::make($newOtpValue),
            'type' => 'email',
            'expires_at' => Carbon::now()->addMinute(),
        ]);
        if ($newOtpRow !== null && $newOtpRow !== null) {

            Mail::to($newEmail)->send(new OtpEmail($newOtpValue));
        }

        return $newOtpRow;
    }

    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     //
    // }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        // return response()->json(['tutor' => $tutor], 200);
        // Get the currently authenticated tutor
        $tutor = Auth::guard('tutor')->user();
        // Log::info("In show()");
        // Log::info("Tutor: $tutor");
        // Log::info("Tutor is an instance of Tutor? ");
        // Log::info($tutor instanceof Tutor ? "true" : "false");

        $currentAdmin = Auth::guard('web')->user();
        $currentTutor = Auth::guard('tutor')->user();
        $currentUser = Auth::user();
        Log::info("Current Admin: $currentAdmin");
        Log::info("Current Tutor: $currentTutor");
        Log::info("Current User: $currentUser");
        Log::info(Auth::guard('web')->check() && Auth::guard('web')->user() instanceof User);

        if ($tutor) {
            // Log::info('Inside if($tutor)');

            Gate::authorize('view', $tutor);
            // Log::info('After authorize()');
            return response()->json(['tutor' => $tutor], 200);
        } else {
            return response()->json(['message' => 'Tutor not found.'], 404);
        }
    }
    public function showById(Tutor $tutor)
    {
        $currentAdmin = Auth::guard('web')->user();
        $currentTutor = Auth::guard('tutor')->user();
        $currentUser = Auth::user();
        Log::info("Current Admin: $currentAdmin");
        Log::info("Current Tutor: $currentTutor");
        Log::info("Current User: $currentUser");
        Log::info(Auth::guard('web')->check() && Auth::guard('web')->user() instanceof User);

        Gate::authorize('view', $tutor);
        // Log::info("Tutor: $tutor");
        return response()->json(['tutor' => $tutor], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tutor $tutor)
    {


        Gate::authorize('update', $tutor);

        $validatedData = $request->validate([
            'name' => ['required', 'string'],
            'age' => ['required', 'integer', 'min:18'],
            // 'email' => ['required', 'string', 'email', "unique:tutors,email,$tutor->id"],//old syntax
            // 'number' => ['required', 'string', "unique:tutors,number,$tutor->id"],//new syntax shown next:
            'email' => [
                'required', 'string', 'email',
                Rule::unique('tutors')->ignore($tutor->id), //it should be unique but ignore uniqueness if it already belongs to the current user (WARNING: You should never pass any user controlled request input into the ignore method. Instead, you should only pass a system generated unique ID such as an auto-incrementing ID or UUID from an Eloquent model instance. Otherwise, your application will be vulnerable to an SQL injection attack.)
            ],
            'number' => [
                'required', 'string',
                Rule::unique('tutors')->ignore($tutor->id)
            ]
        ]);

        if (!(Auth::guard('web')->check() && Auth::guard('web')->user() instanceof User) && $validatedData['email'] !== $tutor->email) {
            $tutorUpdateOtp = TutorUpdateOtp::where('tutor_id', $tutor->id)->where('type', 'email')->first();

            if ($tutorUpdateOtp === null) {
                $newOtpRow = $this->generateAndMailNewUpdateOtp($tutor, $validatedData['email']);
                if ($newOtpRow === null) {
                    return response()->json([
                        'status' => 'failure',
                        'message' => "Otp generation failed. Please try again."
                    ]);
                }

                return response()->json([
                    'status' => 'otp',
                    'message' => "Otp sent successfully to " . $validatedData['email'],
                    'otp_duration' => 60,
                ]);
            }

            if (Carbon::now() >= $tutorUpdateOtp->expires_at) {
                $tutorUpdateOtp->delete();
                $newOtpRow = $this->generateAndMailNewUpdateOtp($tutor, $validatedData['email']);
                if ($newOtpRow === null) {
                    return response()->json([
                        'status' => 'failure',
                        'message' => "Otp generation failed. Please try again."
                    ]);
                }

                return response()->json([
                    'status' => 'otp',
                    'message' => "Previous otp expired. New Otp generated and sent successfully to " . $validatedData['email'],
                    'otp_duration' => 60,
                ]);
            }

            $validatedReqMailOtp = $request->validate(['email_otp' => ['required', 'string', 'size:6']]);
            if (!Hash::check($validatedReqMailOtp['email_otp'], $tutorUpdateOtp->otp)) {
                return response()->json([
                    'status' => 'failure',
                    'message' => "Invalid Otp!"
                ]);
            }
        }


        $wasUpdated = $tutor->update($validatedData); //returns true or false
        if ($wasUpdated) {
            return response()->json(['success' => true], 200);
        } else {
            return response()->json(['success' => false], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tutor $tutor)
    {
        Gate::authorize('delete', $tutor);

        $wasDeleted = $tutor->delete();
        if ($wasDeleted) {
            return response()->json(['success' => true], 200);
        } else {
            return response()->json(['success' => false], 400);
        }
    }
}
