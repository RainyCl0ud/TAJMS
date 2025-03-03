<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'role',
        'profile_picture' => 'profile_pictures/default.png',
        
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function coordinator()
{
    return $this->hasOne(Coordinator::class);
}
public function documents()
{
    return $this->hasMany(Document::class);
}
public function journals()
{
    return $this->hasMany(Journal::class);
}
public function trainee()
{
    return $this->hasOne(Trainee::class);
}
public function attendance()
{
    return $this->hasMany(Attendance::class);
}

public function requests()
{
    return $this->hasMany(Request::class);
}

}