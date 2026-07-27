@extends('layouts.app')

@section('title', 'Student - StudentSchool')

@section('content')
<div id="student-app">
    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-primary sticky-top px-3">
        <a class="navbar-brand" href="#">
            <i class="bi bi-mortarboard-fill me-2"></i>StudentSchool
        </a>
        <div class="d-flex align-items-center gap-2">
            <span class="text-white-50 small">@{{ studentName || user?.username }}</span>
            <button class="btn btn-outline-light btn-sm" @click="logout">
                <i class="bi bi-box-arrow-right me-1"></i>ออกจากระบบ
            </button>
        </div>
    </nav>

    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar" style="width: 220px; min-width: 220px;">
            <nav class="nav flex-column pt-3">
                <a class="nav-link" :class="{active: page==='dashboard'}" @click.prevent="navigate('dashboard')" href="/student/dashboard">
                    <i class="bi bi-calendar-week me-2"></i>ตารางเรียนของฉัน
                </a>
                <a class="nav-link" :class="{active: page==='enrollment'}" @click.prevent="navigate('enrollment')" href="/student/enrollment">
                    <i class="bi bi-journal-plus me-2"></i>ลงทะเบียนเรียน
                </a>
                <a class="nav-link" :class="{active: page==='profile'}" @click.prevent="navigate('profile')" href="/student/profile">
                    <i class="bi bi-person-circle me-2"></i>โปรไฟล์ของฉัน
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1 p-4">
            <div v-if="studentStatus === 'pending'" class="alert alert-warning d-flex align-items-center">
                <i class="bi bi-hourglass-split fs-4 me-3"></i>
                <div>
                    <strong>รอการอนุมัติ</strong><br>
                    บัญชีของคุณอยู่ระหว่างรอการอนุมัติจาก Admin กรุณารอสักครู่
                </div>
            </div>
            <student-dashboard v-if="page==='dashboard'" :token="token"></student-dashboard>
            <enrollment-form v-if="page==='enrollment'" :token="token"></enrollment-form>
            <student-profile v-if="page==='profile'" :token="token"></student-profile>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@vite(['resources/css/app.css', 'resources/js/student/app.js'])
@endpush
