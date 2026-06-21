import { createApp, nextTick, onMounted, ref, watch } from 'vue';
import { initSelect2 } from '../utils/select2';
import { errorDialog, successToast } from '../utils/dialogs';

createApp({
    setup() {
        const form = ref({
            first_name_th: '',
            last_name_th: '',
            first_name_en: '',
            last_name_en: '',
            date_of_birth: '',
            age: '',
            grade_level: '',
            advisor_teacher_id: '',
            phone: '',
            email: '',
            username: '',
            password: '',
            password_confirmation: '',
        });
        const teachers = ref([]);
        const errors = ref({});
        const loading = ref(false);
        const success = ref(false);

        onMounted(async () => {
            await fetchTeachers();
            await nextTick();
            initAdvisorSelect();
        });

        watch(teachers, async () => {
            await nextTick();
            initAdvisorSelect();
        });

        async function fetchTeachers() {
            try {
                const response = await fetch('/api/auth/teachers', {
                    headers: { Accept: 'application/json' },
                });
                if (response.ok) {
                    teachers.value = (await response.json()).data || [];
                }
            } catch {
                teachers.value = [];
            }
        }

        function initAdvisorSelect() {
            const select = document.getElementById('register-advisor-select');
            if (!select) return;

            initSelect2(select, {
                placeholder: '-- เลือกอาจารย์ที่ปรึกษา --',
                allowClear: true,
            });
            window.$(select)
                .off('change.register-advisor')
                .on('change.register-advisor', () => {
                    form.value.advisor_teacher_id = window.$(select).val() || '';
                });
        }

        async function register() {
            loading.value = true;
            errors.value = {};

            try {
                const response = await fetch('/api/auth/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(form.value),
                });
                const data = await response.json();

                if (!response.ok) {
                    errors.value = data.errors || { general: data.message };
                    await errorDialog(data.message || 'สมัครสมาชิกไม่สำเร็จ');
                    return;
                }

                success.value = true;
                successToast(data.message || 'ลงทะเบียนสำเร็จ');
            } catch {
                errors.value = { general: 'เกิดข้อผิดพลาด กรุณาลองใหม่' };
                await errorDialog('เกิดข้อผิดพลาด กรุณาลองใหม่');
            } finally {
                loading.value = false;
            }
        }

        return { form, teachers, errors, loading, success, register };
    },
}).mount('#register-app');
