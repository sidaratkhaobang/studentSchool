import { computed, createApp, ref, onMounted } from 'vue';
import WeeklyScheduleComponent from '../components/student/WeeklySchedule.vue';
import EnrollmentFormComponent from '../components/student/EnrollmentForm.vue';
import { errorDialog, successToast } from '../utils/dialogs';
import { installSelect2 } from '../utils/select2';

const StudentProfile = {
    template: `
    <div>
        <h4 class="fw-bold mb-4"><i class="bi bi-person-circle me-2 text-primary"></i>โปรไฟล์ของฉัน</h4>
        <div class="card" v-if="profile">
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted small">Username</div>
                            <div class="fw-bold">{{ profile.user?.username || '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted small">สถานะ</div>
                            <span :class="statusBadge(profile.status)">{{ statusLabel(profile.status) }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted small">อาจารย์ที่ปรึกษาปัจจุบัน</div>
                            <div class="fw-bold">{{ advisorName(profile.advisor) }}</div>
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">ชื่อ (ไทย)</label><input type="text" class="form-control" v-model="form.first_name_th"></div>
                    <div class="col-md-6"><label class="form-label">นามสกุล (ไทย)</label><input type="text" class="form-control" v-model="form.last_name_th"></div>
                    <div class="col-md-6"><label class="form-label">First Name</label><input type="text" class="form-control" v-model="form.first_name_en"></div>
                    <div class="col-md-6"><label class="form-label">Last Name</label><input type="text" class="form-control" v-model="form.last_name_en"></div>
                    <div class="col-md-4"><label class="form-label">วันเกิด</label><input type="date" class="form-control" v-model="form.date_of_birth"></div>
                    <div class="col-md-4"><label class="form-label">อายุ</label><input type="number" class="form-control" v-model.number="form.age"></div>
                    <div class="col-md-4"><label class="form-label">ชั้นที่ศึกษา</label><input type="text" class="form-control" v-model="form.grade_level"></div>
                    <div class="col-md-6"><label class="form-label">อาจารย์ที่ปรึกษา</label>
                        <select class="form-select select2-control" v-select2="{ placeholder: '-- เลือกอาจารย์ --', allowClear: true }" v-model="form.advisor_teacher_id">
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
    data() { return { profile: null, teachers: [], form: {}, saving: false }; },
    mounted() { this.fetchProfile(); this.fetchTeachers(); },
    methods: {
        async fetchProfile() {
            const r = await fetch('/api/student/profile', { headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' } });
            const data = await r.json();
            this.profile = data.student;
            this.form = { ...data.student, current_password: '', new_password: '', new_password_confirmation: '' };
        },
        async fetchTeachers() {
            const r = await fetch('/api/auth/teachers', { headers: { Accept: 'application/json' } });
            if (r.ok) this.teachers = (await r.json()).data || [];
        },
        async save() {
            this.saving = true;
            const r = await fetch('/api/student/profile', { method: 'PUT', headers: { Authorization: `Bearer ${this.token}`, 'Content-Type': 'application/json', Accept: 'application/json' }, body: JSON.stringify(this.form) });
            const data = await r.json();
            if (r.ok) {
                successToast(data.message || 'อัปเดตข้อมูลสำเร็จ');
                this.fetchProfile();
            } else {
                await errorDialog(data.message || 'อัปเดตข้อมูลไม่สำเร็จ');
            }
            this.saving = false;
        },
        advisorName(advisor) {
            if (!advisor) return '-';
            return `${advisor.first_name_th || ''} ${advisor.last_name_th || ''}`.trim() || '-';
        },
        statusBadge(status) {
            return { pending: 'badge bg-warning text-dark', approved: 'badge bg-success', rejected: 'badge bg-danger' }[status] || 'badge bg-secondary';
        },
        statusLabel(status) {
            return { pending: 'รออนุมัติ', approved: 'อนุมัติแล้ว', rejected: 'ไม่อนุมัติ' }[status] || status || '-';
        },
    }
};

const app = createApp({
    setup() {
        const page = ref(pageFromPath());
        const user = ref(null);
        const student = ref(null);
        const studentStatus = ref('');
        const token = ref(localStorage.getItem('token') || '');
        const studentName = computed(() => {
            if (!student.value) return '';
            return `${student.value.first_name_th || ''} ${student.value.last_name_th || ''}`.trim();
        });

        onMounted(async () => {
            const u = localStorage.getItem('user');
            if (u) user.value = JSON.parse(u);
            if (!token.value || user.value?.role !== 'student') {
                window.location.href = '/login';
                return;
            }
            studentStatus.value = user.value?.student_status || '';
            await fetchMe();
        });

        function pageFromPath() {
            const segment = window.location.pathname.split('/').filter(Boolean)[1];
            return ['dashboard', 'enrollment', 'profile'].includes(segment) ? segment : 'dashboard';
        }

        function navigate(nextPage) {
            page.value = nextPage;
            const path = nextPage === 'dashboard' ? '/student/dashboard' : `/student/${nextPage}`;
            window.history.pushState({}, '', path);
        }

        async function fetchMe() {
            const response = await fetch('/api/auth/me', {
                headers: { Authorization: `Bearer ${token.value}`, Accept: 'application/json' },
            });

            if (!response.ok) {
                localStorage.clear();
                window.location.href = '/login';
                return;
            }

            const data = await response.json();
            user.value = data.user;
            student.value = data.user?.student || null;
            studentStatus.value = student.value?.status || user.value?.student_status || '';
            localStorage.setItem('user', JSON.stringify({
                id: data.user.id,
                username: data.user.username,
                email: data.user.email,
                role: data.user.role,
                student_status: studentStatus.value,
            }));
        }

        function logout() {
            fetch('/api/auth/logout', { method: 'POST', headers: { Authorization: `Bearer ${token.value}`, Accept: 'application/json' } });
            localStorage.clear();
            window.location.href = '/login';
        }

        window.addEventListener('popstate', () => {
            page.value = pageFromPath();
        });

        return { page, user, student, studentName, studentStatus, token: token.value, navigate, logout };
    }
});

app.component('student-dashboard', WeeklyScheduleComponent);
app.component('enrollment-form', EnrollmentFormComponent);
app.component('student-profile', StudentProfile);
installSelect2(app);

app.mount('#student-app');
