import { computed, createApp, onMounted, ref } from 'vue';
import { confirmDialog, errorDialog, successToast, textareaDialog } from '../utils/dialogs';

const TeacherDashboard = {
    props: ['token'],
    template: `
    <div>
        <h4 class="fw-bold mb-4"><i class="bi bi-speedometer2 me-2 text-primary"></i>แดชบอร์ดอาจารย์</h4>
        <div v-if="loading" class="text-center py-5"><div class="spinner-border text-primary"></div></div>
        <div v-else>
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card h-100"><div class="card-body">
                        <div class="text-muted small">วิชาที่รับผิดชอบ</div>
                        <h3 class="fw-bold mb-0">{{ data.stats?.subjects || 0 }}</h3>
                    </div></div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100"><div class="card-body">
                        <div class="text-muted small">ห้องที่ประจำชั้น</div>
                        <h3 class="fw-bold mb-0">{{ data.stats?.classrooms || 0 }}</h3>
                    </div></div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100"><div class="card-body">
                        <div class="text-muted small">นักเรียนที่ดูแล</div>
                        <h3 class="fw-bold mb-0">{{ data.stats?.advising_students || 0 }}</h3>
                    </div></div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100"><div class="card-body">
                        <div class="text-muted small">รออนุมัติ</div>
                        <h3 class="fw-bold text-info mb-0">{{ data.stats?.pending_enrollments || 0 }}</h3>
                    </div></div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card h-100">
                        <div class="card-header fw-semibold"><i class="bi bi-house-door me-2"></i>ห้องที่ประจำชั้น</div>
                        <div class="card-body p-0">
                            <table class="table mb-0">
                                <thead class="table-light"><tr><th>ห้อง/ชั้น</th><th class="text-end">นักเรียน</th></tr></thead>
                                <tbody>
                                    <tr v-if="!data.classrooms?.length"><td colspan="2" class="text-center text-muted py-4">ยังไม่มีห้องที่ดูแล</td></tr>
                                    <tr v-for="room in data.classrooms" :key="room.grade_level">
                                        <td>{{ room.grade_level || '-' }}</td>
                                        <td class="text-end">{{ room.students_count }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card h-100">
                        <div class="card-header fw-semibold"><i class="bi bi-people me-2"></i>รายชื่อนักเรียนที่ดูแล</div>
                        <div class="card-body p-0">
                            <table class="table mb-0">
                                <thead class="table-light"><tr><th>ชื่อ</th><th>ห้อง/ชั้น</th><th>สถานะ</th></tr></thead>
                                <tbody>
                                    <tr v-if="!data.advising_students?.length"><td colspan="3" class="text-center text-muted py-4">ยังไม่มีนักเรียนในความดูแล</td></tr>
                                    <tr v-for="student in data.advising_students" :key="student.id">
                                        <td>{{ student.first_name_th }} {{ student.last_name_th }}</td>
                                        <td>{{ student.grade_level }}</td>
                                        <td><span :class="studentStatusBadge(student.status)">{{ studentStatusLabel(student.status) }}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>`,
    data() {
        return { data: {}, loading: true };
    },
    mounted() {
        this.fetchDashboard();
    },
    methods: {
        async fetchDashboard() {
            this.loading = true;
            const response = await fetch('/api/teacher/dashboard', {
                headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' },
            });
            this.data = await response.json();
            this.loading = false;
        },
        studentStatusBadge(status) {
            return { pending: 'badge bg-warning text-dark', approved: 'badge bg-success', rejected: 'badge bg-danger' }[status] || 'badge bg-secondary';
        },
        studentStatusLabel(status) {
            return { pending: 'รออนุมัติ', approved: 'อนุมัติแล้ว', rejected: 'ไม่อนุมัติ' }[status] || status;
        },
    },
};

