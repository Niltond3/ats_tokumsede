<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pedido', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('idDistribuidor')->index('fk_pedido_distribuidor1_idx');
			$table->float('total', 10)->nullable();
			$table->boolean('formaPagamento')->nullable();
			$table->float('trocoPara', 10)->nullable();
			$table->dateTime('horarioPedido')->nullable();
			$table->dateTime('horarioEntrega')->nullable();
			$table->boolean('status')->nullable();
			$table->integer('idEndereco')->index('fk_pedido_endereco1_idx');
			$table->boolean('agendado')->nullable();
			$table->date('dataAgendada')->nullable();
			$table->time('horaInicio')->nullable();
			$table->time('horaFim')->nullable();
			$table->boolean('origem')->nullable();
			$table->text('obs')->nullable();
			$table->text('retorno')->nullable();
			$table->float('taxaEntrega', 10)->nullable();
			$table->boolean('statusChange')->default(0);
			$table->integer('idAdministrador')->nullable()->index('fk_pedido_administrador1_idx');
			$table->integer('idEntregador')->nullable()->index('fk_pedido_entregador1_idx');
			$table->float('distanciaEntrega', 10)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pedido');
	}

}
