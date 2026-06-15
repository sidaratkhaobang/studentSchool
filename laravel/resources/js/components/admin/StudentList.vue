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
            <select class="form-select" v-model="filterStatus" @change="fetchStudents">
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
                <div class="btn-group btn-group-sm" v-if="s.status === 'pending'">
                  <button class="btn btn-success" @click="updateStatus(s, 'approved')" title="อนุมัติ">
                    <i class="bi bi-check-lg"></i>
                  </button>
                  <button class="btn btn-danger" @click="updateStatus(s, 'rejected')" title="ปฏิเสธ">
                    <i class="bi bi-x-lg"></i>
                  </button>
                </div>
                <button v-else class="btn btn-sm btn-outline-secondary" @click="updateStatus(s, 'pending')">
                  รีเซ็ต
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'StudentList',
  props: ['token'],
  data() { return { students: [], meta: { from:1, to:0, total:0 }, loading: false, search: '', filterStatus: '' }; },
  mounted() { this.fetchStudents(); },
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
    async updateStatus(s, status) {
      const labels = { approved: 'อนุมัติ', rejected: 'ปฏิเสธ', pending: 'รีเซ็ตสถานะ' };
      if (!confirm(`ยืนยัน${labels[status]}นักเรียน ${s.first_name_th}?`)) return;
      const r = await fetch(`/api/admin/students/${s.id}/status`, {
        method: 'PUT', headers: { Authorization: `Bearer ${this.token}`, 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify({ status })
      });
      if (r.ok) this.fetchStudents();
    },
    statusBadge(s) { return { pending: 'badge bg-warning', approved: 'badge bg-success', rejected: 'badge bg-danger' }[s] || 'badge bg-secondary'; },
    statusLabel(s) { return { pending: 'รออนุมัติ', approved: 'อนุมัติแล้ว', rejected: 'ปฏิเสธ' }[s] || s; }
  }
};
</script>
