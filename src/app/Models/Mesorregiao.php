<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mesorregiao extends Model
{
    protected $table = 'mesorregioes';

    protected $fillable = [
        'estado_id',
        'nome',
        'codigo_ibge',
    ];

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function microrregioes(): HasMany
    {
        return $this->hasMany(Microrregiao::class, 'mesorregiao_id');
    }
}
