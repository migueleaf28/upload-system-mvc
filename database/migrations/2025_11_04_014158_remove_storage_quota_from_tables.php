<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('groups', 'storage_quota')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->dropColumn('storage_quota');
            });
        }

        if (Schema::hasColumn('users', 'storage_quota')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('storage_quota');
            });
        }
    }

    public function down()
    {
        if (!Schema::hasColumn('groups', 'storage_quota')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->bigInteger('storage_quota')->nullable()->after('description');
            });
        }

        if (!Schema::hasColumn('users', 'storage_quota')) {
            Schema::table('users', function (Blueprint $table) {
                $table->bigInteger('storage_quota')->nullable()->after('password');
            });
        }
    }
};