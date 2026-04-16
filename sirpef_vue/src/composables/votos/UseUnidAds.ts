import Http from "@/utils/Http";
import { ref} from "vue";
import {alerta} from "@/utils/alert"


export default () => {
    const unids = ref([])
    const active = ref(false)

    const GetUnidades = async () => {
        const response = await Http.get("/api/registro/unidades-adscritas")
        unids.value = response.data.unidad_adscrita
    }

    const NewUnidad = async (e) => {
        try {
            const form = new FormData(e.target)
            const response = await Http.post("/api/registro/unidad-adscritas", form)
            active.value = false
            GetUnidades()
            alerta('Enviado', response.data.message, 'success')
        } catch (error) {
          alerta('error', "No se pudo registrar", 'error')
        }
    }

    const DeleteUnidad = async (id) => {
       try {
        const response = await Http.delete(`/api/registro/unidad-adscritas/${id}`)
        GetUnidades()
        alerta('Enviado', response.data.message, 'success')
       } catch (error) {
        alerta('error', "No se pudo registrar", 'error')
       }
    }

    return {
        GetUnidades,
        unids,
        NewUnidad,
        active,
        DeleteUnidad
    }
}