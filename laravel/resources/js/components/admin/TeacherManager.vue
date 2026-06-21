<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="fw-bold mb-0"><i class="bi bi-person-badge me-2 text-primary"></i>จัดการอาจารย์</h4>
      <button class="btn btn-primary" @click="openModal()">
        <i class="bi bi-plus-lg me-1"></i>เพิ่มอาจารย์
      </button>
    </div>

    <!-- Search -->
    <div class="card mb-3">
      <div class="card-body py-2">
        <div class="row g-2 align-items-center">
          <div class="col-md-6">
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-search"></i></span>
              <input type="text" class="form-control" v-model="search" placeholder="ค้นหาชื่อ อีเมล..." @input="fetchTeachers">
            </div>
          </div>
          <div class="col-md-3">
            <select class="form-select select2-control" v-select2="{ placeholder: '-- สถานะทั้งหมด --', allowClear: true }" v-model="filterActive" @change="fetchTeachers">
              <option value="">-- สถานะทั้งหมด --</option>
              <option value="1">Active</option>
              <option value="0">Inactive</option>
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
              <th>อีเมล</th>
              <th>เบอร์โทร</th>
              <th>วิชาที่รับผิดชอบ</th>
              <th>สถานะ</th>
              <th>จัดการ</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="teachers.length === 0">
              <td colspan="7" class="text-center text-muted py-4">ไม่พบข้อมูลอาจารย์</td>
            </tr>
            <tr v-for="(t, i) in teachers" :key="t.id">
              <td class="text-muted small">{{ meta.from + i }}</td>
              <td>
                <div class="fw-semibold">{{ t.first_name_th }} {{ t.last_name_th }}</div>
                <div class="text-muted small">{{ t.first_name_en }} {{ t.last_name_en }}</div>
              </td>
              <td>{{ t.email }}</td>
              <td>{{ t.phone || '-' }}</td>
              <td><span class="badge bg-info">{{ t.subjects_count }} วิชา</span></td>
              <td>
                <span :class="t.is_active ? 'badge bg-success' : 'badge bg-secondary'">
                  {{ t.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td>
                <button class="btn btn-sm btn-outline-primary me-1" @click="openModal(t)">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" @click="deleteTeacher(t)">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Pagination -->
      <div class="card-footer d-flex justify-content-between align-items-center" v-if="meta.last_page > 1">
        <small class="text-muted">แสดง {{ meta.from }}-{{ meta.to }} จาก {{ meta.total }} รายการ</small>
        <nav>
          <ul class="pagination pagination-sm mb-0">
            <li class="page-item" :class="{disabled: meta.current_page === 1}">
              <a class="page-link" @click="fetchTeachers(meta.current_page - 1)">‹</a>
            </li>
            <li class="page-item active">
              <span class="page-link">{{ meta.current_page }}/{{ meta.last_page }}</span>
            </li>
            <li class="page-item" :class="{disabled: meta.current_page === meta.last_page}">
              <a class="page-link" @click="fetchTeachers(meta.current_page + 1)">›</a>
            </li>
          </ul>
        </nav>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="teacherModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title fw-bold">{{ editId ? 'แก้ไขอาจารย์' : 'เพิ่มอาจารย์ใหม่' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div v-if="formErrors.general" class="alert alert-danger">{{ formErrors.general }}</div>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">ชื่อ (ภาษาไทย) <span class="text-danger">*</span></label>
                <input type="text" class="form-control" :class="{'is-invalid': formErrors.first_name_th}"
                       v-model="form.first_name_th">
                <div class="invalid-feedback">{{ formErrors.first_name_th?.[0] }}</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">นามสกุล (ภาษาไทย) <span class="text-danger">*</span></label>
                <input type="text" class="form-control" :class="{'is-invalid': formErrors.last_name_th}"
                       v-model="form.last_name_th">
                <div class="invalid-feedback">{{ formErrors.last_name_th?.[0] }}</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">First Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" v-model="form.first_name_en">
              </div>
              <div class="col-md-6">
                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" v-model="form.last_name_en">
              </div>
              <div class="col-md-6">
                <label class="form-label">อีเมล <span class="text-danger">*</span></label>
                <input type="email" class="form-control" :class="{'is-invalid': formErrors.email}"
                       v-model="form.email">
                <div class="invalid-feedback">{{ formErrors.email?.[0] }}</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">เบอร์โทร</label>
                <input type="text" class="form-control" v-model="form.phone">
              </div>
              <div class="col-12">
                <label class="form-label">ประวัติโดยย่อ</label>
                <textarea class="form-control" rows="3" v-model="form.bio"></textarea>
              </div>
              <div class="col-12">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" v-model="form.is_active" id="isActive">
                  <label class="form-check-label" for="isActive">Active</label>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
            <button class="btn btn-primary" @click="save" :disabled="saving">
              <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
              {{ saving ? 'กำลังบันทึก...' : 'บันทึก' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { confirmDialog, errorDialog, successToast } from '../../utils/dialogs';

export default {
  name: 'TeacherManager',
  props: ['token'],
  data() {
    return {
      teachers: [], meta: { current_page:1, last_page:1, from:1, to:0, total:0 },
      loading: false, saving: false, search: '', filterActive: '',
      editId: null, form: { first_name_th:'',last_name_th:'',first_name_en:'',last_name_en:'',email:'',phone:'',bio:'',is_active:true },
      formErrors: {}, modal: null
    };
  },
  mounted() {
    this.fetchTeachers();
    this.modal = new bootstrap.Modal(document.getElementById('teacherModal'));
  },
  methods: {
    async fetchTeachers(page = 1) {
      this.loading = true;
      const params = new URLSearchParams({ page, search: this.search, ...(this.filterActive !== '' && { is_active: this.filterActive }) });
      const r = await fetch(`/api/admin/teachers?${params}`, { headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' } });
      const data = await r.json();
      this.teachers = data.data;
      this.meta = { current_page: data.current_page, last_page: data.last_page, from: data.from, to: data.to, total: data.total };
      this.loading = false;
    },
    openModal(t = null) {
      this.formErrors = {};
      if (t) {
        this.editId = t.id;
        this.form = { first_name_th: t.first_name_th, last_name_th: t.last_name_th, first_name_en: t.first_name_en, last_name_en: t.last_name_en, email: t.email, phone: t.phone || '', bio: t.bio || '', is_active: t.is_active };
      } else {
        this.editId = null;
        this.form = { first_name_th:'',last_name_th:'',first_name_en:'',last_name_en:'',email:'',phone:'',bio:'',is_active:true };
      }
      this.modal.show();
    },
    async save() {
      this.saving = true; this.formErrors = {};
      const url = this.editId ? `/api/admin/teachers/${this.editId}` : '/api/admin/teachers';
      const method = this.editId ? 'PUT' : 'POST';
      const r = await fetch(url, { method, headers: { Authorization: `Bearer ${this.token}`, 'Content-Type': 'application/json', Accept: 'application/json' }, body: JSON.stringify(this.form) });
      const data = await r.json();
      if (!r.ok) { this.formErrors = data.errors || { general: data.message }; } else { this.modal.hide(); this.fetchTeachers(); }
      this.saving = false;
    },
    async deleteTeacher(t) {
      const confirmed = await confirmDialog({
        title: 'ลบอาจารย์',
        text: `${t.first_name_th} ${t.last_name_th}`,
        icon: 'warning',
        confirmButtonText: 'ลบ',
        confirmButtonColor: '#dc3545',
      });
      if (!confirmed) return;
      const r = await fetch(`/api/admin/teachers/${t.id}`, { method: 'DELETE', headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' } });
      const data = await r.json();
      if (!r.ok) {
        await errorDialog(data.message || 'ลบอาจารย์ไม่สำเร็จ');
      } else {
        successToast(data.message || 'ลบอาจารย์แล้ว');
        this.fetchTeachers();
      }
    }
  }
};
</script>
