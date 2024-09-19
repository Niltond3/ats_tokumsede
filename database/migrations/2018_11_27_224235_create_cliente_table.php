<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClienteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cliente', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('nome', 80)->nullable();
			$table->string('dddTelefone', 3)->nullable();
			$table->string('telefone', 9)->nullable();
			$table->boolean('sexo')->nullable();
			$table->date('dataNascimento')->nullable();
			$table->string('email', 80)->nullable();
			$table->string('senha')->nullable();
			$table->boolean('tipoPessoa')->nullable();
			$table->string('cpf', 11)->nullable();
			$table->string('cnpj', 14)->nullable();
			$table->boolean('logado')->nullable();
			$table->integer('rating')->nullable();
			$table->boolean('status')->nullable();
			$table->text('regId')->nullable();
			$table->dateTime('ultimoLogin')->nullable();
			$table->string('appVersion', 30)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cliente');
	}

}
