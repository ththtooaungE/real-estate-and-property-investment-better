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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('description');
            $table->string('street')->nullable();
            $table->string('township')->nullable();
            $table->string('city');
            $table->string('state_or_division');
            $table->bigInteger('price');
            $table->string('width');
            $table->string('length');
            $table->bigInteger('view_count')->default(0);
            $table->enum('status', ['rent', 'sell'])->nullable();
            $table->boolean('is_declined')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
