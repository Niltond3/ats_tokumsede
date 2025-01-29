<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistributorStockUnionsTable extends Migration
{
    public function up()
    {
        Schema::create('distributor_stock_unions', function (Blueprint $table) {
            $table->id();
            $table->integer('main_distributor_id');
            $table->integer('secondary_distributor_id');
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index('main_distributor_id');
            $table->index('secondary_distributor_id');
            $table->unique('secondary_distributor_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('distributor_stock_unions');
    }
}
