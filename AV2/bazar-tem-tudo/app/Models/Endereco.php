<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Endereco extends Model
{
    use HasFactory;

    protected $fillable = [
        'nivel_servico_envio',
        'endereco_envio_linha1',
        'endereco_envio_linha2',
        'endereco_envio_linha3',
        'cidade_envio',
        'estado_envio',
        'codigo_postal_envio',
        'pais_envio',
        'id_cliente',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }
}
