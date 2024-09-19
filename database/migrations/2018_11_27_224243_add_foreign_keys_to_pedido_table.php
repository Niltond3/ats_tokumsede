<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddForeignKeysToPedidoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('pedido', function(Blueprint $table)
		{
			$table->foreign('idAdministrador', 'fk_pedido_administrador1')->references('id')->on('administrador')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('idDistribuidor', 'fk_pedido_distribuidor1')->references('id')->on('distribuidor')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('idEndereco', 'fk_pedido_endereco1')->references('id')->on('enderecoCliente')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('idEntregador', 'fk_pedido_entregador1')->references('id')->on('entregador')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('pedido', function(Blueprint $table)
		{
			$table->dropForeign('fk_pedido_administrador1');
			$table->dropForeign('fk_pedido_distribuidor1');
			$table->dropForeign('fk_pedido_endereco1');
			$table->dropForeign('fk_pedido_entregador1');
		});
	}

}
