<?php

namespace App\Policies;

use App\Models\Tutor;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class TutorPolicy
{
    /**
     * Runs before all other member policy-methods.
     */
    public function before($user, string $ability): bool|null //the first argument in all these methods always implicitly gives the currently authenticated user/tutor, etc. (you don't have to pass the first argument during function call, it's given implicitly, so you'll pass the second argument using function in the first position and so on... during the function call)
    {
        Log::info("Inside before()");

        if ($user instanceof User) { //that means its an admin (we're using the 'users' table for admin)
            return true;
        }
        return null; //otherwise its likely an instanceof Tutor or some other model, so let the member methods handle it
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Tutor $currentTutor): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Tutor $currentTutor, Tutor $tutor): bool
    {

        return $currentTutor->id === $tutor->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Tutor $currentTutor): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Tutor $currentTutor, Tutor $tutor): bool
    {
        //the current user must be of type Tutor (no other models should be able to access this freely by default, and if you want another model to access it, you can specify it here as discussed here): Simply type-hinting Tutor in the argument like (Tutor $currentTutor) would also work perfectly since then it'll disallow any model instance not of type Tutor. But in situations where you want multiple types of model instances to be able to access/get authorized in some way (for admins we're already using before(), so for any other model types aside from Tutor), don't give any specific type-hint in the parameter and just let any type of model instance be accepted. Then manually check if they're an instance of the models you want to allow authorization for (using instanceof), like this: return  $currentTutor instanceof Tutor &&  ...any other validations...
        //Like:
        // return  $currentTutor instanceof Tutor &&  $currentTutor->id === $tutor->id;

        return  $currentTutor->id === $tutor->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Tutor $currentTutor, Tutor $tutor): bool
    {
        return $currentTutor->id === $tutor->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Tutor $currentTutor, Tutor $tutor): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Tutor $currentTutor, Tutor $tutor): bool
    {
        return $currentTutor->id === $tutor->id;
    }
}
