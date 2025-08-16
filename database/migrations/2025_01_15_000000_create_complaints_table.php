<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('category');
            $table->text('description');
            $table->string('photo_path')->nullable();
            $table->enum('status', ['new', 'in_progress', 'resolved', 'closed'])->default('new');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('rating')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'assigned_to']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
