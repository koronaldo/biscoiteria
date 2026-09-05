<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class UnidadeMedida extends Model
{
    use LogsActivity;

    protected $table = 'unidades_medida';

    protected $fillable = [
        'descricao',
        'sigla',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('UnidadeMedida')
            ->logOnly(['descricao', 'sigla'])
            ->logOnlyDirty(['descricao', 'sigla'])
            ->dontSubmitEmptyLogs();
    }

    public function produtos(): HasMany
    {
        return $this->hasMany(Produto::class);
    }
}