const TeacherEnrollments = {
    props: ['token'],
    template: `
    <div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-clipboard-check me-2 text-primary"></i>อนุมัติตารางเรียน</h4>
            <select class="form-select w-auto" v-model="status" @change="fetchEnrollments">
                <option value="submitted">รออนุมัติ</option>
                <option value="approved">อนุมัติแล้ว</option>
                <option value="rejected">ไม่อนุมัติ</option>
                <option value="">ทั้งหมด</option>
            </select>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>นักเรียน</th><th>ห้อง/ชั้น</th><th>สัปดาห์</th><th>วิชา</th><th>สถานะ</th><th class="text-end">จัดการ</th></tr>
                        </thead>
                        <tbody>
                            <tr v-if="!enrollments.length"><td colspan="6" class="text-center text-muted py-4">ยังไม่มีตารางเรียน</td></tr>
                            <tr v-for="enrollment in enrollments" :key="enrollment.id">
                                <td>{{ enrollment.student?.first_name_th }} {{ enrollment.student?.last_name_th }}</td>
                                <td>{{ enrollment.student?.grade_level || '-' }}</td>
                                <td>{{ formatDate(enrollment.week_start) }} - {{ formatDate(enrollment.week_end) }}</td>
                                <td>{{ enrollment.courses?.length || enrollment.courses_count || 0 }} วิชา</td>
                                <td><span :class="statusBadge(enrollment.status)">{{ statusLabel(enrollment.status) }}</span></td>
                                <td class="text-end">
                                    <button class="btn btn-outline-primary btn-sm me-1" @click="viewDetail(enrollment)">
                                        <i class="bi bi-eye"></i> ดูรายละเอียด
                                    </button>
                                    <button v-if="enrollment.status === 'submitted'" class="btn btn-success btn-sm me-1" @click="approve(enrollment)">
                                        <i class="bi bi-check-lg"></i> อนุมัติ
                                    </button>
                                    <button v-if="enrollment.status === 'submitted'" class="btn btn-outline-danger btn-sm" @click="reject(enrollment)">
                                        <i class="bi bi-x-lg"></i> ไม่อนุมัติ
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="teacherEnrollmentDetailModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title fw-bold">รายละเอียดตารางเรียน</h5>
                            <div class="text-muted small" v-if="selectedDetail">
                                {{ selectedDetail.student?.first_name_th }} {{ selectedDetail.student?.last_name_th }}
                                · {{ selectedDetail.student?.grade_level || '-' }}
                                · {{ formatDate(selectedDetail.week_start) }} - {{ formatDate(selectedDetail.week_end) }}
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="detailLoading" class="text-center py-5">
                            <div class="spinner-border text-primary"></div>
                        </div>
                        <div v-else-if="selectedDetail">
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <div class="border rounded p-3 h-100">
                                        <div class="text-muted small">นักเรียน</div>
                                        <div class="fw-bold">{{ selectedDetail.student?.first_name_th }} {{ selectedDetail.student?.last_name_th }}</div>
                                        <div class="small text-muted">{{ selectedDetail.student?.user?.username || '-' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3 h-100">
                                        <div class="text-muted small">ห้อง/ชั้น</div>
                                        <div class="fw-bold">{{ selectedDetail.student?.grade_level || '-' }}</div>
                                        <div class="small text-muted">อาจารย์ที่ปรึกษา: {{ advisorName(selectedDetail.student?.advisor) }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3 h-100">
                                        <div class="text-muted small">สถานะตาราง</div>
                                        <span :class="statusBadge(selectedDetail.status)">{{ statusLabel(selectedDetail.status) }}</span>
                                        <div class="small text-muted mt-2">รวม {{ totalHours(selectedDetail.courses || []) }} ชั่วโมง</div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="selectedDetail.rejection_reason" class="alert alert-danger">
                                <strong>เหตุผลที่ไม่อนุมัติ:</strong> {{ selectedDetail.rejection_reason }}
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 160px;">วัน</th>
                                            <th>รายวิชา</th>
                                            <th style="width: 120px;">ชั่วโมง</th>
                                            <th style="width: 180px;">เวลา</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-for="day in days" :key="day.key">
                                            <tr v-if="dayCourses(day.key).length === 0">
                                                <td class="fw-semibold">{{ day.label }}</td>
                                                <td colspan="3" class="text-muted">ไม่มีรายวิชา</td>
                                            </tr>
                                            <tr v-for="(course, index) in dayCourses(day.key)" :key="course.id">
                                                <td v-if="index === 0" class="fw-semibold" :rowspan="dayCourses(day.key).length">{{ day.label }}</td>
                                                <td>
                                                    <div class="fw-semibold">[{{ course.subject?.subject_code }}] {{ course.subject?.name_th }}</div>
                                                    <div class="small text-muted" v-if="course.subject?.name_en">{{ course.subject.name_en }}</div>
                                                    <div class="small text-muted" v-if="courseTeacher(course)">อาจารย์ผู้สอน: {{ courseTeacher(course) }}</div>
                                                </td>
                                                <td>{{ course.hours }} ชม.</td>
                                                <td>{{ course.start_time || '-' }} - {{ course.end_time || '-' }}</td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ปิด</button>
                        <button v-if="selectedDetail?.status === 'submitted'" type="button" class="btn btn-outline-danger" @click="reject(selectedDetail)">
                            <i class="bi bi-x-lg me-1"></i>ไม่อนุมัติ
                        </button>
                        <button v-if="selectedDetail?.status === 'submitted'" type="button" class="btn btn-success" @click="approve(selectedDetail)">
                            <i class="bi bi-check-lg me-1"></i>อนุมัติ
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>`,
    data() {
        return {
            enrollments: [],
            status: 'submitted',
            selectedDetail: null,
            detailLoading: false,
            detailModal: null,
            days: [
                { key: 'monday', label: 'วันจันทร์' },
                { key: 'tuesday', label: 'วันอังคาร' },
                { key: 'wednesday', label: 'วันพุธ' },
                { key: 'thursday', label: 'วันพฤหัสบดี' },
                { key: 'friday', label: 'วันศุกร์' },
            ],
        };
    },
    mounted() {
        this.detailModal = new bootstrap.Modal(document.getElementById('teacherEnrollmentDetailModal'));
        this.fetchEnrollments();
    },
    methods: {
        async fetchEnrollments() {
            const params = new URLSearchParams({ per_page: 50 });
            if (this.status) params.set('status', this.status);
            const response = await fetch(`/api/teacher/enrollments?${params}`, {
                headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' },
            });
            const data = await response.json();
            this.enrollments = data.data || [];
        },
        async viewDetail(enrollment) {
            this.detailLoading = true;
            this.selectedDetail = enrollment;
            this.detailModal.show();
            const response = await fetch(`/api/teacher/enrollments/${enrollment.id}`, {
                headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' },
            });
            const data = await response.json();
            if (!response.ok) {
                await errorDialog(data.message || 'โหลดรายละเอียดตารางเรียนไม่สำเร็จ');
                this.detailModal.hide();
                this.detailLoading = false;
                return;
            }
            this.selectedDetail = data.enrollment;
            this.detailLoading = false;
        },
        async approve(enrollment) {
            const confirmed = await confirmDialog({
                title: 'อนุมัติตารางเรียน',
                text: `${enrollment.student?.first_name_th || ''} ${enrollment.student?.last_name_th || ''}`,
                icon: 'success',
                confirmButtonText: 'อนุมัติ',
            });
            if (!confirmed) return;
            await this.updateStatus(enrollment, { status: 'approved' });
        },
        async reject(enrollment) {
            const reason = await textareaDialog({
                title: 'ไม่อนุมัติตารางเรียน',
                inputLabel: 'เหตุผล',
                inputPlaceholder: 'ระบุเหตุผลเพื่อให้นักเรียนแก้ไขตารางเรียน',
                confirmButtonText: 'ไม่อนุมัติ',
            });
            if (!reason) return;
            await this.updateStatus(enrollment, { status: 'rejected', rejection_reason: reason });
        },
        async updateStatus(enrollment, payload) {
            const response = await fetch(`/api/teacher/enrollments/${enrollment.id}/status`, {
                method: 'PUT',
                headers: { Authorization: `Bearer ${this.token}`, 'Content-Type': 'application/json', Accept: 'application/json' },
                body: JSON.stringify(payload),
            });
            const data = await response.json();
            if (!response.ok) {
                await errorDialog(data.message || 'อัปเดตสถานะไม่สำเร็จ');
                return;
            }
            successToast(data.message || 'อัปเดตสถานะแล้ว');
            if (this.selectedDetail?.id === enrollment.id) {
                this.selectedDetail = data.enrollment;
            }
            this.fetchEnrollments();
        },
        dayCourses(day) {
            return (this.selectedDetail?.courses || []).filter((course) => course.day_of_week === day);
        },
        totalHours(courses) {
            return courses.reduce((sum, course) => sum + parseFloat(course.hours || 0), 0);
        },
        advisorName(advisor) {
            if (!advisor) return '-';
            return `${advisor.first_name_th || ''} ${advisor.last_name_th || ''}`.trim() || '-';
        },
        courseTeacher(course) {
            const teachers = course.subject?.teachers || [];
            if (!teachers.length) return '';
            const primary = teachers.find((teacher) => teacher.pivot?.is_primary) || teachers[0];
            return `${primary.first_name_th || ''} ${primary.last_name_th || ''}`.trim();
        },
        formatDate(value) {
            return value ? new Date(value).toLocaleDateString('th-TH', { day: 'numeric', month: 'short', year: 'numeric' }) : '-';
        },
        statusBadge(status) {
            return { submitted: 'badge bg-info', approved: 'badge bg-success', rejected: 'badge bg-danger' }[status] || 'badge bg-secondary';
        },
        statusLabel(status) {
            return { submitted: 'รออนุมัติ', approved: 'อนุมัติแล้ว', rejected: 'ไม่อนุมัติ' }[status] || status;
        },
    },
};

