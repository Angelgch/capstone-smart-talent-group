<?php

// app/Models/VerificationRequest.php  (CABECERA de la solicitud; tabla "requests")
namespace App\Models;

use App\Support\ServiceCatalog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
///recien añadido este en la rama prueba: use Illuminate\Database\Eloquent\Builder;

class VerificationRequest extends Model
{
    protected $table = 'requests';   // la tabla se llama "requests"
    //recien añadido public const STATUSES = ['en_espera', 'en_progreso', 'realizado', 'cancelado'];
    public const STATUSES = ['en_espera', 'en_progreso', 'realizado', 'cancelado'];
    //fin
    protected $fillable = [
        'user_id', 'dni', 'names', 'surnames', 'email', 'phone', 'observations', 'status', 'address', 'reference'
    ]; //address y reference añadidos en la rama prueba

    // Responsable: quien registró la solicitud
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Detalle: servicios + dirección/referencia (clave foránea: request_id)
    public function services(): HasMany
    {
        return $this->hasMany(RequestService::class, 'request_id');
    }

    // Todos los archivos de la solicitud
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'request_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->names . ' ' . $this->surnames);
    }

    // N° de solicitud para mostrar: SOL-00012
    public function getCodeAttribute(): string
    {
        return 'SOL-' . str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }

    // El ítem pedido con esa clave (o null si no se pidió). Ej: $solicitud->item('laborales')
    public function item(string $key): ?RequestService
    {
        return $this->services->firstWhere('service', $key);
    }

    // Recalcula el estado general. Cuenta SOLO los servicios (dirección/referencia no influyen).
    public function refreshStatus(): void
    {
        $st = $this->services()->whereIn('service', ServiceCatalog::keys())->pluck('status');
        if ($st->isEmpty()) return;

        if ($st->every(fn ($s) => $s === 'cancelado')) {
            $new = 'cancelado';
        } elseif ($st->every(fn ($s) => in_array($s, ['realizado', 'cancelado']))) {
            $new = 'realizado';
        } elseif ($st->contains(fn ($s) => in_array($s, ['en_progreso', 'realizado']))) {
            $new = 'en_progreso';
        } else {
            $new = 'en_espera';
        }

        $this->update(['status' => $new]);
    }
    //Function para filtrar por busqueda y estado de la solicitud
    public function scopeSearch(Builder $query, ?string $q, ?string $status): Builder
    {
    return $query
        ->when($q, function (Builder $query, string $q) {
            $like = '%' . $q . '%';
            $query->where(fn ($w) => $w
                ->where('requests.dni', 'like', $like)
                ->orWhere('requests.names', 'like', $like)
                ->orWhere('requests.surnames', 'like', $like));
        })
        ->when(in_array($status, self::STATUSES, true), fn (Builder $query) => $query->where('requests.status', $status));
    }
    //fin de la funcion
}
