<?php

// app/Models/Company.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Company extends Model
{
    protected $fillable = ['ruc', 'legal_name', 'trade_name', 'address', 'phone'];

    // $company->name = nombre comercial (o la razón social si no tuviera). Así las vistas usan ->name
    public function getNameAttribute(): string
    {
        return $this->trade_name ?: $this->legal_name;
    }

    // Una empresa tiene muchos usuarios
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // Solicitudes de la empresa = solicitudes de sus usuarios (sirve para la matriz del admin)
    public function verificationRequests(): HasManyThrough
    {
        return $this->hasManyThrough(VerificationRequest::class, User::class);
    }
}
