<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToFeriadoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('feriado', function(Blueprint $table)
		{
			$table->foreign('idDistribuidor', 'fk_feriado_distribuidor1')->references('id')->on('distribuidor')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('feriado', function(Blueprint $table)
		{
			$table->dropForeign('fk_feriado_distribuidor1');
		});
	}

}
