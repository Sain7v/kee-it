<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (! Schema::hasColumn('tasks', 'priority_total')) {
                $table->unsignedInteger('priority_total')->default(0)->after('priority');
            }

            $table->index('priority_total', 'tasks_priority_total_idx');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('tasks_priority_total_idx');
        });
    }
};
