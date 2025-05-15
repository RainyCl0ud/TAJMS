<?php

namespace App\Models;

use App\Events\NewRequestNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'coordinator_id',
        'type',
        'date',
        'time',
        'reason',
        'image',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($request) {
            try {
                $notification = Notification::create([
                    'user_id' => $request->user_id,
                    'request_id' => $request->id,
                    'type' => $request->type
                ]);

                broadcast(new NewRequestNotification($notification))->toOthers();
            } catch (\Exception $e) {
                // Log the error but don't stop the request from being created
                Log::error('Failed to create notification: ' . $e->getMessage(), [
                    'request_id' => $request->id,
                    'user_id' => $request->user_id
                ]);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }
}
