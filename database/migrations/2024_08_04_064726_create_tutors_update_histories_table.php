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
        Schema::create('tutor_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->json('history');
            $table->string('type')->default('update'); //can be 'insert' when tutor is just approved and added to tutors table, or 'update' or 'delete' when updated or deleted
            $table->foreignId('tutor_id')->nullable()->constrained('tutors')->onDelete('set null');
            // $table->foreignId('tutor_id')->constrained('tutors')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutor_histories');
    }
};
