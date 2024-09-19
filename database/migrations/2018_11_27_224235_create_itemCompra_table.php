<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemCompraTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('itemCompra', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('idCompra')->index('fk_compra_has_produto_compra1_idx');
			$table->integer('idProduto')->index('fk_compra_has_produto_produto1_idx');
			$table->float('quantidade', 10)->nullable();
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
		Schema::drop('itemCompra');
	}

}
