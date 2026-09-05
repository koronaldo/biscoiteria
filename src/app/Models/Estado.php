<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estado extends Model
{
    protected $table = 'estados';

    protected $fillable = [
        'regiao_id',
        'nome',
        'sigla',
        'codigo_ibge',
    ];

    public function regiao(): BelongsTo
    {
        return $this->belongsTo(Regiao::class, 'regiao_id');
    }

    public function mesorregioes(): HasMany
    {
        return $this->hasMany(Mesorregiao::class, 'estado_id');
    }

    public function cidades(): HasMany
    {
        return $this->hasMany(Cidade::class, 'estado_id');
    }
}
