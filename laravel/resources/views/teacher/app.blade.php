@extends('layouts.app')

@section('title', 'Teacher - StudentSchool')

@section('content')
<div id="teacher-app">
    <nav class="navbar navbar-dark bg-primary sticky-top px-3">
        <a class="navbar-brand" href="/teacher/dashboard" @click.prevent="navigate('dashboard')">
            <i class="bi bi-person-workspace me-2"></i>TeacherSchool
        </a>
        <div class="d-flex align-items-center gap-2">
            <span class="text-white-50 small">@{{ teacherName || user?.username }}</span>
            <button class="btn btn-outline-light btn-sm" @click="logout">
                <i class="bi bi-box-arrow-right me-1"></i>ออกจากระบบ
            </button>
        </div>
    </nav>

    <div class="d-flex">
        <div class="sidebar" style="width: 240px; min-width: 240px;">
            <nav class="nav flex-column pt-3">
                <a class="nav-link" :class="{active: page==='dashboard'}" href="/teacher/dashboard" @click.prevent="navigate('dashboard')">
                    <i class="bi bi-speedometer2 me-2"></i>แดชบอร์ดอาจารย์
                </a>
                <a class="nav-link" :class="{active: page==='enrollments'}" href="/teacher/enrollments" @click.prevent="navigate('enrollments')">
                    <i class="bi bi-clipboard-check me-2"></i>อนุมัติตารางเรียน
                </a>
                <a class="nav-link" :class="{active: page==='subjects'}" href="/teacher/subjects" @click.prevent="navigate('subjects')">
                    <i class="bi bi-journal-text me-2"></i>รายวิชาที่รับผิดชอบ
                </a>
            </nav>
        </div>

        <main class="flex-grow-1 p-4">
            <teacher-dashboard v-if="page==='dashboard'" :token="token"></teacher-dashboard>
            <teacher-enrollments v-if="page==='enrollments'" :token="token"></teacher-enrollments>
            <teacher-subjects v-if="page==='subjects'" :token="token"></teacher-subjects>
        </main>
    </div>
</div>
@endsection

@push('scripts')
@vite(['resources/css/app.css', 'resources/js/teacher/app.js'])
@endpush
