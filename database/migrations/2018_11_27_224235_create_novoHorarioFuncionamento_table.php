<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNovoHorarioFuncionamentoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('novoHorarioFuncionamento', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->boolean('domingo')->nullable();
			$table->time('inicioDomingo')->nullable();
			$table->time('fimDomingo')->nullable();
			$table->boolean('segunda')->nullable();
			$table->time('inicioSegunda')->nullable();
			$table->time('fimSegunda')->nullable();
			$table->boolean('terca')->nullable();
			$table->time('inicioTerca')->nullable();
			$table->time('fimTerca')->nullable();
			$table->boolean('quarta')->nullable();
			$table->time('inicioQuarta')->nullable();
			$table->time('fimQuarta')->nullable();
			$table->boolean('quinta')->nullable();
			$table->time('inicioQuinta')->nullable();
			$table->time('fimQuinta')->nullable();
			$table->boolean('sexta')->nullable();
			$table->time('inicioSexta')->nullable();
			$table->time('fimSexta')->nullable();
			$table->boolean('sabado')->nullable();
			$table->time('inicioSabado')->nullable();
			$table->time('fimSabado')->nullable();
			$table->boolean('pausaAlmoco')->nullable();
			$table->time('inicioAlmoco')->nullable();
			$table->time('fimAlmoco')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('novoHorarioFuncionamento');
	}

}
