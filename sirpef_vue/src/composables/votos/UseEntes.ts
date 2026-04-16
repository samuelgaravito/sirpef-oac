import Http from "@/utils/Http";
import {alerta} from "@/utils/alert"
import {ref} from "vue";

export default () => {
    const Entes = ref([]);
    const entesInput = ref(null)
    
    const GetEntes= async () => {
        const response = await Http.get("/api/registro/entes");
        Entes.value = response.data.entes
      } 
    
    const DeleteEnte = async (id: number) => {
      try {
        const response = await Http.delete(`/api/registro/entes/${id}`);
        alerta('Eliminado', response.data.message, 'success')
        GetEntes()
      } catch (error) {
          alerta('error', "No se pudo registrar", 'error')
      }
    }
    
    const NewEnte = async (e) => {
      try {
        const form = new FormData(e.target)
        const response = await Http.post("/api/registro/entes", form);
        GetEntes()
        entesInput.value = ""
        alerta('Enviado', response.data.message, 'success')
      } catch (error) {
          alerta('error', "No se pudo registrar", 'error')
      }
    }

    return {
        Entes,
        entesInput,
        NewEnte,
        DeleteEnte,
        GetEntes
    }
}