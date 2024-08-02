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
        Schema::create('tutor_update_otps', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('tutor_id')->constrained('tutors')->cascadeOnDelete();
            $table->string('otp');
            $table->string('type');
            $table->timestamp('expires_at');

            // Add unique constraint to user_id
            $table->unique('tutor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutor_update_otps');
    }
};
