<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Entidade extends Model
{
    use LogsActivity;

    protected $table = 'entidades';

    protected $fillable = [
        'nome',
        'nome_social',
        'documento',
        'observacoes',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Entidade')
            ->logOnly(['nome', 'nome_social', 'documento', 'observacoes'])
            ->logOnlyDirty(['nome', 'nome_social', 'documento', 'observacoes'])
            ->dontSubmitEmptyLogs();
    }

    public function enderecos()
    {
        return $this->morphMany(Endereco::class, 'enderecavel');
    }

    public function telefones()
    {
        return $this->morphMany(Telefone::class, 'telefonavel');
    }

    public function emails()
    {
        return $this->morphMany(Email::class, 'emailavel');
    }

    public function empresa()
    {
        return $this->hasOne(Empresa::class);
    }

    public function funcionario()
    {
        return $this->hasOne(Funcionario::class);
    }

    public function fornecedor()
    {
        return $this->hasOne(Fornecedor::class);
    }

    public function cliente()
    {
        return $this->hasOne(Cliente::class);
    }
}
