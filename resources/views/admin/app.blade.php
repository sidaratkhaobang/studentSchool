@extends('layouts.app')

@section('title', 'Admin - StudentSchool')

@section('content')
<div id="admin-app">
    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-dark sticky-top px-3">
        <a class="navbar-brand" href="#">
            <i class="bi bi-mortarboard-fill me-2"></i>StudentSchool <span class="badge bg-danger ms-1 small">Admin</span>
        </a>
        <div class="d-flex align-items-center gap-2">
            <span class="text-white-50 small">ยินดีต้อนรับ, @{{ user?.username }}</span>
            <button class="btn btn-outline-light btn-sm" @click="logout">
                <i class="bi bi-box-arrow-right me-1"></i>ออกจากระบบ
            </button>
        </div>
    </nav>

    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar" style="width: 240px; min-width: 240px;">
            <nav class="nav flex-column pt-3">
                <a class="nav-link" :class="{active: page==='dashboard'}" @click="page='dashboard'" href="#">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
                <a class="nav-link" :class="{active: page==='teachers'}" @click="page='teachers'" href="#">
                    <i class="bi bi-person-badge me-2"></i>จัดการอาจารย์
                </a>
                <a class="nav-link" :class="{active: page==='subjects'}" @click="page='subjects'" href="#">
                    <i class="bi bi-book me-2"></i>จัดการรายวิชา
                </a>
                <a class="nav-link" :class="{active: page==='assignments'}" @click="page='assignments'" href="#">
                    <i class="bi bi-link-45deg me-2"></i>ผู้รับผิดชอบวิชา
                </a>
                <a class="nav-link" :class="{active: page==='students'}" @click="page='students'" href="#">
                    <i class="bi bi-people me-2"></i>รายชื่อนักเรียน
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1 p-4">
            <admin-dashboard v-if="page==='dashboard'" :token="token"></admin-dashboard>
            <teacher-manager v-if="page==='teachers'" :token="token"></teacher-manager>
            <subject-manager v-if="page==='subjects'" :token="token"></subject-manager>
            <assignment-manager v-if="page==='assignments'" :token="token"></assignment-manager>
            <student-list v-if="page==='students'" :token="token"></student-list>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="{{ asset('js/admin/app.js') }}"></script>
@endpush
