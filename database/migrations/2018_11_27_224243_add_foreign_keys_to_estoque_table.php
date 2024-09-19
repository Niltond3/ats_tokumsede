<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToEstoqueTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('estoque', function(Blueprint $table)
		{
			$table->foreign('idDistribuidor', 'fk_estoque_distribuidor1')->references('id')->on('distribuidor')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('idProduto', 'fk_estoque_produto1')->references('id')->on('produto')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('estoque', function(Blueprint $table)
		{
			$table->dropForeign('fk_estoque_distribuidor1');
			$table->dropForeign('fk_estoque_produto1');
		});
	}

}
