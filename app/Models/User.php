<?php

// app/Models/User.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // OJO: "role" NO está aquí a propósito. Así nadie puede hacerse admin mandando
    // role=admin en un formulario. El registro siempre crea role = 'user' (valor por defecto de la BD).
    protected $fillable = [
        'dni',
        'names',
        'surnames',
        'email',
        'ruc',
        'phone',
        'password',
        'terms_accepted_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'terms_accepted_at' => 'datetime',
            'password'          => 'hashed', // se encripta sola al guardar
        ];
    }

    // Nombre completo: así auth()->user()->name sigue funcionando en tu topbar
    public function getNameAttribute(): string
    {
        return trim($this->names . ' ' . $this->surnames);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Solicitudes que creó este usuario
    public function verificationRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(VerificationRequest::class);
    }
}