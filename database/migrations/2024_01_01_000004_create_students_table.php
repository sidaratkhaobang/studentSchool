<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('first_name_th', 100);
            $table->string('last_name_th', 100);
            $table->string('first_name_en', 100);
            $table->string('last_name_en', 100);
            $table->date('date_of_birth');
            $table->unsignedInteger('age');
            $table->string('grade_level', 20);
            $table->foreignId('advisor_teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();

            $table->index('status');
            $table->index('user_id');
            $table->index('advisor_teacher_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
