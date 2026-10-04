<?php

// app/Models/User.php   (REEMPLAZA el anterior)
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // OJO: "role" NO está aquí a propósito. Así nadie puede hacerse admin mandando
    // role=admin en un formulario. El registro siempre crea role = 'user' (valor por defecto de la BD).
    // company_id solo lo asigna el backend (AuthController), nunca viene del formulario.
    protected $fillable = [
        'company_id',
        'dni',
        'names',
        'surnames',
        'email',
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

    // La empresa a la que pertenece (null si es el admin)
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Solicitudes que creó este usuario
    public function verificationRequests(): HasMany
    {
        return $this->hasMany(VerificationRequest::class);
    }

    // Archivos que subió este usuario
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
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
}
