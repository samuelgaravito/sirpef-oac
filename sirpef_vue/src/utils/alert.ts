import Swal, { type SweetAlertIcon } from "sweetalert2";


export const alerta = (title: string, html: string, icon: SweetAlertIcon) => {
    Swal.fire({
        title,
        html,
        icon,
    });
}