<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollment_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_enrollment_id')->constrained('weekly_enrollments')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->restrictOnDelete();
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
            $table->decimal('hours', 4, 1)->default(1.0);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->timestamps();

            $table->index(['weekly_enrollment_id', 'day_of_week']);
            $table->index('subject_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_courses');
    }
};
