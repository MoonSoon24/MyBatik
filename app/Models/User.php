<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getStatusAttribute()
    {
        return $this->email_verified_at ? 'Verified' : 'Not Verified';
    }

    protected $appends = ['status'];

    protected static function booted()
    {
        static::deleting(function ($user) {
            // delete associate info
            $user->reviews()->delete();
            $user->notifications()->delete();
            $user->designs()->delete();
            $user->likes()->delete();
            $user->comments()->delete();

            // leave order for report
            foreach ($user->orders as $order) {
                $order->id_user = null;
                $order->save();
            }
        });
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id', 'id');
    }

    public function designs(): HasMany
    {
        return $this->hasMany(Design::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'id_user', 'id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)->latest();
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('read', false);
    }

    public function readNotifications()
    {
        return $this->notifications()->where('read', true);
    }
}
