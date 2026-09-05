<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class UserGroup extends Model
{
    use LogsActivity;

    protected $table = 'user_groups';

    protected $fillable = [
        'descricao',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('GrupoUsuario')
            ->logOnly(['id', 'descricao'])
            ->logOnlyDirty(['descricao'])
            ->dontSubmitEmptyLogs();
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_user_group')
            ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_user_group')
            ->withTimestamps();
    }
}
