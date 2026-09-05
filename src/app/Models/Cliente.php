<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Cliente extends Model
{
    use LogsActivity;

    protected $table = 'clientes';

    protected $fillable = [
        'entidade_id',
        'contato',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Cliente')
            ->logOnly(['entidade_id', 'contato'])
            ->logOnlyDirty(['entidade_id', 'contato'])
            ->dontSubmitEmptyLogs();
    }

    public function entidade()
    {
        return $this->belongsTo(Entidade::class)
            ->withDefault();
    }

    public function getNomeExibicaoAttribute(): string
    {
        return $this->entidade?->nome_social
            ?? $this->entidade?->nome
            ?? "Cliente {$this->id}";
    }

    protected static function booted()
    {
        static::deleting(function (Cliente $cliente) {
            $cliente->entidade()->delete();
        });
    }
}
