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
        'recipient_id',
        'category_id',
        'otp_code',
        'requires_otp'
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

    public function recipient()  // Add this method
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    protected static function boot()
    {
        parent::boot();
    }

}
