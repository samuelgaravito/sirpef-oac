import { ref } from "vue"
import {alerta} from "../../../utils/alert"
import Http from "@/utils/Http/index"
import { alertQuestion } from "@/utils/alertQuestion"
import { obtenerHoraActual } from "@/utils/GetHora"

export default () => {
    const personas = ref([])
    const popup = ref()
    const popupStatus = ref(0)
    const Loading = ref(false)
    const frase = ref("No hay archivo seleccionado")
    const is_parti = ref(null)
    const next = ref(false)

    const GetUser = async (cedula: string) => {
        const resultado: any = personas.value.find(element => element.cedula == cedula)
        if (resultado) return alerta("Atención", "Esta cédula ya esta cargada", "info")
            try {
                const response = await Http.get(`/api/registro/autorizado/search/${cedula}`);
                personas.value = response.data
            } catch (error) {
                if (error.response.status != 200) {
                    alerta("Atención", error.response.data.msg, "info")
                    return false
                }
            }finally{
                return true
            }
    }

    const DeleteEmpleado = (value:string) => {
        personas.value = personas.value.filter(element => element.cedula != value)
    }

    const Envio_Datos = async () => {

        const choice = await alertQuestion('Info', '¿Estas seguro?', 'question')
        if(!choice) return
        Loading.value = true

        const data = {
            vote: true,
            descripcion: '',
            hora_voto: obtenerHoraActual(),
            persona_ids: personas.value.filter(element => element.id != null).map(element => element.id)
        }


        try {
            const response = await Http.post("/api/registro/register-multiple-votes-autorizado", data);
            alerta('Enviado', `
                ${response.data.msg}\n
                <br>
                <p>${response.data.info || ''}</p><br>
                `, 'success') 
        } catch (error) {
            const {response} = error
            alerta("error", response.data.errors.map(e => `${e}<br>`), "info")
        }
        finally{
            personas.value = []
            popup.value.style.display = "none"
            Loading.value = false
            is_parti.value = null
            frase.value = "No hay archivo seleccionado"
            next.value = false
        }
    }

    const hidden = (e?: PointerEvent, value?: number) => {
        const target = e.target as HTMLElement
        if (e && target.tagName === "SECTION") {
            popup.value.style.display = "none"
        } else {
            if(target.tagName === "BUTTON") {
                const button = e.target as HTMLButtonElement
                if(button.name === "si")  popupStatus.value = value   
            }         
            popup.value.style.display = "grid"
        }
    }

    const SendForCSV = async (form : FormData) => {
        if(is_parti.value === null) return alerta("info", "No se ha seleccionado su participación", "info")
        Loading.value = true
        try {
            const response = await Http.post("/api/registro/import-cedulas", form)
            personas.value = response.data.correcto
        } catch (error) {
            return alerta("info", "Ocurrio un error en la carga", "info")
        } finally {
            Loading.value = false
        }
    }

    const SetFile = (e: FormDataEvent) => {
        const form = new FormData(e.currentTarget as HTMLFormElement)
        const { name }: any = form.get("fileExcel")
    
        const ext = name.split(".")[name.split(".").length - 1]
    
        if (ext !== "csv") return alerta("info", "Solo son permitidos los archivos .CSV", "info")
    
        SendForCSV(form)
    }

    const changueFile = (e: FormDataEvent) => {
        const form = new FormData(e.currentTarget as HTMLFormElement)
        const { name }: any = form.get("fileExcel")
        frase.value = name
    }
    



    return {
        personas,
        GetUser,
        DeleteEmpleado,
        hidden,
        popup,
        Envio_Datos,
        popupStatus,
        Loading,
        frase,
        SetFile,
        changueFile,
        is_parti,
        next
    }
}