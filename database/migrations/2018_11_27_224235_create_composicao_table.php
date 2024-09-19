<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComposicaoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('composicao', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('idComposicao')->index('fk_produto_has_produto_produto1_idx');
			$table->integer('idComponente')->index('fk_produto_has_produto_produto2_idx');
			$table->float('quantidade', 10)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('composicao');
	}

}
