<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Fornecedor extends Model
{
    use LogsActivity;

    protected $table = 'fornecedores';

    protected $fillable = [
        'entidade_id',
        'inscricao_estadual',
        'inscricao_municipal',
        'contato',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Fornecedor')
            ->logOnly(['entidade_id', 'inscricao_estadual', 'inscricao_municipal', 'contato'])
            ->logOnlyDirty(['entidade_id', 'inscricao_estadual', 'inscricao_municipal', 'contato'])
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
            ?? "Fornecedor {$this->id}";
    }

    protected static function booted()
    {
        static::deleting(function (Fornecedor $fornecedor) {
            $fornecedor->entidade()->delete();
        });
    }
}
