<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')
                ->constrained('tasks', 'id', 'reminders_task_id_fk')
                ->cascadeOnDelete();
            $table->datetime('remind_at');
            $table->enum('type', ['push', 'email'])->default('email');
            $table->datetime('sent_at')->nullable();

            $table->index('task_id', 'reminders_task_id_idx');
            $table->index('remind_at', 'reminders_remind_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
