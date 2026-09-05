<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Endereco extends Model
{
    use LogsActivity;

    protected $fillable = [
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade_id',
        'tipo',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Endereco')
            ->logOnly(['cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade_id', 'tipo'])
            ->logOnlyDirty(['cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade_id', 'tipo'])
            ->dontSubmitEmptyLogs();
    }

    public function enderecavel()
    {
        return $this->morphTo();
    }

    public function cidade()
    {
        return $this->belongsTo(Cidade::class);
    }
}
