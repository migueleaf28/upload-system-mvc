<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('storage_limit')->nullable()->after('password');
            $table->bigInteger('storage_used')->default(0)->after('storage_limit');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['storage_limit', 'storage_used']);
        });
    }
};