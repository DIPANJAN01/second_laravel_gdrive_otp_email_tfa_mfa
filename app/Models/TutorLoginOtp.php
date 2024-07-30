<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorLoginOtp extends Model
{
    use HasFactory;

    protected $fillable = ['tutor_id', 'otp', 'expires_at'];

    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }
}
