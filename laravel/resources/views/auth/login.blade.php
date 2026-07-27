@extends('layouts.app')

@section('title', 'เข้าสู่ระบบ - StudentSchool')

@section('content')
<div class="login-shell min-vh-100" id="login-app">
    <main class="login-panel">
        <section class="login-brand">
            <div class="brand-mark">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <h1>StudentSchool</h1>
            <p>ระบบจัดการลงทะเบียนเรียนรายสัปดาห์สำหรับผู้ดูแลระบบและนักเรียน</p>
            <div class="login-summary">
                <div>
                    <span class="summary-value">Admin</span>
                    <span class="summary-label">จัดการข้อมูลระบบ</span>
                </div>
                <div>
                    <span class="summary-value">Student</span>
                    <span class="summary-label">ลงทะเบียนรายวิชา</span>
                </div>
                <div>
                    <span class="summary-value">Teacher</span>
                    <span class="summary-label">อนุมัติตารางเรียน</span>
                </div>
            </div>
        </section>

        <section class="login-card">
            <div class="login-card-header">
                <div>
                    <p class="eyebrow">เข้าสู่ระบบ</p>
                    <h2>Login</h2>
                </div>
                <i class="bi bi-shield-lock"></i>
            </div>

            <div class="alert alert-danger py-2 d-none" id="login-error" role="alert"></div>

            <form id="login-form" novalidate>
                <div class="mb-3">
                    <label for="username" class="form-label fw-semibold">ชื่อผู้ใช้</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input id="username" name="username" type="text" class="form-control"
                               value="admin" autocomplete="username" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">รหัสผ่าน</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input id="password" name="password" type="password" class="form-control"
                               value="Admin1234!" autocomplete="current-password" required>
                        <button class="btn btn-outline-secondary" type="button" id="toggle-password"
                                aria-label="แสดงหรือซ่อนรหัสผ่าน">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="login-hint">
                    <button type="button" class="credential-option active" data-username="admin" data-password="Admin1234!">
                        <span><i class="bi bi-person-gear"></i> Admin</span>
                        <small>admin / Admin1234!</small>
                    </button>
                    <button type="button" class="credential-option" data-username="student01" data-password="Student1234!">
                        <span><i class="bi bi-person-vcard"></i> Student</span>
                        <small>student01 / Student1234!</small>
                    </button>
                    <button type="button" class="credential-option" data-username="teacher01" data-password="Teacher1234!">
                        <span><i class="bi bi-person-workspace"></i> Teacher</span>
                        <small>teacher01 / Teacher1234!</small>
                    </button>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold" id="login-button">
                    <span class="spinner-border spinner-border-sm me-2 d-none" id="login-spinner"></span>
                    <span id="login-button-text">เข้าสู่ระบบ</span>
                </button>

                <div class="register-link">
                    <span>ยังไม่มีบัญชีนักเรียน?</span>
                    <a class="btn btn-outline-primary w-100 fw-semibold" href="/register">
                        <i class="bi bi-person-plus me-1"></i>ลงทะเบียน
                    </a>
                </div>
            </form>
        </section>
    </main>
</div>
@endsection

