<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Embalagem extends Model
{
    use LogsActivity;

    protected $table = 'embalagens';

    protected $fillable = [
        'descricao',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Embalagem')
            ->logOnly(['descricao'])
            ->logOnlyDirty(['descricao'])
            ->dontSubmitEmptyLogs();
    }

    public function itens(): HasMany
    {
        return $this->hasMany(EmbalagemItem::class);
    }
}
