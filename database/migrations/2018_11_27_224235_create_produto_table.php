<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdutoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('produto', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('idCategoria')->index('fk_produto_categoria1_idx');
			$table->string('nome')->nullable();
			$table->string('descricao')->nullable();
			$table->string('img')->nullable();
			$table->boolean('status')->nullable();
			$table->boolean('composicao')->default(0);
			$table->boolean('componente')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('produto');
	}

}
