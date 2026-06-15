import { createApp, computed, ref, onMounted } from 'vue';
import AdminDashboardComponent from '../components/admin/AdminDashboard.vue';
import TeacherManagerComponent from '../components/admin/TeacherManager.vue';
import StudentListComponent from '../components/admin/StudentList.vue';

// Inline SubjectManager and AssignmentManager for brevity
const SubjectManager = {
    template: `
    <div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-book me-2 text-primary"></i>จัดการรายวิชา</h4>
            <button class="btn btn-primary" @click="openModal()"><i class="bi bi-plus-lg me-1"></i>เพิ่มรายวิชา</button>
        </div>
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>รหัส</th><th>ชื่อวิชา</th><th>หน่วยกิต</th><th>สถานะ</th><th>จัดการ</th></tr>
                    </thead>
                    <tbody>
                        <tr v-for="s in subjects" :key="s.id">
                            <td><span class="badge bg-primary">{{ s.subject_code }}</span></td>
                            <td>
                                <div class="fw-semibold">{{ s.name_th }}</div>
                                <div class="text-muted small">{{ s.name_en }}</div>
                            </td>
                            <td>{{ s.credit_hours }} หน่วยกิต / {{ s.hours_per_session }} ชม./ครั้ง</td>
                            <td><span :class="s.is_active ? 'badge bg-success' : 'badge bg-secondary'">{{ s.is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" @click="openModal(s)"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-danger" @click="deleteSubject(s)"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="subjectModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title fw-bold">{{ editId ? 'แก้ไขรายวิชา' : 'เพิ่มรายวิชา' }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4"><label class="form-label">รหัสวิชา *</label><input type="text" class="form-control" v-model="form.subject_code"></div>
                            <div class="col-md-8"><label class="form-label">ชื่อวิชา (ไทย) *</label><input type="text" class="form-control" v-model="form.name_th"></div>
                            <div class="col-12"><label class="form-label">ชื่อวิชา (อังกฤษ) *</label><input type="text" class="form-control" v-model="form.name_en"></div>
                            <div class="col-md-6"><label class="form-label">หน่วยกิต</label><input type="number" class="form-control" v-model.number="form.credit_hours" min="1" max="10"></div>
                            <div class="col-md-6"><label class="form-label">ชม./ครั้ง</label><input type="number" class="form-control" v-model.number="form.hours_per_session" min="1" max="6"></div>
                            <div class="col-12"><label class="form-label">คำอธิบาย</label><textarea class="form-control" rows="3" v-model="form.description"></textarea></div>
                            <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" v-model="form.is_active"><label class="form-check-label">Active</label></div></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button class="btn btn-primary" @click="save">บันทึก</button>
                    </div>
                </div>
            </div>
        </div>
    </div>`,
    props: ['token'],
    data() { return { subjects: [], editId: null, form: { subject_code:'',name_th:'',name_en:'',credit_hours:3,hours_per_session:1,description:'',is_active:true }, modal: null }; },
    mounted() { this.fetchSubjects(); this.modal = new bootstrap.Modal(document.getElementById('subjectModal')); },
    methods: {
        async fetchSubjects() {
            const r = await fetch('/api/admin/subjects?per_page=100', { headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' } });
            const data = await r.json();
            this.subjects = data.data || [];
        },
        openModal(s = null) {
            if (s) { this.editId = s.id; this.form = { ...s }; } else { this.editId = null; this.form = { subject_code:'',name_th:'',name_en:'',credit_hours:3,hours_per_session:1,description:'',is_active:true }; }
            this.modal.show();
        },
        async save() {
            const url = this.editId ? `/api/admin/subjects/${this.editId}` : '/api/admin/subjects';
            const r = await fetch(url, { method: this.editId ? 'PUT' : 'POST', headers: { Authorization: `Bearer ${this.token}`, 'Content-Type': 'application/json', Accept: 'application/json' }, body: JSON.stringify(this.form) });
            if (r.ok) { this.modal.hide(); this.fetchSubjects(); }
        },
        async deleteSubject(s) {
            if (!confirm(`ลบวิชา ${s.name_th}?`)) return;
            const r = await fetch(`/api/admin/subjects/${s.id}`, { method: 'DELETE', headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' } });
            const data = await r.json();
            if (!r.ok) { alert(data.message); } else { this.fetchSubjects(); }
        }
    }
};

const AssignmentManager = {
    template: `
    <div>
        <h4 class="fw-bold mb-4"><i class="bi bi-link-45deg me-2 text-primary"></i>ผู้รับผิดชอบรายวิชา (วิชา : อาจารย์)</h4>
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3">เพิ่มการผูกรายวิชา</h6>
                <div class="row g-3 align-items-end">
                    <div class="col-md-4"><label class="form-label">รายวิชา *</label>
                        <select class="form-select" v-model="form.subject_id">
                            <option value="">-- เลือกรายวิชา --</option>
                            <option v-for="s in subjects" :key="s.id" :value="s.id">[{{ s.subject_code }}] {{ s.name_th }}</option>
                        </select>
                    </div>
                    <div class="col-md-4"><label class="form-label">อาจารย์ *</label>
                        <select class="form-select" v-model="form.teacher_id">
                            <option value="">-- เลือกอาจารย์ --</option>
                            <option v-for="t in teachers" :key="t.id" :value="t.id">{{ t.first_name_th }} {{ t.last_name_th }}</option>
                        </select>
                    </div>
                    <div class="col-md-2"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" v-model="form.is_primary" id="isPrimary"><label class="form-check-label" for="isPrimary">อาจารย์หลัก</label></div></div>
                    <div class="col-md-2"><button class="btn btn-primary w-100" @click="assign">ผูกรายวิชา</button></div>
                </div>
                <div v-if="message" class="alert mt-2 py-2" :class="message.type === 'success' ? 'alert-success' : 'alert-danger'">{{ message.text }}</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>รายวิชา</th><th>อาจารย์</th><th>อาจารย์หลัก</th><th>จัดการ</th></tr></thead>
                    <tbody>
                        <tr v-for="a in assignments" :key="a.id">
                            <td><span class="badge bg-info me-1">{{ a.subject?.subject_code }}</span>{{ a.subject?.name_th }}</td>
                            <td>{{ a.teacher?.first_name_th }} {{ a.teacher?.last_name_th }}</td>
                            <td><span v-if="a.is_primary" class="badge bg-warning">อาจารย์หลัก</span><span v-else class="text-muted">-</span></td>
                            <td><button class="btn btn-sm btn-outline-danger" @click="remove(a)"><i class="bi bi-trash"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>`,
    props: ['token'],
    data() { return { assignments: [], subjects: [], teachers: [], form: { subject_id:'', teacher_id:'', is_primary:false }, message: null }; },
    mounted() { this.fetchAll(); },
    methods: {
        async fetchAll() {
            const [a, s, t] = await Promise.all([
                fetch('/api/admin/subject-teachers?per_page=100', { headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' } }),
                fetch('/api/admin/subjects?per_page=100', { headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' } }),
                fetch('/api/admin/teachers?per_page=100', { headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' } }),
            ]);
            this.assignments = (await a.json()).data || [];
            this.subjects    = (await s.json()).data || [];
            this.teachers    = (await t.json()).data || [];
        },
        async assign() {
            const r = await fetch('/api/admin/subject-teachers', { method: 'POST', headers: { Authorization: `Bearer ${this.token}`, 'Content-Type': 'application/json', Accept: 'application/json' }, body: JSON.stringify(this.form) });
            const data = await r.json();
            this.message = { type: r.ok ? 'success' : 'error', text: data.message };
            if (r.ok) { this.form = { subject_id:'', teacher_id:'', is_primary:false }; this.fetchAll(); }
        },
        async remove(a) {
            if (!confirm('ยืนยันยกเลิกการผูก?')) return;
            await fetch(`/api/admin/subject-teachers/${a.id}`, { method: 'DELETE', headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' } });
            this.fetchAll();
        }
    }
};

const app = createApp({
    setup() {
        const menuItems = [
            { key: 'dashboard', label: 'Dashboard', path: '/admin', icon: 'bi bi-speedometer2' },
            { key: 'teachers', label: 'จัดการอาจารย์', path: '/admin/teachers', icon: 'bi bi-person-badge' },
            { key: 'subjects', label: 'จัดการรายวิชา', path: '/admin/subjects', icon: 'bi bi-book' },
            { key: 'assignments', label: 'ผู้รับผิดชอบวิชา', path: '/admin/assignments', icon: 'bi bi-link-45deg' },
            { key: 'students', label: 'รายชื่อนักเรียน', path: '/admin/students', icon: 'bi bi-people' },
        ];
        const page = ref(resolvePageFromPath(window.location.pathname));
        const user = ref(null);
        const token = ref(localStorage.getItem('token') || '');

        const currentMenu = computed(() => menuItems.find((item) => item.key === page.value) || menuItems[0]);

        onMounted(async () => {
            const u = localStorage.getItem('user');
            if (u) user.value = JSON.parse(u);
            if (!token.value || user.value?.role !== 'admin') {
                window.location.href = '/login';
                return;
            }

            window.addEventListener('popstate', () => {
                page.value = resolvePageFromPath(window.location.pathname);
            });
        });

        function resolvePageFromPath(pathname) {
            const segment = pathname.replace(/^\/admin\/?/, '').split('/')[0];
            return menuItems.some((item) => item.key === segment) ? segment : 'dashboard';
        }

        function navigate(nextPage) {
            const target = menuItems.find((item) => item.key === nextPage) || menuItems[0];
            page.value = target.key;
            if (window.location.pathname !== target.path) {
                window.history.pushState({}, '', target.path);
            }
        }

        async function logout() {
            await fetch('/api/auth/logout', {
                method: 'POST',
                headers: { Authorization: `Bearer ${token.value}`, Accept: 'application/json' },
            }).catch(() => {});
            localStorage.clear();
            window.location.href = '/login';
        }

        return { currentMenu, menuItems, navigate, page, user, token: token.value, logout };
    }
});

app.component('admin-dashboard', AdminDashboardComponent);
app.component('teacher-manager', TeacherManagerComponent);
app.component('subject-manager', SubjectManager);
app.component('assignment-manager', AssignmentManager);
app.component('student-list', StudentListComponent);

app.mount('#admin-app');
