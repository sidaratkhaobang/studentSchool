<?php

use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Api\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Api\Admin\SubjectController as AdminSubjectController;
use App\Http\Controllers\Api\Admin\SubjectTeacherController;
use App\Http\Controllers\Api\Admin\TeacherController as AdminTeacherController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Api\Student\EnrollmentController;
use App\Http\Controllers\Api\Student\ProfileController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,15');
});

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Admin routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index']);

        Route::apiResource('/teachers', AdminTeacherController::class);
        Route::apiResource('/subjects', AdminSubjectController::class);

        Route::get('/subject-teachers', [SubjectTeacherController::class, 'index']);
        Route::post('/subject-teachers', [SubjectTeacherController::class, 'store']);
        Route::put('/subject-teachers/{subjectTeacher}', [SubjectTeacherController::class, 'update']);
        Route::delete('/subject-teachers/{subjectTeacher}', [SubjectTeacherController::class, 'destroy']);

        Route::get('/students', [AdminStudentController::class, 'index']);
        Route::get('/students/{student}', [AdminStudentController::class, 'show']);
        Route::put('/students/{student}/status', [AdminStudentController::class, 'updateStatus']);
    });

    // Student routes
    Route::middleware('student')->prefix('student')->group(function () {
        Route::get('/dashboard', [StudentDashboard::class, 'index']);
        Route::get('/subjects', [EnrollmentController::class, 'subjects']);

        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);

        Route::get('/enrollments', [EnrollmentController::class, 'index']);
        Route::post('/enrollments', [EnrollmentController::class, 'store']);
        Route::get('/enrollments/{enrollment}', [EnrollmentController::class, 'show']);
        Route::put('/enrollments/{enrollment}/submit', [EnrollmentController::class, 'submit']);
        Route::post('/enrollments/{enrollment}/courses', [EnrollmentController::class, 'addCourse']);
        Route::delete('/enrollments/{enrollment}/courses/{courseId}', [EnrollmentController::class, 'removeCourse']);
    });
});
