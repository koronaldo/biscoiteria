<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReceitaTamanho extends Model
{
    protected $table = 'receita_tamanhos';

    protected $fillable = [
        'receita_id',
        'descricao',
        'quantidade_produzida',
    ];

    public function receita(): BelongsTo
    {
        return $this->belongsTo(Receita::class);
    }

    public function pacotes(): HasMany
    {
        return $this->hasMany(ReceitaPacote::class);
    }

    public function getDescricaoCompletaAttribute(): string
    {
        return trim(($this->receita?->descricao ?? '').' - '.$this->descricao, ' -');
    }
}
