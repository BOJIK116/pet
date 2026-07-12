<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name', 100);
            $table->string('species', 50);
            $table->string('breed', 100)->nullable();
            $table->string('sex', 20)->nullable();

            $table->date('birth_date')->nullable();
            $table->decimal('weight', 5, 2)->nullable();

            $table->text('chronic_conditions')->nullable();
            $table->boolean('is_neutered')->nullable();
            $table->text('notes')->nullable();

            $table->string('photo_path')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};