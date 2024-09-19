<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxaEntregaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('taxaEntrega', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->boolean('taxaUnica');
			$table->float('valorTaxaUnica', 10);
			$table->boolean('taxaDomingo');
			$table->float('valorTaxaDomingo', 10);
			$table->boolean('taxaCompraMinima');
			$table->float('valorCompraMinima', 10);
			$table->boolean('taxaEntregaDistante');
			$table->float('distanciaMaxima', 10);
			$table->float('valorKmAdicional', 10);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('taxaEntrega');
	}

}
