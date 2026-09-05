<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Microrregiao extends Model
{
    protected $table = 'microrregioes';

    protected $fillable = [
        'mesorregiao_id',
        'nome',
        'codigo_ibge',
    ];

    public function mesorregiao(): BelongsTo
    {
        return $this->belongsTo(Mesorregiao::class, 'mesorregiao_id');
    }

    public function cidades(): HasMany
    {
        return $this->hasMany(Cidade::class, 'microrregiao_id');
    }
}
