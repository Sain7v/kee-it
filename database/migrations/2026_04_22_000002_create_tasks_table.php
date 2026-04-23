<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users', 'id', 'tasks_user_id_fk')
                ->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('category', ['tarea', 'examen', 'proyecto', 'lectura', 'otro']);
            $table->enum('priority', ['baja', 'media', 'alta', 'critica'])->default('baja');
            $table->enum('status', ['pendiente', 'en_progreso', 'completada', 'cancelada'])->default('pendiente');
            $table->datetime('due_date');
            $table->unsignedInteger('estimated_minutes')->default(0);
            $table->unsignedInteger('actual_minutes')->nullable();
            $table->unsignedInteger('priority_total')->default(0);
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_rule')->nullable();
            $table->unsignedInteger('procrastination_score')->default(0);
            $table->text('ai_suggestion')->nullable();
            $table->timestamp('ai_prioritized_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status'], 'tasks_user_status_idx');
            $table->index(['user_id', 'priority'], 'tasks_user_priority_idx');
            $table->index('due_date', 'tasks_due_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
