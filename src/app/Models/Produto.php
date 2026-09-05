<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Produto extends Model
{
    use LogsActivity;

    protected $table = 'produtos';

    protected $fillable = [
        'descricao',
        'unidade_medida_id',
        'medida',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Produto')
            ->logOnly(['descricao', 'unidade_medida_id', 'medida'])
            ->logOnlyDirty(['descricao', 'unidade_medida_id', 'medida'])
            ->dontSubmitEmptyLogs();
    }

    public function unidadeMedida(): BelongsTo
    {
        return $this->belongsTo(UnidadeMedida::class);
    }
}
