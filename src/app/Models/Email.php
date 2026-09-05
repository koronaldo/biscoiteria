<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Email extends Model
{
    use LogsActivity;

    protected $table = 'emails';

    protected $fillable = [
        'email',
        'tipo',
        'principal',
        'verificado',
    ];

    protected $casts = [
        'principal' => 'boolean',
        'verificado' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Email')
            ->logOnly(['email', 'tipo', 'principal', 'verificado'])
            ->logOnlyDirty(['email', 'tipo', 'principal', 'verificado'])
            ->dontSubmitEmptyLogs();
    }

    public function emailavel()
    {
        return $this->morphTo();
    }
}
