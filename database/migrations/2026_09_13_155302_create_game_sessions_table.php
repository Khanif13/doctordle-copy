<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->string('pin', 6)->unique();
            $table->enum('status', ['waiting', 'in_progress', 'finished'])->default('waiting');
            $table->integer('current_question_index')->default(0);
            $table->json('settings');
            $table->timestamp('current_question_started_at')->nullable();
            $table->timestamps();

            $table->index('pin');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_sessions');
    }
};
