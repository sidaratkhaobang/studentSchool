<template>
  <div>
    <h4 class="fw-bold mb-4"><i class="bi bi-journal-plus me-2 text-primary"></i>ลงทะเบียนรายวิชา</h4>

    <!-- Create Week Schedule -->
    <div class="card mb-4" v-if="!currentEnrollment">
      <div class="card-body">
        <h6 class="fw-bold mb-3">สร้างตารางเรียนสัปดาห์ใหม่</h6>
        <div class="row g-3 align-items-end">
          <div class="col-md-4">
            <label class="form-label">เลือกสัปดาห์</label>
            <input type="week" class="form-control" v-model="selectedWeek">
          </div>
          <div class="col-md-3">
            <button class="btn btn-primary w-100" @click="createSchedule" :disabled="!selectedWeek || creating">
              <span v-if="creating" class="spinner-border spinner-border-sm me-1"></span>
              สร้างตารางเรียน
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Current Enrollment -->
    <div v-if="currentEnrollment">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h6 class="fw-bold mb-0">
            ตารางสัปดาห์ {{ formatDate(currentEnrollment.week_start) }} - {{ formatDate(currentEnrollment.week_end) }}
          </h6>
          <small class="text-muted">รวม {{ totalHours }} ชั่วโมง</small>
        </div>
        <div class="d-flex gap-2">
          <span :class="statusBadge(currentEnrollment.status)" class="fs-6 px-3 py-2">
            {{ statusLabel(currentEnrollment.status) }}
          </span>
          <button v-if="canModifyCurrentEnrollment" class="btn btn-success"
                  @click="submitSchedule" :disabled="submitting">
            <i class="bi bi-send me-1"></i>{{ submitting ? 'กำลังส่ง...' : 'ส่งตาราง' }}
          </button>
          <button class="btn btn-outline-secondary btn-sm" @click="currentEnrollment = null">
            <i class="bi bi-plus me-1"></i>สัปดาห์ใหม่
          </button>
        </div>
      </div>

      <!-- Add Course Form -->
      <div v-if="currentEnrollment.status === 'rejected' && currentEnrollment.rejection_reason" class="alert alert-danger">
        <strong>ตารางเรียนไม่ผ่านการอนุมัติ:</strong> {{ currentEnrollment.rejection_reason }}
      </div>

      <div class="card mb-3" v-if="canModifyCurrentEnrollment">
        <div class="card-header fw-semibold bg-light">
          <i class="bi bi-plus-circle me-2 text-success"></i>เพิ่มรายวิชา
        </div>
        <div class="card-body">
          <div v-if="addError" class="alert alert-danger py-2 small">{{ addError }}</div>
          <div class="row g-3 align-items-end">
            <div class="col-md-4">
              <label class="form-label">เลือกวัน <span class="text-danger">*</span></label>
              <select class="form-select select2-control" v-select2="{ placeholder: '-- เลือกวัน --', allowClear: true }" v-model="addForm.day_of_week">
                <option value="">-- เลือกวัน --</option>
                <option v-for="d in days" :key="d.key" :value="d.key"
                        :disabled="getDailyHours(d.key) >= 6">
                  {{ d.label }} ({{ getDailyHours(d.key) }}/6 ชม.)
                </option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">เลือกรายวิชา <span class="text-danger">*</span></label>
              <select class="form-select select2-control" v-select2="{ placeholder: '-- เลือกรายวิชา --', allowClear: true }" v-model="addForm.subject_id">
                <option value="">-- เลือกรายวิชา --</option>
                <option v-for="s in subjects" :key="s.id" :value="s.id">
                  [{{ s.subject_code }}] {{ s.name_th }}
                </option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">จำนวนชั่วโมง</label>
              <input type="number" class="form-control" v-model.number="addForm.hours"
                     min="0.5" max="6" step="0.5">
            </div>
            <div class="col-md-2">
              <button class="btn btn-success w-100" @click="addCourse" :disabled="adding">
                <span v-if="adding" class="spinner-border spinner-border-sm"></span>
                <i v-else class="bi bi-plus-lg"></i> เพิ่ม
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Weekly Schedule Grid -->
      <div class="row g-3">
        <div class="col-md" v-for="d in days" :key="d.key">
          <div class="card h-100" :class="getDailyHours(d.key) >= 6 ? 'border-danger' : 'border-primary'">
            <div class="card-header text-center fw-bold py-2"
                 :class="getDailyHours(d.key) >= 6 ? 'bg-danger text-white' : 'bg-primary text-white'">
              {{ d.label }}<br>
              <small>{{ getDailyHours(d.key) }}/6 ชม.</small>
            </div>
            <div class="card-body p-2">
              <div v-if="getDayCourses(d.key).length === 0" class="text-center text-muted small py-2">ว่าง</div>
              <div v-for="c in getDayCourses(d.key)" :key="c.id"
                   class="d-flex justify-content-between align-items-start bg-light rounded p-2 mb-1">
                <div>
                  <div class="small fw-semibold">{{ c.subject?.name_th }}</div>
                  <div class="smaller text-muted">{{ c.hours }} ชม.</div>
                  <a v-if="c.subject?.material_path" class="smaller d-inline-block" :href="'/storage/' + c.subject.material_path" target="_blank">
                    <i class="bi bi-download me-1"></i>เอกสาร
                  </a>
                </div>
                <button v-if="canModifyCurrentEnrollment"
                        class="btn btn-link btn-sm text-danger p-0" @click="removeCourse(c)">
                  <i class="bi bi-x-circle"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { confirmDialog, errorDialog, warningDialog, successToast } from '../../utils/dialogs';

