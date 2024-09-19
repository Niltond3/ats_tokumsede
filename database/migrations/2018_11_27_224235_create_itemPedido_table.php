<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemPedidoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('itemPedido', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('idProduto')->index('fk_produto_has_pedido_produto1_idx');
			$table->integer('idPedido')->index('fk_produto_has_pedido_pedido1_idx');
			$table->float('qtd', 10)->nullable();
			$table->float('preco', 10)->nullable();
			$table->float('subtotal', 10)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('itemPedido');
	}

}
