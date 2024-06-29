<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'SKU',
        'UPC'
    ];

    public function estoques()
    {
        return $this->hasMany(Estoque::class, 'id_produto');
    }

    public function movimentacoes()
    {
        return $this->hasMany(Movimentacao::class, 'id_produto');
    }

    public function itensPedidos()
    {
        return $this->hasMany(ItemPedido::class, 'id_produto');
    }

    public function compras()
    {
        return $this->hasMany(Compra::class, 'id_produto');
    }
}
