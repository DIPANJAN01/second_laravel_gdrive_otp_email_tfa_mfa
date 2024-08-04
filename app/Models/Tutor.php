<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// class Tutor extends Model
class Tutor extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $guard = "tutor";


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [ //specifying fillable automatically makes the rest of the columns guarded (i.e., those cannot be assigned from outside), and specifying guarded makes the rest fillable; and if both fillable and guarded are specified for the same columns, fillable takes precedence for those columns
        'name',
        'email',
        'age',
        'number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['created_at', 'updated_at', 'password', 'remember_token',]; //these won't show up when sending an instance of Tutor model as json response 


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function loginOtp()
    {
        return $this->hasOne(TutorLoginOtp::class);
    }
    public function updateOtp()
    {
        return $this->hasOne(TutorUpdateOtp::class);
    }
    public function updateHistories()
    {
        return $this->hasMany(TutorHistory::class);
    }
}
