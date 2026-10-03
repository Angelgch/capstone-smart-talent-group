<?php

// app/Models/Company.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Company extends Model
{
    protected $fillable = ['name', 'ruc'];

    // Usuarios de la empresa: se unen por RUC (no hay FK porque el RUC del registro es libre por ahora)
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'ruc', 'ruc');
    }

    // Solicitudes de la empresa = solicitudes de sus usuarios (sirve para filtrar la matriz del admin)
    public function verificationRequests(): HasManyThrough
    {
        return $this->hasManyThrough(VerificationRequest::class, User::class, 'ruc', 'user_id', 'ruc', 'id');
    }
}