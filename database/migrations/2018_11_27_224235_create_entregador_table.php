<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntregadorTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('entregador', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('idDistribuidor')->index('fk_entregador_distribuidor1_idx');
			$table->string('nome', 100)->nullable();
			$table->string('placaVeiculo', 10)->nullable();
			$table->string('telefone', 45)->nullable();
			$table->boolean('status')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('entregador');
	}

}
