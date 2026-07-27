import{c as p,r as o,o as f,u as g,e as c,s as m,y,q as _}from"./dialogs-CpUu2P3S.js";const w={props:["token"],template:`
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
    </div>`,data(){return{data:{},loading:!0}},mounted(){this.fetchDashboard()},methods:{async fetchDashboard(){this.loading=!0;const t=await fetch("/api/teacher/dashboard",{headers:{Authorization:`Bearer ${this.token}`,Accept:"application/json"}});this.data=await t.json(),this.loading=!1},studentStatusBadge(t){return{pending:"badge bg-warning text-dark",approved:"badge bg-success",rejected:"badge bg-danger"}[t]||"badge bg-secondary"},studentStatusLabel(t){return{pending:"รออนุมัติ",approved:"อนุมัติแล้ว",rejected:"ไม่อนุมัติ"}[t]||t}}},j={props:["token"],template:`
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
    </div>`,data(){return{enrollments:[],status:"submitted",selectedDetail:null,detailLoading:!1,detailModal:null,days:[{key:"monday",label:"วันจันทร์"},{key:"tuesday",label:"วันอังคาร"},{key:"wednesday",label:"วันพุธ"},{key:"thursday",label:"วันพฤหัสบดี"},{key:"friday",label:"วันศุกร์"}]}},mounted(){this.detailModal=new bootstrap.Modal(document.getElementById("teacherEnrollmentDetailModal")),this.fetchEnrollments()},methods:{async fetchEnrollments(){const t=new URLSearchParams({per_page:50});this.status&&t.set("status",this.status);const s=await(await fetch(`/api/teacher/enrollments?${t}`,{headers:{Authorization:`Bearer ${this.token}`,Accept:"application/json"}})).json();this.enrollments=s.data||[]},async viewDetail(t){this.detailLoading=!0,this.selectedDetail=t,this.detailModal.show();const e=await fetch(`/api/teacher/enrollments/${t.id}`,{headers:{Authorization:`Bearer ${this.token}`,Accept:"application/json"}}),s=await e.json();if(!e.ok){await c(s.message||"โหลดรายละเอียดตารางเรียนไม่สำเร็จ"),this.detailModal.hide(),this.detailLoading=!1;return}this.selectedDetail=s.enrollment,this.detailLoading=!1},async approve(t){var s,a;await _({title:"อนุมัติตารางเรียน",text:`${((s=t.student)==null?void 0:s.first_name_th)||""} ${((a=t.student)==null?void 0:a.last_name_th)||""}`,icon:"success",confirmButtonText:"อนุมัติ"})&&await this.updateStatus(t,{status:"approved"})},async reject(t){const e=await y({title:"ไม่อนุมัติตารางเรียน",inputLabel:"เหตุผล",inputPlaceholder:"ระบุเหตุผลเพื่อให้นักเรียนแก้ไขตารางเรียน",confirmButtonText:"ไม่อนุมัติ"});e&&await this.updateStatus(t,{status:"rejected",rejection_reason:e})},async updateStatus(t,e){var d;const s=await fetch(`/api/teacher/enrollments/${t.id}/status`,{method:"PUT",headers:{Authorization:`Bearer ${this.token}`,"Content-Type":"application/json",Accept:"application/json"},body:JSON.stringify(e)}),a=await s.json();if(!s.ok){await c(a.message||"อัปเดตสถานะไม่สำเร็จ");return}m(a.message||"อัปเดตสถานะแล้ว"),((d=this.selectedDetail)==null?void 0:d.id)===t.id&&(this.selectedDetail=a.enrollment),this.fetchEnrollments()},dayCourses(t){var e;return(((e=this.selectedDetail)==null?void 0:e.courses)||[]).filter(s=>s.day_of_week===t)},totalHours(t){return t.reduce((e,s)=>e+parseFloat(s.hours||0),0)},advisorName(t){return t&&`${t.first_name_th||""} ${t.last_name_th||""}`.trim()||"-"},courseTeacher(t){var a;const e=((a=t.subject)==null?void 0:a.teachers)||[];if(!e.length)return"";const s=e.find(d=>{var n;return(n=d.pivot)==null?void 0:n.is_primary})||e[0];return`${s.first_name_th||""} ${s.last_name_th||""}`.trim()},formatDate(t){return t?new Date(t).toLocaleDateString("th-TH",{day:"numeric",month:"short",year:"numeric"}):"-"},statusBadge(t){return{submitted:"badge bg-info",approved:"badge bg-success",rejected:"badge bg-danger"}[t]||"badge bg-secondary"},statusLabel(t){return{submitted:"รออนุมัติ",approved:"อนุมัติแล้ว",rejected:"ไม่อนุมัติ"}[t]||t}}},x={props:["token"],template:`
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
    </div>`,data(){return{subjects:[],selected:null,form:{learning_content:"",material_file:null},saving:!1}},mounted(){this.fetchSubjects()},methods:{async fetchSubjects(){const e=await(await fetch("/api/teacher/subjects",{headers:{Authorization:`Bearer ${this.token}`,Accept:"application/json"}})).json();this.subjects=e.subjects||[],!this.selected&&this.subjects.length&&this.selectSubject(this.subjects[0])},selectSubject(t){this.selected=t,this.form={learning_content:t.learning_content||"",material_file:null}},onFileChange(t){var e;this.form.material_file=((e=t.target.files)==null?void 0:e[0])||null},async save(){if(!this.selected)return;this.saving=!0;const t=new FormData;t.append("learning_content",this.form.learning_content||""),this.form.material_file&&t.append("material_file",this.form.material_file);const e=await fetch(`/api/teacher/subjects/${this.selected.id}/content`,{method:"POST",headers:{Authorization:`Bearer ${this.token}`,Accept:"application/json"},body:t}),s=await e.json();e.ok?(m(s.message||"บันทึกเนื้อหาแล้ว"),this.selected=s.subject,await this.fetchSubjects()):await c(s.message||"บันทึกเนื้อหาไม่สำเร็จ"),this.saving=!1}}},r=p({setup(){const t=o(n()),e=o(null),s=o(null),a=o(localStorage.getItem("token")||""),d=g(()=>s.value?`${s.value.first_name_th||""} ${s.value.last_name_th||""}`.trim():"");f(async()=>{var i;const l=localStorage.getItem("user");if(l&&(e.value=JSON.parse(l)),!a.value||((i=e.value)==null?void 0:i.role)!=="teacher"){window.location.href="/login";return}await v()});function n(){const l=window.location.pathname.split("/").filter(Boolean)[1];return["dashboard","enrollments","subjects"].includes(l)?l:"dashboard"}function h(l){t.value=l;const i=l==="dashboard"?"/teacher/dashboard":`/teacher/${l}`;window.history.pushState({},"",i)}async function v(){var u;const l=await fetch("/api/auth/me",{headers:{Authorization:`Bearer ${a.value}`,Accept:"application/json"}});if(!l.ok){localStorage.clear(),window.location.href="/login";return}const i=await l.json();e.value=i.user,s.value=((u=i.user)==null?void 0:u.teacher)||null,localStorage.setItem("user",JSON.stringify({id:i.user.id,username:i.user.username,email:i.user.email,role:i.user.role,teacher:s.value}))}function b(){fetch("/api/auth/logout",{method:"POST",headers:{Authorization:`Bearer ${a.value}`,Accept:"application/json"}}),localStorage.clear(),window.location.href="/login"}return window.addEventListener("popstate",()=>{t.value=n()}),{page:t,user:e,teacher:s,teacherName:d,token:a.value,navigate:h,logout:b}}});r.component("teacher-dashboard",w);r.component("teacher-enrollments",j);r.component("teacher-subjects",x);r.mount("#teacher-app");
