import { ref } from "vue"
import { getSolicitudes, AcceptSolicitud } from "../services"
import { alerta } from "@/utils/alert"
import { alertQuestion } from "@/utils/alertQuestion"
import { obtenerHoraActual } from "@/utils/GetHora"

export default () => {
    const autorizados = ref([] as any)
    const solicitudes = ref([] as any)
    const filter = ref<any>(null)

    const GetSoli = async () => {
        try {
            const response = await getSolicitudes()
            solicitudes.value = response
        } catch (error) {
            console.log(error)
        }
    }

    const AcceptJubi = async (id: string, status: boolean) => {
        try {
            const choice = await alertQuestion('Info', `¿Estas seguro que deseas ${status ? 'aceptar' : 'rechazar'} esta solicitud?`, 'question')
            if(!choice) return


            const payload = {
                vote: status,
                descripcion: 'Formulario',
                hora_voto: obtenerHoraActual(),
                persona_id: id 
            }

            const response = await AcceptSolicitud(payload)
            alerta('completado', response.msg, 'success')
            GetSoli()
        } catch(error) {
            console.log(error)
        }
    }

    return {
        solicitudes,
        filter,
        GetSoli,
        AcceptJubi
    }
}