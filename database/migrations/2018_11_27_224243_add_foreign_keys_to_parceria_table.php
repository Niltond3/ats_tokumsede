<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToParceriaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('parceria', function(Blueprint $table)
		{
			$table->foreign('idClienteParceiro', 'fk_parceria_clienteParceiro1')->references('id')->on('clienteParceiro')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('idDistribuidor', 'fk_parceria_distribuidor1')->references('id')->on('distribuidor')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('parceria', function(Blueprint $table)
		{
			$table->dropForeign('fk_parceria_clienteParceiro1');
			$table->dropForeign('fk_parceria_distribuidor1');
		});
	}

}
