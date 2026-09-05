<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ContaPagar extends Model
{
    use LogsActivity;

    protected $table = 'contas_pagar';

    protected $fillable = [
        'compra_id',
        'tipo_movimento_id',
        'fornecedor_id',
        'descricao',
        'numero_parcela',
        'data_vencimento',
        'valor',
        'pago',
        'data_pagamento',
        'valor_pago',
    ];

    protected $casts = [
        'data_vencimento' => 'date',
        'data_pagamento' => 'date',
        'pago' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('ContaPagar')
            ->logOnly(['compra_id', 'tipo_movimento_id', 'fornecedor_id', 'descricao', 'numero_parcela', 'data_vencimento', 'valor', 'pago', 'data_pagamento', 'valor_pago'])
            ->logOnlyDirty(['compra_id', 'tipo_movimento_id', 'fornecedor_id', 'descricao', 'numero_parcela', 'data_vencimento', 'valor', 'pago', 'data_pagamento', 'valor_pago'])
            ->dontSubmitEmptyLogs();
    }

    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class);
    }

    public function tipoMovimento(): BelongsTo
    {
        return $this->belongsTo(TipoMovimento::class);
    }

    public function fornecedor(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class);
    }

    public function getDescricaoExibicaoAttribute(): string
    {
        return $this->descricao
            ?? $this->fornecedor?->entidade?->nome
            ?? ($this->compra ? "Compra NF {$this->compra->numero_nota}" : "Conta a Pagar #{$this->id}");
    }
}
