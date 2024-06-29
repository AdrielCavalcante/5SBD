<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('enderecos', function (Blueprint $table) {
            $table->id();
            $table->string('nivel_servico_envio');
            $table->string('endereco_envio_linha1');
            $table->string('endereco_envio_linha2')->nullable();
            $table->string('endereco_envio_linha3')->nullable();
            $table->string('cidade_envio');
            $table->string('estado_envio');
            $table->string('codigo_postal_envio');
            $table->string('pais_envio');
            $table->foreignId('id_cliente')->constrained('clientes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enderecos');
    }
};
