<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemPedido extends Model
{
    protected $fillable = [
        'id',
        'valor',
        'quantidade',
        'id_pedido',
        'id_produto',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto', 'id');
    }
}
