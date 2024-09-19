<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHorarioFuncionamentoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('horarioFuncionamento', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('inicioSemana')->nullable();
			$table->integer('fimSemana')->nullable();
			$table->integer('inicioSabado')->nullable();
			$table->integer('fimSabado')->nullable();
			$table->boolean('domingo')->nullable();
			$table->integer('inicioDomingo')->nullable();
			$table->integer('fimDomingo')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('horarioFuncionamento');
	}

}
