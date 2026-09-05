<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceitaPacoteEmbalagem extends Model
{
    protected $table = 'receita_pacote_embalagens';

    protected $fillable = [
        'receita_pacote_id',
        'produto_id',
        'quantidade',
    ];

    public function receitaPacote(): BelongsTo
    {
        return $this->belongsTo(ReceitaPacote::class);
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }
}
