<template>
  <div>
    <h4 class="fw-bold mb-4"><i class="bi bi-people me-2 text-primary"></i>รายชื่อนักเรียน</h4>
    <!-- Filter -->
    <div class="card mb-3">
      <div class="card-body py-2">
        <div class="row g-2">
          <div class="col-md-5">
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-search"></i></span>
              <input type="text" class="form-control" v-model="search" placeholder="ค้นหาชื่อ, username..." @input="fetchStudents">
            </div>
          </div>
          <div class="col-md-3">
            <select class="form-select select2-control" v-select2="{ placeholder: '-- สถานะทั้งหมด --', allowClear: true }" v-model="filterStatus" @change="fetchStudents">
              <option value="">-- สถานะทั้งหมด --</option>
              <option value="pending">รออนุมัติ</option>
              <option value="approved">อนุมัติแล้ว</option>
              <option value="rejected">ปฏิเสธ</option>
            </select>
          </div>
        </div>
      </div>
    </div>
    <!-- Table -->
    <div class="card">
      <div class="card-body p-0">
        <div v-if="loading" class="text-center py-5"><div class="spinner-border text-primary"></div></div>
        <table v-else class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>ชื่อ-นามสกุล</th>
              <th>Username</th>
              <th>ชั้น</th>
              <th>อาจารย์ที่ปรึกษา</th>
              <th>ลงทะเบียน</th>
              <th>สถานะ</th>
              <th>จัดการ</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="students.length === 0">
              <td colspan="8" class="text-center text-muted py-4">ไม่พบข้อมูลนักเรียน</td>
            </tr>
            <tr v-for="(s, i) in students" :key="s.id">
              <td class="text-muted small">{{ meta.from + i }}</td>
              <td>
                <div class="fw-semibold">{{ s.first_name_th }} {{ s.last_name_th }}</div>
                <div class="text-muted small">{{ s.first_name_en }} {{ s.last_name_en }}</div>
              </td>
              <td><code>{{ s.user?.username }}</code></td>
              <td>{{ s.grade_level }}</td>
              <td>{{ s.advisor ? s.advisor.first_name_th + ' ' + s.advisor.last_name_th : '-' }}</td>
              <td><span class="badge bg-secondary">{{ s.weekly_enrollments_count }} สัปดาห์</span></td>
              <td>
                <span :class="statusBadge(s.status)">{{ statusLabel(s.status) }}</span>
              </td>
              <td>
                <div class="btn-group btn-group-sm">
                  <button class="btn btn-outline-primary" @click="viewStudent(s)" title="ดูข้อมูล">
                    <i class="bi bi-eye"></i>
                  </button>
                  <template v-if="s.status === 'pending'">
                  <button class="btn btn-success" @click="updateStatus(s, 'approved')" title="อนุมัติ">
                    <i class="bi bi-check-lg"></i>
                  </button>
                  <button class="btn btn-danger" @click="updateStatus(s, 'rejected')" title="ปฏิเสธ">
                    <i class="bi bi-x-lg"></i>
                  </button>
                  </template>
                  <button v-else class="btn btn-outline-secondary" @click="updateStatus(s, 'pending')" title="รีเซ็ตสถานะ">
                    <i class="bi bi-arrow-counterclockwise"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="modal fade" id="studentDetailModal" tabindex="-1">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title fw-bold">
              <i class="bi bi-person-lines-fill me-2 text-primary"></i>ข้อมูลนักเรียน
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div v-if="detailLoading" class="text-center py-5">
              <div class="spinner-border text-primary"></div>
            </div>
            <div v-else-if="selectedStudent">
              <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                  <h5 class="fw-bold mb-1">{{ selectedStudent.first_name_th }} {{ selectedStudent.last_name_th }}</h5>
                  <div class="text-muted">{{ selectedStudent.first_name_en }} {{ selectedStudent.last_name_en }}</div>
                </div>
                <span :class="statusBadge(selectedStudent.status)">{{ statusLabel(selectedStudent.status) }}</span>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <div class="detail-label">Username</div>
                  <div><code>{{ selectedStudent.user?.username || '-' }}</code></div>
                </div>
                <div class="col-md-6">
                  <div class="detail-label">อีเมล</div>
                  <div>{{ selectedStudent.email || selectedStudent.user?.email || '-' }}</div>
                </div>
                <div class="col-md-4">
                  <div class="detail-label">ชั้น</div>
                  <div>{{ selectedStudent.grade_level || '-' }}</div>
                </div>
                <div class="col-md-4">
                  <div class="detail-label">อายุ</div>
                  <div>{{ selectedStudent.age || '-' }}</div>
                </div>
                <div class="col-md-4">
                  <div class="detail-label">วันเกิด</div>
                  <div>{{ formatDate(selectedStudent.date_of_birth) }}</div>
                </div>
                <div class="col-md-6">
                  <div class="detail-label">เบอร์โทร</div>
                  <div>{{ selectedStudent.phone || '-' }}</div>
                </div>
                <div class="col-md-6">
                  <div class="detail-label">อาจารย์ที่ปรึกษา</div>
                  <div>{{ selectedStudent.advisor ? selectedStudent.advisor.first_name_th + ' ' + selectedStudent.advisor.last_name_th : '-' }}</div>
                </div>
              </div>

              <h6 class="fw-bold mt-4 mb-2">ประวัติการลงทะเบียน</h6>
              <div v-if="selectedStudent.weekly_enrollments?.length" class="table-responsive">
                <table class="table table-sm align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>สัปดาห์</th>
                      <th>สถานะ</th>
                      <th>จำนวนวิชา</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="e in selectedStudent.weekly_enrollments" :key="e.id">
                      <td>{{ formatDate(e.week_start) }} - {{ formatDate(e.week_end) }}</td>
                      <td><span :class="enrollmentStatusBadge(e.status)">{{ enrollmentStatusLabel(e.status) }}</span></td>
                      <td>{{ e.courses?.length || 0 }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div v-else class="text-muted small">ยังไม่มีประวัติการลงทะเบียน</div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { confirmDialog, errorDialog, successToast } from '../../utils/dialogs';

export default {
  name: 'StudentList',
  props: ['token'],
  data() {
    return {
      students: [],
      meta: { from:1, to:0, total:0 },
      loading: false,
      search: '',
      filterStatus: '',
      selectedStudent: null,
      detailLoading: false,
      detailModal: null,
    };
  },
  mounted() {
    this.fetchStudents();
    this.detailModal = new bootstrap.Modal(document.getElementById('studentDetailModal'));
  },
  methods: {
    async fetchStudents(page = 1) {
      this.loading = true;
      const params = new URLSearchParams({ page, search: this.search, ...(this.filterStatus && { status: this.filterStatus }) });
      const r = await fetch(`/api/admin/students?${params}`, { headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' } });
      const data = await r.json();
      this.students = data.data;
      this.meta = { from: data.from, to: data.to, total: data.total };
      this.loading = false;
    },
    async viewStudent(s) {
      this.selectedStudent = null;
      this.detailLoading = true;
      this.detailModal.show();
      const r = await fetch(`/api/admin/students/${s.id}`, { headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' } });
      const data = await r.json();
      if (r.ok) {
        this.selectedStudent = data.student;
      } else {
        this.detailModal.hide();
        await errorDialog(data.message || 'โหลดข้อมูลนักเรียนไม่สำเร็จ');
      }
      this.detailLoading = false;
    },
    async updateStatus(s, status) {
      const labels = { approved: 'อนุมัติ', rejected: 'ปฏิเสธ', pending: 'รีเซ็ตสถานะ' };
      const confirmed = await confirmDialog({
        title: `${labels[status]}นักเรียน`,
        text: `${s.first_name_th} ${s.last_name_th}`,
        icon: status === 'approved' ? 'success' : 'warning',
        confirmButtonText: labels[status],
        confirmButtonColor: status === 'rejected' ? '#dc3545' : '#198754',
      });
      if (!confirmed) return;
      const r = await fetch(`/api/admin/students/${s.id}/status`, {
        method: 'PUT', headers: { Authorization: `Bearer ${this.token}`, 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify({ status })
      });
      const data = await r.json();
      if (r.ok) {
        successToast(data.message || 'อัปเดตสถานะแล้ว');
        this.fetchStudents();
      } else {
        await errorDialog(data.message || 'อัปเดตสถานะไม่สำเร็จ');
      }
    },
    statusBadge(s) { return { pending: 'badge bg-warning', approved: 'badge bg-success', rejected: 'badge bg-danger' }[s] || 'badge bg-secondary'; },
    statusLabel(s) { return { pending: 'รออนุมัติ', approved: 'อนุมัติแล้ว', rejected: 'ปฏิเสธ' }[s] || s; },
    enrollmentStatusBadge(s) { return { draft: 'badge bg-secondary', submitted: 'badge bg-info', approved: 'badge bg-success' }[s] || 'badge bg-secondary'; },
    enrollmentStatusLabel(s) { return { draft: 'ร่าง', submitted: 'ส่งแล้ว', approved: 'อนุมัติแล้ว' }[s] || s; },
    formatDate(d) { return d ? new Date(d).toLocaleDateString('th-TH', { day: 'numeric', month: 'short', year: 'numeric' }) : '-'; },
  }
};
</script>

<style scoped>
.detail-label {
  color: #6c757d;
  font-size: 0.8rem;
  font-weight: 600;
  margin-bottom: 0.15rem;
}
</style>
