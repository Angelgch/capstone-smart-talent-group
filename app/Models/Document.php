<?php

// app/Models/Document.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = ['request_id', 'request_service_id', 'user_id', 'type', 'file_path', 'original_name'];

    public function verificationRequest(): BelongsTo
    {
        return $this->belongsTo(VerificationRequest::class, 'request_id');
    }

    public function requestService(): BelongsTo
    {
        return $this->belongsTo(RequestService::class);
    }

    // Quién lo subió
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
