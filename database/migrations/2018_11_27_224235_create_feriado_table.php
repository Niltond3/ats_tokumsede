<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeriadoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('feriado', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->date('dataFeriado')->nullable();
			$table->integer('idDistribuidor')->index('fk_feriado_distribuidor1_idx');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('feriado');
	}

}
