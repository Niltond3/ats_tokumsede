<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_cliente');
            $table->text('descricao');
            $table->timestamp('data_criacao');
            $table->date('data_limite');
            $table->enum('status', ['ATIVO', 'INATIVO', 'EXCLUIDO'])->default('ATIVO');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_cliente')
                  ->references('id')
                  ->on('cliente')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
