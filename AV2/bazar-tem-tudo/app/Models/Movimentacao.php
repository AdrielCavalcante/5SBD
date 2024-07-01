<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movimentacao extends Model
{
    protected $table = 'movimentacoes';

    protected $fillable = [
        'quantidade',
        'dataMovimentacao',
        'id_pedido',
        'id_produto',
    ];

    
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}
