<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('pedidos', function (Blueprint $table) {
            DB::statement("ALTER TABLE pedidos MODIFY COLUMN status ENUM('Pendente', 'Em andamento', 'Concluído', 'Faltando produto', 'Compra Realizada') DEFAULT 'Pendente'");
        });
    }

    public function down()
    {
        Schema::table('pedidos', function (Blueprint $table) {
            DB::statement("ALTER TABLE pedidos MODIFY COLUMN status ENUM('Pendente', 'Em andamento', 'Concluído')");
        });
    }
};
