<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'storage_quota')) {
                $table->bigInteger('storage_quota')->nullable()->after('email');
            }
        });

        if (Schema::hasTable('groups')) {
            Schema::table('groups', function (Blueprint $table) {
                if (!Schema::hasColumn('groups', 'storage_quota')) {
                    $table->bigInteger('storage_quota')->nullable()->after('name');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('storage_quota');
        });

        if (Schema::hasTable('groups')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->dropColumn('storage_quota');
            });
        }
    }
};
