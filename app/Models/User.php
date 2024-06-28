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

    public function getIsAdminAttribute(){
        return $this->role_id ===  1;
    }

    public function readDocuments(){
        return $this->belongsToMany(Document::class)->withPivot('read_at')->withTimestamps();
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
            '#FFB3BA', // Pastel Pink
            '#BAFFC9', // Pastel Green
            '#BAE1FF', // Pastel Blue
            '#FFFFBA', // Pastel Yellow
            '#FFDFBA', // Pastel Orange
            '#E6BAFF', // Pastel Purple
            '#C1FFC1', // Pastel Mint
            '#FFC9DE', // Pastel Rose
            '#C9FFFF', // Pastel Cyan
            '#F0E68C', // Pastel Khaki
            '#DDA0DD', // Pastel Plum
            '#E0FFFF', // Pastel Light Cyan
            '#FFDAB9', // Pastel Peach
            '#D8BFD8', // Pastel Thistle
            '#AFEEEE', // Pastel Turquoise
            '#FFE4E1', // Pastel Misty Rose
            '#F0FFF0', // Pastel Honeydew
            '#F5F5DC', // Pastel Beige
            '#FFF0F5', // Pastel Lavender
            '#F0F8FF', // Pastel Alice Blue
        ];
        return $colors[array_rand($colors)];
    }


}
