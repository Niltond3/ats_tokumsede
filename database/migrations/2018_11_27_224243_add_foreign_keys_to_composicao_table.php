<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToComposicaoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('composicao', function(Blueprint $table)
		{
			$table->foreign('idComposicao', 'fk_produto_has_produto_produto1')->references('id')->on('produto')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('idComponente', 'fk_produto_has_produto_produto2')->references('id')->on('produto')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('composicao', function(Blueprint $table)
		{
			$table->dropForeign('fk_produto_has_produto_produto1');
			$table->dropForeign('fk_produto_has_produto_produto2');
		});
	}

}
