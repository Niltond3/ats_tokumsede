<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistribuidorTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('distribuidor', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('nome', 200)->nullable();
			$table->string('cnpj', 18)->nullable();
			$table->string('email', 80)->nullable();
			$table->string('dddTelefone', 2)->nullable();
			$table->string('telefonePrincipal', 10)->nullable();
			$table->string('outrosContatos')->nullable();
			$table->boolean('status')->nullable();
			$table->integer('idEnderecoDistribuidor')->index('fk_distribuidor_enderecoDistribuidor1_idx');
			$table->integer('idHorarioFuncionamento')->index('fk_distribuidor_horarioFuncionamento1_idx');
			$table->integer('idTaxaEntrega')->index('fk_distribuidor_taxaEntrega1_idx');
			$table->integer('idNovoHorarioFuncionamento')->index('fk_distribuidor_novoHorarioFuncionamento1_idx');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('distribuidor');
	}

}
