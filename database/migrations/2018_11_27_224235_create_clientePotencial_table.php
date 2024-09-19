<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientePotencialTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('clientePotencial', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->float('latitude', 10)->nullable();
			$table->float('longitude', 10)->nullable();
			$table->dateTime('dataAcesso')->nullable();
			$table->string('nome', 80)->nullable();
			$table->string('email', 80)->nullable();
			$table->string('telefone', 20)->nullable();
			$table->string('endereco')->nullable();
			$table->string('numero', 8)->nullable();
			$table->string('bairro', 80)->nullable();
			$table->string('cidade', 80)->nullable();
			$table->string('estado', 2)->nullable();
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
		Schema::drop('clientePotencial');
	}

}
