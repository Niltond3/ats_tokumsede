<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompraTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('compra', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('idDistribuidor')->index('fk_compra_distribuidor1_idx');
			$table->dateTime('dataCompra')->nullable();
			$table->dateTime('dataEntrega')->nullable();
			$table->boolean('status')->nullable();
			$table->float('total', 10)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('compra');
	}

}
