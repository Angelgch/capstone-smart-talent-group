<?php

// app/Models/Document.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = ['request_service_id', 'kind', 'file_path', 'original_name'];

    public function requestService(): BelongsTo
    {
        return $this->belongsTo(RequestService::class);
    }
}