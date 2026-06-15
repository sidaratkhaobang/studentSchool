@extends('layouts.app')

@section('title', 'Admin - StudentSchool')

@section('content')
<div id="admin-app" v-cloak>
    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-dark sticky-top px-3 admin-topbar">
        <a class="navbar-brand" href="/admin" @click.prevent="navigate('dashboard')">
            <i class="bi bi-mortarboard-fill me-2"></i>StudentSchool <span class="badge bg-danger ms-1 small">Admin</span>
        </a>
        <div class="d-flex align-items-center gap-2">
            <span class="text-white-50 small">ยินดีต้อนรับ, @{{ user?.username }}</span>
            <button class="btn btn-outline-light btn-sm" @click="logout">
                <i class="bi bi-box-arrow-right me-1"></i>ออกจากระบบ
            </button>
        </div>
    </nav>

    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar admin-sidebar">
            <div class="sidebar-section-label">Admin Menu</div>
            <nav class="nav flex-column">
                <a v-for="item in menuItems" :key="item.key"
                   class="nav-link" :class="{active: page === item.key}"
                   :href="item.path" @click.prevent="navigate(item.key)">
                    <i :class="item.icon"></i>
                    <span>@{{ item.label }}</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-content">
            <div class="page-titlebar">
                <div>
                    <p class="text-muted mb-1 small">Admin / @{{ currentMenu.label }}</p>
                    <h1>@{{ currentMenu.label }}</h1>
                </div>
                <a class="btn btn-outline-secondary btn-sm" href="/login">
                    <i class="bi bi-box-arrow-in-left me-1"></i>หน้า Login
                </a>
            </div>

            <admin-dashboard v-if="page==='dashboard'" :token="token"></admin-dashboard>
            <teacher-manager v-if="page==='teachers'" :token="token"></teacher-manager>
            <subject-manager v-if="page==='subjects'" :token="token"></subject-manager>
            <assignment-manager v-if="page==='assignments'" :token="token"></assignment-manager>
            <student-list v-if="page==='students'" :token="token"></student-list>
        </main>
    </div>
</div>
@endsection

@push('styles')
<style>
    [v-cloak] {
        display: none;
    }

    .admin-layout {
        display: grid;
        grid-template-columns: 260px minmax(0, 1fr);
        min-height: calc(100vh - 56px);
    }

    .admin-sidebar {
        width: 260px;
        min-width: 260px;
        padding: 1rem 0.75rem;
        position: sticky;
        top: 56px;
        align-self: start;
        height: calc(100vh - 56px);
        overflow-y: auto;
    }

    .sidebar-section-label {
        color: rgba(255, 255, 255, 0.55);
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 0.75rem 0.75rem 0.5rem;
    }

    .admin-sidebar .nav-link {
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 0.7rem;
        margin-bottom: 0.25rem;
    }

    .admin-sidebar .nav-link i {
        width: 1.25rem;
        text-align: center;
        margin: 0;
    }

    .admin-content {
        min-width: 0;
        padding: 1.5rem;
    }

    .page-titlebar {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .page-titlebar h1 {
        font-size: 1.6rem;
        font-weight: 800;
        margin: 0;
        color: #111827;
    }

    @media (max-width: 900px) {
        .admin-layout {
            grid-template-columns: 1fr;
        }

        .admin-sidebar {
            position: static;
            width: 100%;
            min-width: 0;
            min-height: auto !important;
            height: auto;
        }

        .admin-sidebar .nav {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.25rem;
        }
    }
</style>
@endpush

@push('scripts')
@vite(['resources/css/app.css', 'resources/js/admin/app.js'])
@endpush
