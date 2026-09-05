<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Telefone extends Model
{
    use LogsActivity;

    protected $table = 'telefones';

    protected $fillable = [
        'numero',
        'tipo',
        'principal',
        'whatsapp',
    ];

    protected $casts = [
        'principal' => 'boolean',
        'whatsapp' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Telefone')
            ->logOnly(['id', 'numero', 'tipo', 'principal', 'whatsapp'])
            ->logOnlyDirty(['numero', 'tipo', 'principal', 'whatsapp'])
            ->dontSubmitEmptyLogs();
    }

    public function telefonavel()
    {
        return $this->morphTo();
    }
}
