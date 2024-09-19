<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTipoTableDistribuidor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('distribuidor', function (Blueprint $table) {
            $table->string('tipoDistribuidor', '45') // Nome da coluna
                    ->nullable(); // Preenchimento não obrigatório
            $table->integer('idDistribuidor') // Nome da coluna
                    ->nullable(); // Preenchimento não obrigatório
            $table->foreign('idDistribuidor')->references('id')->on('distribuidor');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('distribuidor', function (Blueprint $table) {
            $table->dropColumn('tipoDistribuidor');
            $table->dropForeign(['idDistribuidor']);
        });
    }
}
