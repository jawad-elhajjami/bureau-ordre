<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class note extends Model
    {
        use HasFactory;

    protected $fillable = [
        'user_id',
        'document_id',
        'content'
    ];

    public function writer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function document(){
        return $this->belongsTo(Document::class, 'document_id');
    }

}
