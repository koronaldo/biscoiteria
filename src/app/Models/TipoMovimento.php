<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TipoMovimento extends Model
{
    use LogsActivity;

    protected $table = 'tipos_movimento';

    protected $fillable = [
        'descricao',
        'tipo',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('TipoMovimento')
            ->logOnly(['descricao', 'tipo'])
            ->logOnlyDirty(['descricao', 'tipo'])
            ->dontSubmitEmptyLogs();
    }

    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class);
    }

    public function contasPagar(): HasMany
    {
        return $this->hasMany(ContaPagar::class);
    }
}
