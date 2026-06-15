<template>
  <div>
    <h4 class="fw-bold mb-4"><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard</h4>

    <div class="row g-4 mb-4" v-if="stats">
      <!-- Student Stats -->
      <div class="col-md-3">
        <div class="card stat-card border-primary h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <p class="text-muted small mb-1">นักเรียนทั้งหมด</p>
                <h2 class="fw-bold text-primary">{{ stats.students.total }}</h2>
              </div>
              <i class="bi bi-people-fill fs-1 text-primary opacity-25"></i>
            </div>
            <div class="mt-2 small">
              <span class="badge bg-warning me-1">รออนุมัติ {{ stats.students.pending }}</span>
              <span class="badge bg-success">อนุมัติแล้ว {{ stats.students.approved }}</span>
            </div>
          </div>
        </div>
      </div>
      <!-- Teacher Stats -->
      <div class="col-md-3">
        <div class="card stat-card border-success h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <p class="text-muted small mb-1">อาจารย์ทั้งหมด</p>
                <h2 class="fw-bold text-success">{{ stats.teachers.total }}</h2>
              </div>
              <i class="bi bi-person-badge-fill fs-1 text-success opacity-25"></i>
            </div>
            <div class="mt-2 small text-muted">Active {{ stats.teachers.active }} คน</div>
          </div>
        </div>
      </div>
      <!-- Subject Stats -->
      <div class="col-md-3">
        <div class="card stat-card border-info h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <p class="text-muted small mb-1">รายวิชาทั้งหมด</p>
                <h2 class="fw-bold text-info">{{ stats.subjects.total }}</h2>
              </div>
              <i class="bi bi-book-fill fs-1 text-info opacity-25"></i>
            </div>
            <div class="mt-2 small text-muted">Active {{ stats.subjects.active }} วิชา</div>
          </div>
        </div>
      </div>
      <!-- Enrollment Stats -->
      <div class="col-md-3">
        <div class="card stat-card border-warning h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <p class="text-muted small mb-1">ลงทะเบียนสัปดาห์นี้</p>
                <h2 class="fw-bold text-warning">{{ stats.enrollments.this_week }}</h2>
              </div>
              <i class="bi bi-calendar-check-fill fs-1 text-warning opacity-25"></i>
            </div>
            <div class="mt-2 small">
              <span class="badge bg-info me-1">ส่งแล้ว {{ stats.enrollments.submitted }}</span>
              <span class="badge bg-success">อนุมัติ {{ stats.enrollments.approved }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Weekly Trend -->
    <div class="card" v-if="weeklyTrend.length">
      <div class="card-header fw-semibold">
        <i class="bi bi-bar-chart me-2"></i>จำนวนการลงทะเบียน 4 สัปดาห์ล่าสุด
      </div>
      <div class="card-body">
        <div class="d-flex gap-4 justify-content-around">
          <div v-for="w in weeklyTrend" :key="w.week" class="text-center">
            <div class="bg-primary rounded" :style="{width:'60px', height: Math.max(w.count * 3, 8) + 'px', margin: '0 auto 8px'}"></div>
            <div class="fw-bold fs-5">{{ w.count }}</div>
            <div class="text-muted small">{{ w.week }}</div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary"></div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminDashboard',
  props: ['token'],
  data() { return { stats: null, weeklyTrend: [], loading: true }; },
  mounted() { this.fetchDashboard(); },
  methods: {
    async fetchDashboard() {
      this.loading = true;
      const r = await fetch('/api/admin/dashboard', { headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' } });
      const data = await r.json();
      this.stats = data.stats;
      this.weeklyTrend = data.weekly_trend;
      this.loading = false;
    }
  }
};
</script>
