<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function members()
    {
        return $this->hasMany(User::class);
    }

    public function documents(){
        return $this->hasMany(Document::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($service) {
            User::where('service_id', $service->id)->update(['service_id' => null]);
        });

    }
    
    

}
