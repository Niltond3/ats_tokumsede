<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds a nullable pix_key column to the distribuidor table.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('distribuidor', function (Blueprint $table) {
            $table->string('pix_key', 140)->nullable()->after('outrosContatos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the pix_key column.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('distribuidor', function (Blueprint $table) {
            $table->dropColumn('pix_key');
        });
    }
};
