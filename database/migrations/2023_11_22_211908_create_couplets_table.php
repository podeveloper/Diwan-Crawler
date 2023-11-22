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
        Schema::create('couplets', function (Blueprint $table) {
            $table->id();
            $table->integer('number_of_couplet');
            $table->string('first_line');
            $table->string('second_line');
            $table->foreignId('poem_id')->constrained('poems');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('couplets');
    }
};
