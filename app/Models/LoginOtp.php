<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class LoginOtp extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = ['user_id', 'otp', 'expires_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    //these two methods are coming from HasUuids trait and we're overwriting them here as needed, for this model:

    /**
     * Generate a new UUID for the model.
     */
    public function newUniqueId(): string
    {
        return (string) Uuid::uuid1(); //use uuid1 for mysql (uuid4 needs more setup for efficient use in mysql), use uuid4 for postgres
    }

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['key']; //the fields/columns that you want to be populated with uuids
    }

    public $timestamps = true;
}