@push('styles')
<style>
    .login-shell {
        background:
            linear-gradient(135deg, rgba(16, 24, 40, 0.88), rgba(30, 64, 175, 0.76)),
            url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1600&q=80') center/cover;
        display: grid;
        place-items: center;
        padding: 2rem 1rem;
    }

    .login-panel {
        width: min(980px, 100%);
        display: grid;
        grid-template-columns: minmax(0, 1fr) 420px;
        background: rgba(255, 255, 255, 0.96);
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 24px 80px rgba(15, 23, 42, 0.32);
    }

    .login-brand {
        background: #10243f;
        color: #fff;
        padding: 3rem;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        min-height: 560px;
    }

    .brand-mark {
        width: 64px;
        height: 64px;
        display: grid;
        place-items: center;
        background: #f59e0b;
        color: #10243f;
        border-radius: 8px;
        font-size: 2rem;
        margin-bottom: 1.5rem;
    }

    .login-brand h1 {
        font-size: 2.6rem;
        font-weight: 800;
        margin-bottom: 0.75rem;
    }

    .login-brand p {
        color: rgba(255, 255, 255, 0.78);
        max-width: 440px;
        font-size: 1.05rem;
        margin-bottom: 2rem;
    }

    .login-summary {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .login-summary > div {
        min-width: 140px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 8px;
        padding: 0.85rem 1rem;
    }

    .summary-value,
    .summary-label {
        display: block;
    }

    .summary-value {
        font-weight: 800;
        font-size: 1.15rem;
    }

    .summary-label {
        color: rgba(255, 255, 255, 0.68);
        font-size: 0.82rem;
    }

    .login-card {
        padding: 3rem 2.5rem;
        align-self: center;
    }

    .login-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.75rem;
    }

    .login-card-header h2 {
        font-size: 1.7rem;
        font-weight: 800;
        margin: 0;
        color: #111827;
    }

    .login-card-header > i {
        color: #1d4ed8;
        font-size: 2rem;
    }

    .eyebrow {
        color: #64748b;
        font-size: 0.82rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
        text-transform: uppercase;
    }

    .login-hint {
        display: grid;
        gap: 0.6rem;
        margin-bottom: 1rem;
    }

    .credential-option {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem 0.85rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
        color: #475569;
        font-size: 0.9rem;
        text-align: left;
        width: 100%;
    }

    .credential-option span {
        font-weight: 700;
        color: #111827;
    }

    .credential-option small {
        color: #64748b;
    }

    .credential-option.active {
        border-color: #2563eb;
        background: #eff6ff;
    }

    .register-link {
        border-top: 1px solid #e5e7eb;
        display: grid;
        gap: 0.75rem;
        margin-top: 1rem;
        padding-top: 1rem;
        text-align: center;
    }

    .register-link span {
        color: #64748b;
        font-size: 0.9rem;
    }

    @media (max-width: 860px) {
        .login-panel {
            grid-template-columns: 1fr;
        }

        .login-brand {
            min-height: auto;
            padding: 2rem;
        }

        .login-card {
            padding: 2rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
const form = document.getElementById('login-form');
const errorBox = document.getElementById('login-error');
const button = document.getElementById('login-button');
const buttonText = document.getElementById('login-button-text');
const spinner = document.getElementById('login-spinner');
const passwordInput = document.getElementById('password');
const togglePassword = document.getElementById('toggle-password');
const credentialOptions = document.querySelectorAll('.credential-option');

function setLoading(isLoading) {
    button.disabled = isLoading;
    spinner.classList.toggle('d-none', !isLoading);
    buttonText.textContent = isLoading ? 'กำลังเข้าสู่ระบบ...' : 'เข้าสู่ระบบ';
}

function showError(message) {
    errorBox.textContent = message;
    errorBox.classList.remove('d-none');
}

togglePassword.addEventListener('click', () => {
    const showPassword = passwordInput.type === 'password';
    passwordInput.type = showPassword ? 'text' : 'password';
    togglePassword.querySelector('i').className = showPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
});

credentialOptions.forEach((option) => {
    option.addEventListener('click', () => {
        credentialOptions.forEach((item) => item.classList.remove('active'));
        option.classList.add('active');
        form.username.value = option.dataset.username;
        form.password.value = option.dataset.password;
    });
});

form.addEventListener('submit', async (event) => {
    event.preventDefault();
    errorBox.classList.add('d-none');
    setLoading(true);

    const payload = {
        username: form.username.value.trim(),
        password: form.password.value,
    };

    try {
        const res = await fetch('/api/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(payload),
        });
        const data = await res.json();

        if (!res.ok) {
            showError(data.message || 'เข้าสู่ระบบไม่สำเร็จ');
            return;
        }

        localStorage.setItem('token', data.token);
        localStorage.setItem('user', JSON.stringify(data.user));

        if (data.user?.role === 'admin') {
            window.location.href = '/admin';
            return;
        }

        if (data.user?.role === 'student') {
            window.location.href = '/student';
            return;
        }

        if (data.user?.role === 'teacher') {
            window.location.href = '/teacher';
            return;
        }

        showError('บัญชีนี้ยังไม่มีสิทธิ์เข้าใช้งานระบบ');
    } catch (error) {
        showError('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
    } finally {
        setLoading(false);
    }
});
</script>
@endpush
