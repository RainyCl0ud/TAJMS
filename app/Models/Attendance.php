<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'in_time',
        'out_time',
        'rendered_hours',
        'image',
    ];

    protected $casts = [
        'in_time' => 'datetime',
        'out_time' => 'datetime',
        'rendered_hours' => 'decimal:2'
    ];

    public function calculateRenderedHours()
    {
        if (!$this->in_time || !$this->out_time) {
            return 0;
        }

        $minutes = $this->in_time->diffInMinutes($this->out_time);
        
        // Deduct 1 hour break if work duration is more than 5 hours
        if ($minutes > 300) { // 5 hours * 60 minutes
            $minutes -= 60; // Deduct 60 minutes for break
        }
        
        return round($minutes / 60, 2);
    }
   
    public function getStatusAttribute()
    {
        $now = Carbon::now('Asia/Manila');
        $inTime = $this->in_time ? $this->in_time->copy() : null;
        $outTime = $this->out_time ? $this->out_time->copy() : null;
    
        // Completed: Time in and time out within 1 minute
        if ($inTime && $outTime && $inTime->diffInHours($outTime) <= 24) {
            return 'Completed';
        }
    
        // Missing: Time in but no time out after 1 minute
        if ($inTime && !$outTime && $inTime->diffInHours($now) > 24) {
            return 'Missing';
        }
    
        // Working: Time in but no time out within 1 minute
        if ($inTime && !$outTime && $inTime->diffInHours($now) <= 24) {
            return 'Working';
        }
    
        // Absent: No time in recorded
        if (!$inTime) {
            return 'Absent';
        }
    
        return 'Error';
    }
      
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
