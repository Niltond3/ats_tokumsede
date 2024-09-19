<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParceriaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('parceria', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('idDistribuidor')->index('fk_parceria_distribuidor1_idx');
			$table->integer('idClienteParceiro')->index('fk_parceria_clienteParceiro1_idx');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('parceria');
	}

}
