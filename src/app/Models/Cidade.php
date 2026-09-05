<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cidade extends Model
{
    protected $table = 'cidades';

    protected $fillable = [
        'microrregiao_id',
        'estado_id',
        'nome',
        'codigo_ibge',
    ];

    public function microrregiao(): BelongsTo
    {
        return $this->belongsTo(Microrregiao::class, 'microrregiao_id');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }
}
