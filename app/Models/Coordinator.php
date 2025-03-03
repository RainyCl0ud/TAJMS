<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coordinator extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_id',
        'contact_number',
        'course',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
