<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompraItem extends Model
{
    protected $table = 'compra_itens';

    protected $fillable = [
        'compra_id',
        'produto_id',
        'quantidade',
        'valor_unitario',
        'valor_desconto',
        'valor_acrescimo',
        'valor_total',
    ];

    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class);
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }
}
