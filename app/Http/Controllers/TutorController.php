<?php

namespace App\Http\Controllers;

use App\Models\Tutor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class TutorController extends Controller
{
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
        // return response()->json(['tutor' => $tutor], 200);
        // Get the currently authenticated tutor

        // Log::info("In showById");
        Gate::authorize('view', $tutor);
        // Log::info("Tutor: $tutor");
        return response()->json(['tutor' => $tutor], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tutor $tutor)
    {
        $currentTutor = Auth::guard('tutor')->user();
        // Log::info("In update()");
        // Log::info("Current Tutor: $tutor");
        // Log::info("Target Tutor: $tutor");

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
