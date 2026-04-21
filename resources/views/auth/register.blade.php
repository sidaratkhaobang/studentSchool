@extends('layouts.app')

@section('title', 'สมัครสมาชิก - StudentSchool')

@section('content')
<div class="min-vh-100 py-4 bg-light" id="register-app">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="text-center mb-4">
                    <i class="bi bi-mortarboard-fill text-primary" style="font-size: 2.5rem;"></i>
                    <h1 class="h3 fw-bold mt-2">StudentSchool</h1>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">
                            <i class="bi bi-person-plus me-2 text-primary"></i>สมัครสมาชิกนักเรียน
                        </h5>
                        <div v-if="success" class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            ลงทะเบียนสำเร็จ! กรุณารอการอนุมัติจาก Admin
                            <a href="{{ route('login') }}" class="alert-link ms-2">กลับไปหน้า Login</a>
                        </div>
                        <div v-if="errors.general" class="alert alert-danger">@{{ errors.general }}</div>
                        <form @submit.prevent="register" v-if="!success">
                            <!-- ข้อมูลส่วนตัว -->
                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">
                                <i class="bi bi-person-circle me-1"></i>ข้อมูลส่วนตัว
                            </h6>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">ชื่อ (ภาษาไทย) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" :class="{'is-invalid': errors.first_name_th}"
                                           v-model="form.first_name_th" placeholder="ชื่อจริง">
                                    <div class="invalid-feedback">@{{ errors.first_name_th?.[0] }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">นามสกุล (ภาษาไทย) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" :class="{'is-invalid': errors.last_name_th}"
                                           v-model="form.last_name_th" placeholder="นามสกุล">
                                    <div class="invalid-feedback">@{{ errors.last_name_th?.[0] }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">First Name (English) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" :class="{'is-invalid': errors.first_name_en}"
                                           v-model="form.first_name_en" placeholder="First Name">
                                    <div class="invalid-feedback">@{{ errors.first_name_en?.[0] }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Last Name (English) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" :class="{'is-invalid': errors.last_name_en}"
                                           v-model="form.last_name_en" placeholder="Last Name">
                                    <div class="invalid-feedback">@{{ errors.last_name_en?.[0] }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">วันเดือนปีเกิด <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" :class="{'is-invalid': errors.date_of_birth}"
                                           v-model="form.date_of_birth">
                                    <div class="invalid-feedback">@{{ errors.date_of_birth?.[0] }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">อายุ <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" :class="{'is-invalid': errors.age}"
                                           v-model="form.age" min="5" max="99" placeholder="อายุ">
                                    <div class="invalid-feedback">@{{ errors.age?.[0] }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">ชั้นที่กำลังศึกษา <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" :class="{'is-invalid': errors.grade_level}"
                                           v-model="form.grade_level" placeholder="เช่น ม.4, ป.6">
                                    <div class="invalid-feedback">@{{ errors.grade_level?.[0] }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">อาจารย์ที่ปรึกษา</label>
                                    <select class="form-select" v-model="form.advisor_teacher_id">
                                        <option value="">-- เลือกอาจารย์ที่ปรึกษา --</option>
                                        <option v-for="t in teachers" :key="t.id" :value="t.id">
                                            @{{ t.first_name_th }} @{{ t.last_name_th }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">เบอร์ติดต่อ</label>
                                    <input type="text" class="form-control" v-model="form.phone" placeholder="0812345678">
                                </div>
                            </div>
                            <!-- ข้อมูลการเข้าใช้งาน -->
                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2 mt-4">
                                <i class="bi bi-shield-lock me-1"></i>ข้อมูลการเข้าใช้งาน
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">อีเมล <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" :class="{'is-invalid': errors.email}"
                                           v-model="form.email" placeholder="email@example.com">
                                    <div class="invalid-feedback">@{{ errors.email?.[0] }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" :class="{'is-invalid': errors.username}"
                                           v-model="form.username" placeholder="ตัวอักษร a-z, 0-9, _ (4-20 ตัว)">
                                    <div class="invalid-feedback">@{{ errors.username?.[0] }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">รหัสผ่าน <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" :class="{'is-invalid': errors.password}"
                                           v-model="form.password" placeholder="อย่างน้อย 8 ตัว">
                                    <div class="invalid-feedback">@{{ errors.password?.[0] }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">ยืนยันรหัสผ่าน <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" v-model="form.password_confirmation"
                                           placeholder="กรอกรหัสผ่านอีกครั้ง">
                                </div>
                            </div>
                            <div class="d-flex gap-3 mt-4">
                                <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold" :disabled="loading">
                                    <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                                    <i v-else class="bi bi-person-check me-2"></i>
                                    @{{ loading ? 'กำลังสมัคร...' : 'สมัครสมาชิก' }}
                                </button>
                                <a href="{{ route('login') }}" class="btn btn-outline-secondary px-4 py-2">
                                    มีบัญชีแล้ว? เข้าสู่ระบบ
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script>
const { createApp, ref, onMounted } = Vue;
createApp({
    setup() {
        const form = ref({ first_name_th:'',last_name_th:'',first_name_en:'',last_name_en:'',
            date_of_birth:'',age:'',grade_level:'',advisor_teacher_id:'',phone:'',
            email:'',username:'',password:'',password_confirmation:'' });
        const teachers = ref([]);
        const errors = ref({});
        const loading = ref(false);
        const success = ref(false);

        onMounted(async () => {
            try {
                const res = await fetch('/api/student/subjects');
                // Load teachers for advisor selection - use a public endpoint
                const r = await fetch('/api/auth/teachers');
                if (r.ok) teachers.value = (await r.json()).data || [];
            } catch {}
        });

        async function register() {
            loading.value = true;
            errors.value = {};
            try {
                const res = await fetch('/api/auth/register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json',
                               'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify(form.value)
                });
                const data = await res.json();
                if (!res.ok) { errors.value = data.errors || { general: data.message }; return; }
                success.value = true;
            } catch (e) {
                errors.value = { general: 'เกิดข้อผิดพลาด กรุณาลองใหม่' };
            } finally {
                loading.value = false;
            }
        }
        return { form, teachers, errors, loading, success, register };
    }
}).mount('#register-app');
</script>
@endpush
