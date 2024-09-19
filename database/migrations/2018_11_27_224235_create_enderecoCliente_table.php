<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnderecoClienteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('enderecoCliente', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('logradouro')->nullable();
			$table->string('numero', 100)->nullable();
			$table->string('bairro', 100)->nullable();
			$table->string('complemento', 100)->nullable();
			$table->string('cep', 8)->nullable();
			$table->string('cidade', 100)->nullable();
			$table->string('estado', 2)->nullable();
			$table->string('referencia', 100)->nullable();
			$table->string('apelido', 6)->nullable();
			$table->boolean('atual')->nullable();
			$table->integer('idCliente')->index('fk_endereco_cliente1_idx');
			$table->float('latitude', 10)->nullable();
			$table->float('longitude', 10)->nullable();
			$table->boolean('status')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('enderecoCliente');
	}

}
