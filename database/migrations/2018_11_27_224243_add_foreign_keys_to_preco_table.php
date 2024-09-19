<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddForeignKeysToPrecoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('preco', function(Blueprint $table)
		{
			$table->foreign('idEstoque', 'fk_preco_estoque1')->references('id')->on('estoque')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('idDistribuidor', 'fk_produto_has_distribuidor_distribuidor1')->references('id')->on('distribuidor')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('idProduto', 'fk_produto_has_distribuidor_produto1')->references('id')->on('produto')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('preco', function(Blueprint $table)
		{
			$table->dropForeign('fk_preco_estoque1');
			$table->dropForeign('fk_produto_has_distribuidor_distribuidor1');
			$table->dropForeign('fk_produto_has_distribuidor_produto1');
		});
	}

}
