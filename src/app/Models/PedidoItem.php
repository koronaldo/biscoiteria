<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoItem extends Model
{
    protected $table = 'pedido_itens';

    protected $fillable = [
        'pedido_id',
        'receita_tamanho_id',
        'receita_pacote_id',
        'forma_venda',
        'quantidade',
        'preco_unitario',
        'valor_total',
    ];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function receitaTamanho(): BelongsTo
    {
        return $this->belongsTo(ReceitaTamanho::class);
    }

    public function receitaPacote(): BelongsTo
    {
        return $this->belongsTo(ReceitaPacote::class);
    }
}
