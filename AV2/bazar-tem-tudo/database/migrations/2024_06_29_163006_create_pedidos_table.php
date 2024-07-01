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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->date('dataPedido');
            $table->date('dataPagamento')->nullable();
            $table->string('moeda');
            $table->decimal('valorTotal', 10, 2);
            $table->string('status');
            $table->foreignId('id_cliente')->constrained('clientes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
