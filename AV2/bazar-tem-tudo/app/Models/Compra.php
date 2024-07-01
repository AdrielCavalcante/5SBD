<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $fillable = [
        'quantidade',
        'data_compra',
        'id_produto',
        'comprado'
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}
