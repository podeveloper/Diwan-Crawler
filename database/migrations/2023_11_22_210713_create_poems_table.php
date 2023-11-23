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
        Schema::create('poems', function (Blueprint $table) {
            $table->id();
            $table->integer('number_of_poem')->nullable();
            $table->string('title')->nullable();
            $table->string('type')->nullable();
            $table->string('couplet_count')->nullable();
            $table->string('meter')->nullable();
            $table->string('url')->nullable();
            $table->foreignId('poet_id')->nullable()->constrained('poets');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poems');
    }
};