export default {
  name: 'EnrollmentForm',
  props: ['token'],
  data() {
    return {
      currentEnrollment: null,
      courses: [],
      subjects: [],
      selectedWeek: '',
      creating: false,
      submitting: false,
      adding: false,
      addError: '',
      addForm: { day_of_week: '', subject_id: '', hours: 1 },
      days: [
        { key: 'monday', label: 'จันทร์' }, { key: 'tuesday', label: 'อังคาร' },
        { key: 'wednesday', label: 'พุธ' }, { key: 'thursday', label: 'พฤหัสฯ' },
        { key: 'friday', label: 'ศุกร์' }
      ]
    };
  },
  computed: {
    totalHours() { return this.courses.reduce((s, c) => s + parseFloat(c.hours), 0); },
    canModifyCurrentEnrollment() {
      return ['draft', 'rejected'].includes(this.currentEnrollment?.status);
    }
  },
  mounted() { this.fetchSubjects(); this.fetchCurrentEnrollment(); },
  methods: {
    async fetchSubjects() {
      const r = await fetch('/api/student/subjects', { headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' } });
      const data = await r.json();
      this.subjects = data.subjects || [];
    },
    async fetchCurrentEnrollment() {
      const r = await fetch('/api/student/enrollments?per_page=1', { headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' } });
      const data = await r.json();
      if (['draft', 'rejected'].includes(data.data?.[0]?.status)) {
        this.currentEnrollment = data.data[0];
        this.fetchEnrollmentDetail(data.data[0].id);
      }
    },
    async fetchEnrollmentDetail(id) {
      const r = await fetch(`/api/student/enrollments/${id}`, { headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' } });
      const data = await r.json();
      this.currentEnrollment = data.enrollment;
      this.courses = data.enrollment?.courses || [];
    },
    async createSchedule() {
      this.creating = true;
      const [year, week] = this.selectedWeek.split('-W');
      const weekStart = this.getMonday(parseInt(year), parseInt(week));
      const r = await fetch('/api/student/enrollments', {
        method: 'POST',
        headers: { Authorization: `Bearer ${this.token}`, 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify({ week_start: weekStart })
      });
      const data = await r.json();
      if (r.ok) { this.currentEnrollment = data.enrollment; this.courses = []; successToast(data.message || 'สร้างตารางเรียนแล้ว'); }
      else { await errorDialog(data.message || 'สร้างตารางเรียนไม่สำเร็จ'); }
      this.creating = false;
    },
    async addCourse() {
      if (!this.addForm.day_of_week || !this.addForm.subject_id) { this.addError = 'กรุณาเลือกวันและรายวิชา'; return; }
      this.adding = true; this.addError = '';
      const r = await fetch(`/api/student/enrollments/${this.currentEnrollment.id}/courses`, {
        method: 'POST',
        headers: { Authorization: `Bearer ${this.token}`, 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify(this.addForm)
      });
      const data = await r.json();
      if (r.ok) { this.fetchEnrollmentDetail(this.currentEnrollment.id); this.addForm = { day_of_week:'', subject_id:'', hours:1 }; }
      else { this.addError = data.message || 'เกิดข้อผิดพลาด'; }
      this.adding = false;
    },
    async removeCourse(c) {
      const confirmed = await confirmDialog({
        title: 'ลบรายวิชา',
        text: c.subject?.name_th || 'ยืนยันลบรายวิชานี้?',
        icon: 'warning',
        confirmButtonText: 'ลบ',
        confirmButtonColor: '#dc3545',
      });
      if (!confirmed) return;
      await fetch(`/api/student/enrollments/${this.currentEnrollment.id}/courses/${c.id}`, {
        method: 'DELETE', headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' }
      });
      successToast('ลบรายวิชาแล้ว');
      this.fetchEnrollmentDetail(this.currentEnrollment.id);
    },
    async submitSchedule() {
      if (this.courses.length === 0) { await warningDialog('กรุณาเพิ่มรายวิชาก่อนส่งตาราง'); return; }
      this.submitting = true;
      const r = await fetch(`/api/student/enrollments/${this.currentEnrollment.id}/submit`, {
        method: 'PUT', headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' }
      });
      const data = await r.json();
      if (r.ok) { this.currentEnrollment = data.enrollment; successToast(data.message || 'ส่งตารางเรียนแล้ว'); }
      else { await errorDialog(data.message || 'ส่งตารางเรียนไม่สำเร็จ'); }
      this.submitting = false;
    },
    getDayCourses(day) { return this.courses.filter(c => c.day_of_week === day); },
    getDailyHours(day) { return this.getDayCourses(day).reduce((s, c) => s + parseFloat(c.hours), 0); },
    formatDate(d) { return d ? new Date(d).toLocaleDateString('th-TH', { day:'numeric', month:'short' }) : '-'; },
    statusBadge(s) { return { draft:'badge bg-secondary', submitted:'badge bg-info', approved:'badge bg-success', rejected:'badge bg-danger' }[s]; },
    statusLabel(s) { return { draft:'ร่าง', submitted:'รออนุมัติ', approved:'อนุมัติแล้ว', rejected:'ไม่อนุมัติ' }[s] || s; },
    getMonday(year, week) {
      const jan4 = new Date(year, 0, 4);
      const dayOfWeek = jan4.getDay() || 7;
      const monday = new Date(jan4.getTime() - (dayOfWeek - 1) * 86400000 + (week - 1) * 7 * 86400000);
      return monday.toISOString().split('T')[0];
    }
  }
};
</script>
