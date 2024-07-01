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
        Schema::create('item_pedidos', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->decimal('valor', 10, 2);
            $table->integer('quantidade');
            $table->string('id_pedido')->nullable();
            $table->foreign('id_pedido')->references('id')->on('pedidos');
            $table->foreignId('id_produto')->constrained('produtos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_pedidos');
    }
};
