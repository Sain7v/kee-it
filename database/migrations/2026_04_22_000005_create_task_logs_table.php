<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')
                ->constrained('tasks', 'id', 'task_logs_task_id_fk')
                ->cascadeOnDelete();
            $table->string('action');
            $table->text('note')->nullable();
            $table->timestamp('logged_at')->useCurrent();

            $table->index('task_id', 'task_logs_task_id_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_logs');
    }
};
