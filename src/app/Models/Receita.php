<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Receita extends Model
{
    use LogsActivity;

    protected $table = 'receitas';

    protected $fillable = [
        'descricao',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Receita')
            ->logOnly(['descricao'])
            ->logOnlyDirty(['descricao'])
            ->dontSubmitEmptyLogs();
    }

    public function itens(): HasMany
    {
        return $this->hasMany(ReceitaItem::class);
    }

    public function tamanhos(): HasMany
    {
        return $this->hasMany(ReceitaTamanho::class);
    }

    public function pacotes(): HasMany
    {
        return $this->hasMany(ReceitaPacote::class);
    }
}
