<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorUpdateOtp extends Model
{
    use HasFactory;
    protected $fillable = ['tutor_id', 'otp', 'type', 'expires_at'];

    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }
}
