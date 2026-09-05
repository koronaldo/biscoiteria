<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Compra extends Model
{
    use LogsActivity;

    protected $table = 'compras';

    protected $fillable = [
        'fornecedor_id',
        'tipo_movimento_id',
        'numero_nota',
        'data_emissao',
        'data_compra',
        'valor_nota',
    ];

    protected $casts = [
        'data_emissao' => 'date',
        'data_compra' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Compra')
            ->logOnly(['fornecedor_id', 'tipo_movimento_id', 'numero_nota', 'data_emissao', 'data_compra', 'valor_nota'])
            ->logOnlyDirty(['fornecedor_id', 'tipo_movimento_id', 'numero_nota', 'data_emissao', 'data_compra', 'valor_nota'])
            ->dontSubmitEmptyLogs();
    }

    public function fornecedor(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class);
    }

    public function tipoMovimento(): BelongsTo
    {
        return $this->belongsTo(TipoMovimento::class);
    }

    public function itens(): HasMany
    {
        return $this->hasMany(CompraItem::class);
    }

    public function parcelas(): HasMany
    {
        return $this->hasMany(ContaPagar::class);
    }
}
