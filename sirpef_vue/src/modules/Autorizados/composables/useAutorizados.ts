import { ref } from "vue"
import { getSolicitudes, setSolicitud } from "../services"
import { alerta } from "@/utils/alert"
import { alertQuestion } from "@/utils/alertQuestion"

export default () => {
    const autorizados = ref([] as any)
    const solicitudes = ref([] as any)
    const filter = ref(null)

    const GetSoli = async () => {
        try {
            const response = await getSolicitudes()
            solicitudes.value = response
        } catch (error) {
            console.log(error)
        }
    }

    const setStatus = async (estatus: string, id: string) => {
        try {
            const result = await alertQuestion('Atención', '¿Estás seguro de este cambio?', 'info')
            if(!result) return
            const response = await setSolicitud(id, {estatus})
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
        setStatus
    }
}