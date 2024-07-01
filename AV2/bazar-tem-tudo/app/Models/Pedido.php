<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'dataPedido',
        'dataPagamento',
        'moeda',
        'valorTotal',
        'status',
        'id_cliente'
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function itensPedidos()
    {
        return $this->hasMany(ItemPedido::class, 'id_pedido');
    }

    public function movimentacoes()
    {
        return $this->hasMany(Movimentacao::class, 'id_pedido');
    }
}
