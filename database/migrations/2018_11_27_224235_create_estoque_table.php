<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstoqueTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('estoque', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('idDistribuidor')->index('fk_estoque_distribuidor1_idx');
			$table->integer('idProduto')->index('fk_estoque_produto1_idx');
			$table->float('quantidade', 10)->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('estoque');
	}

}
