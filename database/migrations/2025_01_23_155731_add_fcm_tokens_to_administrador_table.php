<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('administrador', function (Blueprint $table) {
            $table->string('token_fcm')->nullable();
            $table->string('token_fcm_mobile')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('administrador', function (Blueprint $table) {
            $table->dropColumn('token_fcm');
            $table->dropColumn('token_fcm_mobile');
        });
    }
};
