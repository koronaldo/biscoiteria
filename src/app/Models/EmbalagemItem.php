<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmbalagemItem extends Model
{
    protected $table = 'embalagem_itens';

    protected $fillable = [
        'embalagem_id',
        'produto_id',
        'quantidade',
    ];

    public function embalagem(): BelongsTo
    {
        return $this->belongsTo(Embalagem::class);
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }
}
