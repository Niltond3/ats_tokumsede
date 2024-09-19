<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToItemPedidoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('itemPedido', function(Blueprint $table)
		{
			$table->foreign('idPedido', 'fk_produto_has_pedido_pedido1')->references('id')->on('pedido')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('idProduto', 'fk_produto_has_pedido_produto1')->references('id')->on('produto')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('itemPedido', function(Blueprint $table)
		{
			$table->dropForeign('fk_produto_has_pedido_pedido1');
			$table->dropForeign('fk_produto_has_pedido_produto1');
		});
	}

}
