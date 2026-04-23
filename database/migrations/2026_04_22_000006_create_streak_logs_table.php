<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('streak_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users', 'id', 'streak_logs_user_id_fk')
                ->cascadeOnDelete();
            $table->date('date');
            $table->unsignedInteger('tasks_completed')->default(0);
            $table->boolean('is_productive')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'date'], 'streak_logs_user_date_unique');
            $table->index('user_id', 'streak_logs_user_id_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('streak_logs');
    }
};
