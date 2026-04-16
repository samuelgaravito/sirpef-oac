import Http from "@/utils/Http";
import {alerta} from "@/utils/alert"
import {ref} from "vue";

export default () => {
    const Tipos = ref([]);
    const TiposInput = ref(null)

    const GetTipos = async () => {
        const response = await Http.get("/api/registro/tipos")
        Tipos.value = response.data.tipos 
    }


    const PostNewTipo = async (e) => {
        try {
            const form = new FormData(e.target)
            const response = await Http.post("/api/registro/tipos", form)
            TiposInput.value = ""
            GetTipos()
            alerta('Enviado', response.data.message, 'success')
        } catch (error) {
          alerta('error', "No se pudo registrar", 'error')
        }
    }

    const DeleteTipo = async (id: Number) => {
        try {
            const response = await Http.delete(`/api/registro/tipos/${id}`)
            GetTipos()
            alerta('Enviado', response.data.message, 'success')
        } catch (error) {
          alerta('error', "No se pudo registrar", 'error')
        }
    }

    return {
        Tipos,
        TiposInput,
        GetTipos,
        PostNewTipo,
        DeleteTipo
    }
}