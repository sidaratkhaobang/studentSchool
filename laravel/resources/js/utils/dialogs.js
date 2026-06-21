import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

window.Swal = Swal;

export async function confirmDialog({
    title = 'ยืนยันการทำรายการ',
    text = '',
    icon = 'question',
    confirmButtonText = 'ยืนยัน',
    cancelButtonText = 'ยกเลิก',
    confirmButtonColor = '#0d6efd',
} = {}) {
    const result = await Swal.fire({
        title,
        text,
        icon,
        showCancelButton: true,
        confirmButtonText,
        cancelButtonText,
        confirmButtonColor,
        reverseButtons: true,
    });

    return result.isConfirmed;
}

export async function errorDialog(message, title = 'ไม่สามารถทำรายการได้') {
    await Swal.fire({
        title,
        text: message,
        icon: 'error',
        confirmButtonText: 'ตกลง',
    });
}

export async function warningDialog(message, title = 'แจ้งเตือน') {
    await Swal.fire({
        title,
        text: message,
        icon: 'warning',
        confirmButtonText: 'ตกลง',
    });
}

export function successToast(message, title = 'สำเร็จ') {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title,
        text: message,
        showConfirmButton: false,
        timer: 2200,
        timerProgressBar: true,
    });
}
