import type { UserData } from "@/types/votos/voteOne"
import { ref } from "vue"
import Http from "@/utils/Http";
import { alerta } from "../../../utils/alert"
import { getCorte, sendCorte } from "../services/cortesia";
import { alertQuestion } from "@/utils/alertQuestion";
import { obtenerHoraActual } from "@/utils/GetHora"

export default () => {

    const popup = ref<HTMLElement>(null)
    const result = ref({} as UserData)
    const popupStatus = ref(0)
    const Loading = ref(false)
    const cortesia = ref(false)
    const cortesias = ref({} as any)

    const GetUser = async (cedula: string) => {
        result.value = {} as UserData
        try {
            const res = await Http.get(`/api/registro/search/${cedula}`);
            result.value = res.data
            return true
        } catch (error) {
            if (error.response.status != 200) {
                alerta("error", error.response.data.msg, "info")
            }
            return false
        }
    }

    const getCortesias = async () => {
        try {
            const res = await getCorte()
            cortesias.value = res.data
        } catch (error) {
            if (error.response.status != 200) {
                alerta("error", error.response.data.msg || error.response.data.mensaje, "info")
            }
        }
    }

    const hidden = (e?: PointerEvent, value?: number) => {
        const target = e.target as HTMLElement
        if (e && target.tagName === "SECTION") {
            popup.value.style.display = "none"
        } else {
            if (target.tagName === "BUTTON") {
                const button = e.target as HTMLButtonElement
                if (button.name === "si") popupStatus.value = value
            }
            popup.value.style.display = "grid"
        }
    }

    const sendCortesia = async (e: FormDataEvent) => {
        const form = new FormData(e.currentTarget as HTMLFormElement)

        try {
            const cortesiaForm = parseInt(form.get("cortesia") as string)
            const response = await sendCorte({
                cortesia_entregada: cortesiaForm
            })
            alerta('Enviado', response.data.mensaje, 'success')
            cortesia.value = false
        } catch (error) {
            const { response } = error
            alerta("error", response.data.mensaje, "info")
        }
        finally {
            result.value = {} as UserData
            popup.value.style.display = "none"
            Loading.value = false
        }
    }


    const loadCortesia = async () => {
        await getCortesias()
        if (Object.keys(cortesias.value).length == 0) return
        if (cortesias.value.cortesias_faltantes == 0) return alerta("error", "No hay más cortesias disponibles", "info")
        cortesia.value = true
    }


    const envioData = async (e: FormDataEvent) => {
        const choice = await alertQuestion('Info', '¿Estas seguro?', 'question')
        if (!choice) return
        Loading.value = true

        const form = new FormData(e.target as HTMLFormElement)

        const data = {
            vote: form.get("motivo") ? false : true,
            descripcion: form.get("motivo"),
            hora_voto: obtenerHoraActual(),
            persona_id: result.value[0].id
        }

        try {
            const response = await Http.post("/api/registro/vote", data);
            alerta('Enviado', response.data.msg, 'success')
        } catch (error) {
            const { response } = error
            alerta("error", response.data.msg, "info")
        }
        finally {
            result.value = {} as UserData
            popup.value.style.display = "none"
            Loading.value = false
        }
    }



    return {
        result,
        popup,
        popupStatus,
        Loading,
        cortesia,
        cortesias,
        GetUser,
        hidden,
        alerta,
        envioData,
        sendCortesia,
        getCortesias,
        loadCortesia
    }
}