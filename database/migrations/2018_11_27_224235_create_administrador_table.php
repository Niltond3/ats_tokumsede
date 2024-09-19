<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
class CreateAdministradorTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('administrador', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('nome', 50)->nullable();
			$table->string('email', 80)->nullable();
			$table->string('login', 25)->nullable();
			$table->string('senha')->nullable();
			$table->string('acessos', 5)->nullable();
			$table->string('status', 45)->nullable();
			$table->string('tipoAdministrador', 45)->nullable();
			$table->timestamp('dataCadastro')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->integer('idDistribuidor')->nullable()->index('fk_administrador_distribuidor1_idx');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('administrador');
	}

}
