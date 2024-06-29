<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estoque extends Model
{
    protected $fillable = [
        'quantidade',
        'id_produto',
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}
