<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_session_id')->constrained()->cascadeOnDelete();
            $table->string('nickname');
            $table->string('session_token', 64)->unique();
            $table->timestamp('joined_at');
            $table->timestamps();

            $table->index('session_token');
            $table->unique(['game_session_id', 'nickname']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
