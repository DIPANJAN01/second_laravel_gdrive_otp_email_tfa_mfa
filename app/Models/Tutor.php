<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tutor extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'age',
        'number',
    ];

    public function loginOtp()
    {
        return $this->hasOne(TutorLoginOtp::class);
    }
}
