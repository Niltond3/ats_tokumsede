<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnderecoDistribuidorTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('enderecoDistribuidor', function(Blueprint $table)
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
			$table->float('latitude', 10)->nullable();
			$table->float('longitude', 10)->nullable();
			$table->float('distanciaMaxima', 10)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('enderecoDistribuidor');
	}

}
