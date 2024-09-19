<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrecoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('preco', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('idProduto')->index('fk_produto_has_distribuidor_produto1_idx');
			$table->integer('idDistribuidor')->index('fk_produto_has_distribuidor_distribuidor1_idx');
			$table->integer('idEstoque')->index('fk_preco_estoque1_idx');
			$table->float('valor', 10)->nullable();
			$table->float('qtdMin', 10)->nullable();
			$table->date('inicioValidade')->nullable();
			$table->date('fimValidade')->nullable();
			$table->time('inicioHora')->nullable();
			$table->time('fimHora')->nullable();
			$table->boolean('status')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('preco');
	}

}
