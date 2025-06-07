<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'bio',
        'username',
        'password',
        'is_private'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $timestamps = false;
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

    public static function whereName(string $name): Builder
    {
        return static::where('username', $name);
    }

    public function posts(): HasMany {
        return $this->hasMany(Posts::class, "user_id", "id");
    }

    public function following() {
        return $this->belongsToMany(User::class, 'follow', 'follower_id', 'following_id')
                    ->withPivot('is_accepted');
    }

    public function followingList() {
        return $this->hasMany(Follow::class, 'follower_id', 'id');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follow', 'following_id', 'follower_id')
                    ->withPivot('is_accepted');
    }

    public function isYourAccount() {
        return FacadesAuth::user()->id === $this->id ? true : false;
    }

    public function followingStatus(): string 
    {
        $follow = Follow::where([
            'follower_id' => Auth::id(),
            'following_id' => $this->id
        ])->first();

        return $follow ? ($follow->is_accepted ? 'following' : 'requested') : 'not-following';
    }
}
