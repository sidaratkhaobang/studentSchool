import { createApp, ref, onMounted } from 'vue';
import WeeklyScheduleComponent from '../components/student/WeeklySchedule.vue';
import EnrollmentFormComponent from '../components/student/EnrollmentForm.vue';

const StudentProfile = {
    template: `
    <div>
        <h4 class="fw-bold mb-4"><i class="bi bi-person-circle me-2 text-primary"></i>โปรไฟล์ของฉัน</h4>
        <div class="card" v-if="profile">
            <div class="card-body">
                <div v-if="success" class="alert alert-success">อัปเดตข้อมูลสำเร็จ</div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">ชื่อ (ไทย)</label><input type="text" class="form-control" v-model="form.first_name_th"></div>
                    <div class="col-md-6"><label class="form-label">นามสกุล (ไทย)</label><input type="text" class="form-control" v-model="form.last_name_th"></div>
                    <div class="col-md-6"><label class="form-label">First Name</label><input type="text" class="form-control" v-model="form.first_name_en"></div>
                    <div class="col-md-6"><label class="form-label">Last Name</label><input type="text" class="form-control" v-model="form.last_name_en"></div>
                    <div class="col-md-4"><label class="form-label">วันเกิด</label><input type="date" class="form-control" v-model="form.date_of_birth"></div>
                    <div class="col-md-4"><label class="form-label">อายุ</label><input type="number" class="form-control" v-model.number="form.age"></div>
                    <div class="col-md-4"><label class="form-label">ชั้นที่ศึกษา</label><input type="text" class="form-control" v-model="form.grade_level"></div>
                    <div class="col-md-6"><label class="form-label">อาจารย์ที่ปรึกษา</label>
                        <select class="form-select" v-model="form.advisor_teacher_id">
                            <option value="">-- เลือกอาจารย์ --</option>
                            <option v-for="t in teachers" :key="t.id" :value="t.id">{{ t.first_name_th }} {{ t.last_name_th }}</option>
                        </select>
                    </div>
                    <div class="col-md-6"><label class="form-label">เบอร์ติดต่อ</label><input type="text" class="form-control" v-model="form.phone"></div>
                    <div class="col-md-6"><label class="form-label">อีเมล</label><input type="email" class="form-control" v-model="form.email"></div>
                    <div class="col-12 border-top pt-3">
                        <h6 class="fw-bold text-muted">เปลี่ยนรหัสผ่าน (ไม่บังคับ)</h6>
                        <div class="row g-3">
                            <div class="col-md-4"><label class="form-label">รหัสผ่านปัจจุบัน</label><input type="password" class="form-control" v-model="form.current_password"></div>
                            <div class="col-md-4"><label class="form-label">รหัสผ่านใหม่</label><input type="password" class="form-control" v-model="form.new_password"></div>
                            <div class="col-md-4"><label class="form-label">ยืนยันรหัสผ่านใหม่</label><input type="password" class="form-control" v-model="form.new_password_confirmation"></div>
                        </div>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary px-4" @click="save" :disabled="saving">
                            <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                            บันทึกข้อมูล
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>`,
    props: ['token'],
    data() { return { profile: null, teachers: [], form: {}, saving: false, success: false }; },
    mounted() { this.fetchProfile(); this.fetchTeachers(); },
    methods: {
        async fetchProfile() {
            const r = await fetch('/api/student/profile', { headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' } });
            const data = await r.json();
            this.profile = data.student;
            this.form = { ...data.student, current_password: '', new_password: '', new_password_confirmation: '' };
        },
        async fetchTeachers() {
            const r = await fetch('/api/admin/teachers?per_page=100', { headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' } });
            if (r.ok) this.teachers = (await r.json()).data || [];
        },
        async save() {
            this.saving = true; this.success = false;
            const r = await fetch('/api/student/profile', { method: 'PUT', headers: { Authorization: `Bearer ${this.token}`, 'Content-Type': 'application/json', Accept: 'application/json' }, body: JSON.stringify(this.form) });
            if (r.ok) { this.success = true; this.fetchProfile(); }
            this.saving = false;
        }
    }
};

const app = createApp({
    setup() {
        const page = ref('dashboard');
        const user = ref(null);
        const student = ref(null);
        const studentStatus = ref('');
        const token = ref(localStorage.getItem('token') || '');

        onMounted(async () => {
            const u = localStorage.getItem('user');
            if (u) user.value = JSON.parse(u);
            if (!token.value || user.value?.role !== 'student') {
                window.location.href = '/login';
                return;
            }
            studentStatus.value = user.value?.student_status || '';
        });

        function logout() {
            fetch('/api/auth/logout', { method: 'POST', headers: { Authorization: `Bearer ${token.value}`, Accept: 'application/json' } });
            localStorage.clear();
            window.location.href = '/login';
        }

        return { page, user, student, studentStatus, token: token.value, logout };
    }
});

app.component('student-dashboard', WeeklyScheduleComponent);
app.component('enrollment-form', EnrollmentFormComponent);
app.component('student-profile', StudentProfile);

app.mount('#student-app');
