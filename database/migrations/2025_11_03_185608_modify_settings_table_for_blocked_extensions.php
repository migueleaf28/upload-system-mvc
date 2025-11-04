<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->text('value')->change();
        });

        $blockedExtensions = [
            'exe', 'bat', 'cmd', 'sh', 'php', 'js', 'html', 'htm', 
            'phtml', 'py', 'pl', 'jar', 'war', 'apk'
        ];

        DB::table('settings')->updateOrInsert(
            ['key' => 'blocked_extensions'],
            [
                'value' => json_encode($blockedExtensions),
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('value')->change();
        });

        DB::table('settings')->where('key', 'blocked_extensions')->delete();
    }
};