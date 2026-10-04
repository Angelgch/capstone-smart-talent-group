<?php

// app/Models/RequestService.php  (DETALLE: un ítem pedido dentro de una solicitud)
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RequestService extends Model
{
    protected $fillable = ['request_id', 'service', 'status', 'detail'];

    public function verificationRequest(): BelongsTo
    {
        return $this->belongsTo(VerificationRequest::class, 'request_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    // Lo que subió el usuario (null si eligió "No enviar documento" o escribió texto)
    public function attachment(): HasOne
    {
        return $this->hasOne(Document::class)->where('type', 'requisito_cliente');
    }

    // PDF final que sube el admin (null mientras no lo suba)
    public function result(): HasOne
    {
        return $this->hasOne(Document::class)->where('type', 'informe_admin');
    }
}
