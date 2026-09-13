<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Pedido extends Model
{
    use LogsActivity;

    protected $table = 'pedidos';

    protected $fillable = [
        'cliente_id',
        'data_pedido',
        'desconto',
        'valor_total',
    ];

    protected $casts = [
        'data_pedido' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Pedido')
            ->logOnly(['cliente_id', 'data_pedido', 'desconto', 'valor_total'])
            ->logOnlyDirty(['cliente_id', 'data_pedido', 'desconto', 'valor_total'])
            ->dontSubmitEmptyLogs();
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function itens(): HasMany
    {
        return $this->hasMany(PedidoItem::class);
    }
}
