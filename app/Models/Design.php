<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Design extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'image_path',
        'is_public',
    ];

    /**
     * Get the user that owns the design.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the comments for the design.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->latest(); // Show newest comments first
    }

    /**
     * Get all of the likes for the design.
     */
    public function likes()
    {
        // This is a polymorphic relationship
        return $this->morphMany(Like::class, 'likeable');
    }
}