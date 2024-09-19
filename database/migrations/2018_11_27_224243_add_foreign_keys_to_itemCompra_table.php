<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToItemCompraTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('itemCompra', function(Blueprint $table)
		{
			$table->foreign('idCompra', 'fk_compra_has_produto_compra1')->references('id')->on('compra')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('idProduto', 'fk_compra_has_produto_produto1')->references('id')->on('produto')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('itemCompra', function(Blueprint $table)
		{
			$table->dropForeign('fk_compra_has_produto_compra1');
			$table->dropForeign('fk_compra_has_produto_produto1');
		});
	}

}
