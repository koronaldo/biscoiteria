<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Empresa extends Model
{
    use LogsActivity;

    protected $table = 'empresas';

    protected $fillable = [
        'entidade_id',
        'logo',
        'inscricao_estadual',
        'inscricao_municipal',
        'percentual_lucro',
        'percentual_maodeobra',
        'percentual_despesa_fixa',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Empresa')
            ->logOnly(['entidade_id', 'inscricao_estadual', 'inscricao_municipal', 'percentual_lucro', 'percentual_maodeobra', 'percentual_despesa_fixa'])
            ->logOnlyDirty(['entidade_id', 'inscricao_estadual', 'inscricao_municipal', 'percentual_lucro', 'percentual_maodeobra', 'percentual_despesa_fixa'])
            ->dontSubmitEmptyLogs();
    }

    public function entidade()
    {
        return $this->belongsTo(Entidade::class)
            ->withDefault();
    }

    public function funcionarios()
    {
        return $this->belongsToMany(Funcionario::class, 'empresa_funcionario')
            ->withTimestamps();
    }

    public function getNomeExibicaoAttribute(): string
    {
        return $this->entidade?->nome_social
            ?? $this->entidade?->nome
            ?? "Empresa {$this->id}";
    }

    protected static function booted()
    {
        static::deleting(function (Empresa $empresa) {
            $empresa->entidade()->delete();
        });
    }
}
