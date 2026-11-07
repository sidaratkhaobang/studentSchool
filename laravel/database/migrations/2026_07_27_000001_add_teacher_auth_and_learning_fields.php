<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'teacher', 'student') NOT NULL DEFAULT 'student'");
            DB::statement("ALTER TABLE weekly_enrollments MODIFY status ENUM('draft', 'submitted', 'approved', 'rejected') NOT NULL DEFAULT 'draft'");
        }

        Schema::table('teachers', function (Blueprint $table) {
            if (! Schema::hasColumn('teachers', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
                $table->unique('user_id');
            }
        });

        Schema::table('subjects', function (Blueprint $table) {
            if (! Schema::hasColumn('subjects', 'learning_content')) {
                $table->text('learning_content')->nullable()->after('description');
            }

            if (! Schema::hasColumn('subjects', 'material_path')) {
                $table->string('material_path')->nullable()->after('learning_content');
            }
        });

        Schema::table('weekly_enrollments', function (Blueprint $table) {
            if (! Schema::hasColumn('weekly_enrollments', 'approved_by_teacher_id')) {
                $table->foreignId('approved_by_teacher_id')->nullable()->after('note')->constrained('teachers')->nullOnDelete();
            }

            if (! Schema::hasColumn('weekly_enrollments', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by_teacher_id');
            }

            if (! Schema::hasColumn('weekly_enrollments', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('approved_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('weekly_enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('weekly_enrollments', 'approved_by_teacher_id')) {
                $table->dropConstrainedForeignId('approved_by_teacher_id');
            }

            if (Schema::hasColumn('weekly_enrollments', 'approved_at')) {
                $table->dropColumn('approved_at');
            }

            if (Schema::hasColumn('weekly_enrollments', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
        });

        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'learning_content')) {
                $table->dropColumn('learning_content');
            }

            if (Schema::hasColumn('subjects', 'material_path')) {
                $table->dropColumn('material_path');
            }
        });

        Schema::table('teachers', function (Blueprint $table) {
            if (Schema::hasColumn('teachers', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'student') NOT NULL DEFAULT 'student'");
            DB::statement("ALTER TABLE weekly_enrollments MODIFY status ENUM('draft', 'submitted', 'approved') NOT NULL DEFAULT 'draft'");
        }
    }
};
