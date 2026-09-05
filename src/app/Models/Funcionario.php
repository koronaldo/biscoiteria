<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Funcionario extends Model
{
    use LogsActivity;

    protected $table = 'funcionarios';

    protected $fillable = [
        'entidade_id',
        'apelido',
        'cargo',
        'rg',
        'valor_salario',
        'bloqueado',
        'user_id',
        'empresa_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Funcionario')
            ->logOnly(['id', 'entidade_id', 'apelido', 'cargo', 'rg', 'valor_salario', 'bloqueado', 'user_id', 'empresa_id'])
            ->logOnlyDirty(['entidade_id', 'apelido', 'cargo', 'rg', 'valor_salario', 'bloqueado', 'user_id', 'empresa_id'])
            ->dontSubmitEmptyLogs();
    }

    public function entidade()
    {
        return $this->belongsTo(Entidade::class)
            ->withDefault();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function empresas()
    {
        return $this->belongsToMany(Empresa::class, 'empresa_funcionario')
            ->withTimestamps();
    }

    public function empresaPadrao()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function getNomeExibicaoAttribute(): string
    {
        return $this->entidade?->nome_social
            ?? $this->entidade?->nome
            ?? "Funcionário {$this->id}";
    }

    protected static function booted()
    {
        static::deleting(function (Funcionario $funcionario) {
            $funcionario->entidade()->delete();
        });
    }
}