const TeacherSubjects = {
    props: ['token'],
    template: `
    <div>
        <h4 class="fw-bold mb-4"><i class="bi bi-journal-text me-2 text-primary"></i>รายวิชาที่รับผิดชอบ</h4>
        <div class="row g-3">
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-header fw-semibold">รายการวิชา</div>
                    <div class="list-group list-group-flush">
                        <button v-for="subject in subjects" :key="subject.id" class="list-group-item list-group-item-action"
                                :class="{ active: selected?.id === subject.id }" @click="selectSubject(subject)">
                            <div class="fw-semibold">[{{ subject.subject_code }}] {{ subject.name_th }}</div>
                            <small>{{ subject.name_en }}</small>
                        </button>
                        <div v-if="!subjects.length" class="text-center text-muted py-4">ยังไม่มีรายวิชาที่รับผิดชอบ</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card h-100" v-if="selected">
                    <div class="card-header fw-semibold">เนื้อหาและเอกสาร</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">เนื้อหาการเรียนการสอน</label>
                            <textarea class="form-control" rows="8" v-model="form.learning_content"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">แนบเอกสารการเรียน</label>
                            <input class="form-control" type="file" @change="onFileChange">
                            <div v-if="selected.material_path" class="form-text">
                                ไฟล์ปัจจุบัน: <a :href="'/storage/' + selected.material_path" target="_blank">ดาวน์โหลดเอกสาร</a>
                            </div>
                        </div>
                        <button class="btn btn-primary" :disabled="saving" @click="save">
                            <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                            บันทึกเนื้อหา
                        </button>
                    </div>
                </div>
                <div class="alert alert-info" v-else>เลือกรายวิชาทางซ้ายเพื่อจัดการเนื้อหา</div>
            </div>
        </div>
    </div>`,
    data() {
        return { subjects: [], selected: null, form: { learning_content: '', material_file: null }, saving: false };
    },
    mounted() {
        this.fetchSubjects();
    },
    methods: {
        async fetchSubjects() {
            const response = await fetch('/api/teacher/subjects', {
                headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' },
            });
            const data = await response.json();
            this.subjects = data.subjects || [];
            if (!this.selected && this.subjects.length) this.selectSubject(this.subjects[0]);
        },
        selectSubject(subject) {
            this.selected = subject;
            this.form = { learning_content: subject.learning_content || '', material_file: null };
        },
        onFileChange(event) {
            this.form.material_file = event.target.files?.[0] || null;
        },
        async save() {
            if (!this.selected) return;
            this.saving = true;
            const formData = new FormData();
            formData.append('learning_content', this.form.learning_content || '');
            if (this.form.material_file) formData.append('material_file', this.form.material_file);
            const response = await fetch(`/api/teacher/subjects/${this.selected.id}/content`, {
                method: 'POST',
                headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' },
                body: formData,
            });
            const data = await response.json();
            if (!response.ok) {
                await errorDialog(data.message || 'บันทึกเนื้อหาไม่สำเร็จ');
            } else {
                successToast(data.message || 'บันทึกเนื้อหาแล้ว');
                this.selected = data.subject;
                await this.fetchSubjects();
            }
            this.saving = false;
        },
    },
};

