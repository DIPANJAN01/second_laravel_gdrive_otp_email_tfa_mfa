<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
// class Tutor extends Model
class Tutor extends Authenticatable
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
