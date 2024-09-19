<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToEnderecoClienteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('enderecoCliente', function(Blueprint $table)
		{
			$table->foreign('idCliente', 'fk_endereco_cliente1')->references('id')->on('cliente')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('enderecoCliente', function(Blueprint $table)
		{
			$table->dropForeign('fk_endereco_cliente1');
		});
	}

}
