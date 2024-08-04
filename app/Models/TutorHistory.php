<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorHistory extends Model
{
    use HasFactory;

    protected $fillable = ['history', 'type', 'tutor_id'];
    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }
}
