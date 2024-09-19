<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddForeignKeysToProdutoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('produto', function(Blueprint $table)
		{
			$table->foreign('idCategoria', 'fk_produto_categoria1')->references('id')->on('categoria')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('produto', function(Blueprint $table)
		{
			$table->dropForeign('fk_produto_categoria1');
		});
	}

}
