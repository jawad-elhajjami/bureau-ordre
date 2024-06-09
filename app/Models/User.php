<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'color',
        'service_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function role(){
        return $this->belongsTo(Role::class);
    }

    public function service(){
        return $this->belongsTo(Service::class);
    }


    /**
     * Get the initials of the user's name.
     *
     * @return string
     */
    public function getInitialsAttribute()
    {
        $nameParts = explode(' ', $this->name);
        $initials = '';

        foreach ($nameParts as $part) {
            $initials .= strtoupper($part[0]);
        }

        return $initials;
    }
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->color = self::generateRandomColor();
        });
        static::deleting(function ($user) {
            Document::where('recipient_id', $user->id)->update(['recipient_id' => null]);
        });
    }

    private static function generateRandomColor()
    {
        $colors = [
            '#2196F3', // Light Blue
            '#4CAF50', // Light Green
            '#F44336', // Light Red
            '#FF9800', // Light Orange
            '#9C27B0', // Light Purple
            '#009688', // Light Teal
            '#FFEB3B', // Light Yellow
            '#E91E63', // Light Pink
            '#00BCD4', // Light Cyan
            '#FFC107', // Light Amber
            '#3F51B5', // Indigo
            '#795548', // Brown
            '#673AB7', // Deep Purple
            '#FF5722', // Deep Orange
            '#607D8B', // Blue Grey
            '#CDDC39', // Lime
            '#8BC34A', // Light Green
            '#03A9F4', // Light Blue
            '#9E9E9E', // Grey
            '#FF5252', // Red
        ];
        return $colors[array_rand($colors)];
    }


}