const app = createApp({
    setup() {
        const page = ref(pageFromPath());
        const user = ref(null);
        const teacher = ref(null);
        const token = ref(localStorage.getItem('token') || '');
        const teacherName = computed(() => {
            if (!teacher.value) return '';
            return `${teacher.value.first_name_th || ''} ${teacher.value.last_name_th || ''}`.trim();
        });

        onMounted(async () => {
            const storedUser = localStorage.getItem('user');
            if (storedUser) user.value = JSON.parse(storedUser);
            if (!token.value || user.value?.role !== 'teacher') {
                window.location.href = '/login';
                return;
            }
            await fetchMe();
        });

        function pageFromPath() {
            const segment = window.location.pathname.split('/').filter(Boolean)[1];
            return ['dashboard', 'enrollments', 'subjects'].includes(segment) ? segment : 'dashboard';
        }

        function navigate(nextPage) {
            page.value = nextPage;
            const path = nextPage === 'dashboard' ? '/teacher/dashboard' : `/teacher/${nextPage}`;
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
            teacher.value = data.user?.teacher || null;
            localStorage.setItem('user', JSON.stringify({
                id: data.user.id,
                username: data.user.username,
                email: data.user.email,
                role: data.user.role,
                teacher: teacher.value,
            }));
        }

        function logout() {
            fetch('/api/auth/logout', {
                method: 'POST',
                headers: { Authorization: `Bearer ${token.value}`, Accept: 'application/json' },
            });
            localStorage.clear();
            window.location.href = '/login';
        }

        window.addEventListener('popstate', () => {
            page.value = pageFromPath();
        });

        return { page, user, teacher, teacherName, token: token.value, navigate, logout };
    },
});

app.component('teacher-dashboard', TeacherDashboard);
app.component('teacher-enrollments', TeacherEnrollments);
app.component('teacher-subjects', TeacherSubjects);

app.mount('#teacher-app');
