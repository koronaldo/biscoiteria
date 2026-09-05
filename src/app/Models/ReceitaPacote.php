<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReceitaPacote extends Model
{
    protected $table = 'receita_pacotes';

    protected $fillable = [
        'receita_id',
        'receita_tamanho_id',
        'descricao',
        'quantidade_por_pacote',
    ];

    public function receita(): BelongsTo
    {
        return $this->belongsTo(Receita::class);
    }

    public function tamanho(): BelongsTo
    {
        return $this->belongsTo(ReceitaTamanho::class, 'receita_tamanho_id');
    }

    public function embalagens(): HasMany
    {
        return $this->hasMany(ReceitaPacoteEmbalagem::class);
    }

    public function getDescricaoCompletaAttribute(): string
    {
        return trim(($this->receita?->descricao ?? '').' - '.$this->descricao, ' -');
    }
}
