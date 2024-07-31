<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
// class Tutor extends Model
class Tutor extends Authenticatable
{
    use HasFactory;
    protected $hidden = ['created_at', 'updated_at']; //these won't show up when sending an instance of Tutor model as json response 
    protected $fillable = [ //specifying fillable automatically makes the rest of the columns guarded (i.e., those cannot be assigned from outside), and specifying guarded makes the rest fillable; and if both fillable and guarded are specified for the same columns, fillable takes precedence for those columns
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
