<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Regiao extends Model
{
    protected $table = 'regioes';

    protected $fillable = [
        'nome',
        'codigo_ibge',
    ];

    public function estados(): HasMany
    {
        return $this->hasMany(Estado::class, 'regiao_id');
    }
}
