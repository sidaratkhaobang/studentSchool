<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name_th', 100);
            $table->string('last_name_th', 100);
            $table->string('first_name_en', 100);
            $table->string('last_name_en', 100);
            $table->string('email', 100)->unique();
            $table->string('phone', 20)->nullable();
            $table->text('bio')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
            $table->index(['last_name_th', 'first_name_th']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
