<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Menu extends Model
{
    use LogsActivity;

    protected $fillable = [
        'title',
        'icon',
        'route',
        'permission',
        'parent_id',
        'sort',
        'active',
        'admin',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Menu')
            ->logOnly(['id', 'title', 'icon', 'route', 'permission', 'parent_id', 'sort', 'active', 'admin'])
            ->logOnlyDirty(['title', 'icon', 'route', 'permission', 'parent_id', 'sort', 'active', 'admin'])
            ->dontSubmitEmptyLogs();
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('sort');
    }

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function groups()
    {
        return $this->belongsToMany(UserGroup::class, 'menu_user_group');
    }
}
