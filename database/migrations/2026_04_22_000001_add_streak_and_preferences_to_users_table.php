<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('preferences')->nullable()->after('remember_token');
            $table->unsignedInteger('streak_current')->default(0)->after('preferences');
            $table->unsignedInteger('streak_best')->default(0)->after('streak_current');
            $table->date('streak_last_active')->nullable()->after('streak_best');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['preferences', 'streak_current', 'streak_best', 'streak_last_active']);
        });
    }
};
