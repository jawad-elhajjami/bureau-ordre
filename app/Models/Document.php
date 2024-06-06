<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{


    use HasFactory;

    protected $fillable = [
        'order_number',
        'subject',
        'file_path',
        'description',
        'user_id',
        'service_id',
        'category_id',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function category()
    {
        return $this->belongsTo(DocumentCategory::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($document) {
            // Generate the n_ordre field value
            $yearMonth = date('Y_m');
            $latestId = static::latest()->value('id') ?? 0;
            $document->order_number = $yearMonth . '_N' . str_pad($latestId + 1, 3, '0', STR_PAD_LEFT);
        });
    }

}
