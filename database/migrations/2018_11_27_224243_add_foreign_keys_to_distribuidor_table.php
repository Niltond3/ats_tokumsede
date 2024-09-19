<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToDistribuidorTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('distribuidor', function(Blueprint $table)
		{
			$table->foreign('idEnderecoDistribuidor', 'fk_distribuidor_enderecoDistribuidor1')->references('id')->on('enderecoDistribuidor')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('idHorarioFuncionamento', 'fk_distribuidor_horarioFuncionamento1')->references('id')->on('horarioFuncionamento')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('idNovoHorarioFuncionamento', 'fk_distribuidor_novoHorarioFuncionamento1')->references('id')->on('novoHorarioFuncionamento')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('idTaxaEntrega', 'fk_distribuidor_taxaEntrega1')->references('id')->on('taxaEntrega')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('distribuidor', function(Blueprint $table)
		{
			$table->dropForeign('fk_distribuidor_enderecoDistribuidor1');
			$table->dropForeign('fk_distribuidor_horarioFuncionamento1');
			$table->dropForeign('fk_distribuidor_novoHorarioFuncionamento1');
			$table->dropForeign('fk_distribuidor_taxaEntrega1');
		});
	}

}
