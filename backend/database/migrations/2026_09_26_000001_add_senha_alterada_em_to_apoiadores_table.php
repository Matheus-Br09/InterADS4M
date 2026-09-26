<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('apoiadores', function (Blueprint $table) {
            $table->timestamp('senha_alterada_em')->nullable()->after('senha');
        });
    }

    public function down(): void
    {
        Schema::table('apoiadores', function (Blueprint $table) {
            $table->dropColumn('senha_alterada_em');
        });
    }
};
