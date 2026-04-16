import Swal, { type SweetAlertIcon } from "sweetalert2";

export const alertQuestion = async (title: string, html: string, icon: SweetAlertIcon) => {
    const result = await Swal.fire({
        title,
        html,
        icon,
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
    });

    return result.isConfirmed
}