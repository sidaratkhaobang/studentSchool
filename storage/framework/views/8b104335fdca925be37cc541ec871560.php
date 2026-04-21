<?php $__env->startSection('title', 'เข้าสู่ระบบ - StudentSchool'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-vh-100 d-flex align-items-center bg-light" id="login-app">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="text-center mb-4">
                    <i class="bi bi-mortarboard-fill text-primary" style="font-size: 3rem;"></i>
                    <h1 class="h3 fw-bold mt-2">StudentSchool</h1>
                    <p class="text-muted">ระบบลงทะเบียนเรียนรายวิชา</p>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">เข้าสู่ระบบ</h5>
                        <div v-if="error" class="alert alert-danger alert-sm py-2" role="alert">
                            {{ error }}
                        </div>
                        <form @submit.prevent="login">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">ชื่อผู้ใช้</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" v-model="form.username"
                                           placeholder="กรอกชื่อผู้ใช้" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">รหัสผ่าน</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input :type="showPass ? 'text' : 'password'" class="form-control"
                                           v-model="form.password" placeholder="กรอกรหัสผ่าน" required>
                                    <button class="btn btn-outline-secondary" type="button" @click="showPass = !showPass">
                                        <i :class="showPass ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold"
                                    :disabled="loading">
                                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                                {{ loading ? 'กำลังเข้าสู่ระบบ...' : 'เข้าสู่ระบบ' }}
                            </button>
                        </form>
                        <hr class="my-3">
                        <div class="text-center">
                            <span class="text-muted small">ยังไม่มีบัญชี? </span>
                            <a href="<?php echo e(route('register')); ?>" class="text-decoration-none fw-semibold">สมัครสมาชิก</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script>
const { createApp, ref } = Vue;
createApp({
    setup() {
        const form = ref({ username: '', password: '' });
        const error = ref('');
        const loading = ref(false);
        const showPass = ref(false);

        async function login() {
            loading.value = true;
            error.value = '';
            try {
                const res = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json',
                               'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify(form.value)
                });
                const data = await res.json();
                if (!res.ok) { error.value = data.message || 'เข้าสู่ระบบไม่สำเร็จ'; return; }
                localStorage.setItem('token', data.token);
                localStorage.setItem('user', JSON.stringify(data.user));
                window.location.href = data.user.role === 'admin' ? '/admin' : '/student';
            } catch (e) {
                error.value = 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง';
            } finally {
                loading.value = false;
            }
        }
        return { form, error, loading, showPass, login };
    }
}).mount('#login-app');
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/ServBay/www/studentSchool/resources/views/auth/login.blade.php ENDPATH**/ ?>