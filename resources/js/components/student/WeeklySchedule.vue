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
                        <div class="smaller text-muted">{{ c.hours }} ชม.</div>
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
  mounted() { this.fetchDashboard(); },
  methods: {
    async fetchDashboard() {
      this.loading = true;
      const r = await fetch('/api/student/dashboard', { headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' } });
      this.dashboard = await r.json();
      this.loading = false;
    },
    formatDate(d) { return d ? new Date(d).toLocaleDateString('th-TH', { day:'numeric', month:'short', year:'numeric' }) : '-'; },
    statusBadge(s) { return { draft:'badge bg-secondary', submitted:'badge bg-info', approved:'badge bg-success' }[s] || 'badge bg-light text-dark'; },
    statusLabel(s) { return { draft:'ร่าง', submitted:'รออนุมัติ', approved:'อนุมัติแล้ว' }[s] || s; }
  }
};
</script>
