<template>
  <div>
    <h4 class="fw-bold mb-4"><i class="bi bi-calendar-week me-2 text-primary"></i>ตารางเรียนสัปดาห์นี้</h4>

    <div v-if="loading" class="text-center py-5"><div class="spinner-border text-primary"></div></div>

    <div v-else>
      <!-- Week Info -->
      <div class="alert alert-info d-flex align-items-center mb-4">
        <i class="bi bi-info-circle me-2 fs-5"></i>
        <div>
          <strong>สัปดาห์ปัจจุบัน:</strong> {{ formatDate(dashboard.week_start) }} - {{ formatDate(dashboard.week_end) }}
          &nbsp;&nbsp;|&nbsp;&nbsp;
          <strong>รวม:</strong> {{ dashboard.total_hours_week }} ชั่วโมง
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <div class="card h-100">
            <div class="card-body">
              <div class="text-muted small mb-1">นักเรียน</div>
              <div class="fw-bold">{{ studentName }}</div>
              <span :class="studentStatusBadge" class="mt-2">{{ studentStatusLabel }}</span>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card h-100">
            <div class="card-body">
              <div class="text-muted small mb-1">ห้องเรียน / ชั้นเรียน</div>
              <div class="fw-bold">{{ dashboard.student?.grade_level || '-' }}</div>
              <div class="small text-muted mt-2">ใช้สำหรับจัดกลุ่มข้อมูลตารางเรียน</div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card h-100">
            <div class="card-body">
              <div class="text-muted small mb-1">อาจารย์ที่ปรึกษา</div>
              <div class="fw-bold">{{ advisorName }}</div>
              <div class="small text-muted mt-2">{{ dashboard.student?.advisor?.email || dashboard.student?.advisor?.phone || '-' }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Schedule Table -->
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered text-center">
              <thead class="table-primary">
                <tr>
                  <th v-for="d in days" :key="d.key" style="min-width: 150px;">
                    <div class="fw-bold">{{ d.label }}</div>
                    <div class="small text-muted" v-if="dashboard.schedule">
                      {{ dashboard.schedule[d.key]?.total_hours || 0 }}/6 ชั่วโมง
                      <div class="progress mt-1" style="height:4px">
                        <div class="progress-bar" :style="{width: ((dashboard.schedule[d.key]?.total_hours || 0)/6*100) + '%'}"></div>
                      </div>
                    </div>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td v-for="d in days" :key="d.key" class="align-top" style="min-height: 120px;">
                    <div v-if="dashboard.schedule?.[d.key]?.courses?.length">
                      <div v-for="c in dashboard.schedule[d.key].courses" :key="c.id"
                           class="badge bg-primary-subtle text-primary border border-primary rounded p-2 mb-1 d-block text-start">
                        <div class="fw-semibold small">{{ c.subject?.name_th }}</div>
                        <div class="smaller text-muted">{{ c.subject?.subject_code }} · {{ c.hours }} ชม.</div>
                        <div class="smaller text-muted" v-if="courseTeacher(c)">อาจารย์: {{ courseTeacher(c) }}</div>
                        <div class="smaller text-muted" v-if="c.start_time || c.end_time">
                          เวลา: {{ c.start_time || '-' }} - {{ c.end_time || '-' }}
                        </div>
                      </div>
                    </div>
                    <div v-else class="text-muted small py-3">ว่าง</div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Recent Enrollments -->
      <div class="card mt-4" v-if="dashboard.recent_enrollments?.length">
        <div class="card-header fw-semibold">
          <i class="bi bi-clock-history me-2"></i>ประวัติการลงทะเบียน
        </div>
        <div class="card-body p-0">
          <table class="table table-sm mb-0">
            <thead class="table-light">
              <tr><th>สัปดาห์</th><th>วิชา</th><th>สถานะ</th></tr>
            </thead>
            <tbody>
              <tr v-for="e in dashboard.recent_enrollments" :key="e.id">
                <td>{{ formatDate(e.week_start) }}</td>
                <td>{{ e.courses_count }} วิชา</td>
                <td><span :class="statusBadge(e.status)">{{ statusLabel(e.status) }}</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'StudentDashboard',
  props: ['token'],
  data() {
    return {
      dashboard: {},
      loading: true,
      days: [
        { key:'monday', label:'วันจันทร์' }, { key:'tuesday', label:'วันอังคาร' },
        { key:'wednesday', label:'วันพุธ' }, { key:'thursday', label:'วันพฤหัสบดี' },
        { key:'friday', label:'วันศุกร์' }
      ]
    };
  },
  computed: {
    advisorName() {
      const advisor = this.dashboard.student?.advisor;
      if (!advisor) return '-';
      return `${advisor.first_name_th || ''} ${advisor.last_name_th || ''}`.trim() || '-';
    },
    studentName() {
      const student = this.dashboard.student;
      if (!student) return '-';
      return `${student.first_name_th || ''} ${student.last_name_th || ''}`.trim() || '-';
    },
    studentStatusBadge() {
      return {
        pending: 'badge bg-warning text-dark',
        approved: 'badge bg-success',
        rejected: 'badge bg-danger',
      }[this.dashboard.student?.status] || 'badge bg-secondary';
    },
    studentStatusLabel() {
      return {
        pending: 'รออนุมัติ',
        approved: 'อนุมัติแล้ว',
        rejected: 'ไม่อนุมัติ',
      }[this.dashboard.student?.status] || '-';
    },
  },
  mounted() { this.fetchDashboard(); },
  methods: {
    async fetchDashboard() {
      this.loading = true;
      const r = await fetch('/api/student/dashboard', { headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' } });
      this.dashboard = await r.json();
      this.loading = false;
    },
    courseTeacher(course) {
      const teachers = course.subject?.teachers || [];
      if (!teachers.length) return '';
      const primary = teachers.find((teacher) => teacher.pivot?.is_primary) || teachers[0];
      return `${primary.first_name_th || ''} ${primary.last_name_th || ''}`.trim();
    },
    formatDate(d) { return d ? new Date(d).toLocaleDateString('th-TH', { day:'numeric', month:'short', year:'numeric' }) : '-'; },
    statusBadge(s) { return { draft:'badge bg-secondary', submitted:'badge bg-info', approved:'badge bg-success' }[s] || 'badge bg-light text-dark'; },
    statusLabel(s) { return { draft:'ร่าง', submitted:'รออนุมัติ', approved:'อนุมัติแล้ว' }[s] || s; }
  }
};
</script>
