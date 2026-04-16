import { alerta } from "@/utils/alert";
import { ref } from "vue"
import { postPersona } from "../services";

export default () => {

    const step = ref(1 as any);
    const estado = ref([] as number[])
    const UserInfo = ref({
        cedula: "", 
        ente: "",
        nombre_completo: "",
        telefono: "",
        unidad_Adscrita: "",
        tipo_empleado_id: '',
        direccion: '',
        centro: '0',
        parroquia: '0',
        unidad_adscrita: '0',
        municipio: '0',
        estado: '0',
        sexo: '0',
        fecha_nacimiento: '0'
    })

    const id_user = ref('0')

    const mode = ref('POST')

    const emitForm = async (e: FormDataEvent) => {        
        if (step.value == 1) {
            step.value = 2
            estado.value.push(1)

        } else if (step.value == 2) {
            estado.value.push(2)
            step.value = 3
            const response = await postPersona(UserInfo.value)
            if (response.status == 200) alerta("Registrado", `Se ha registrado correctamente a ${UserInfo.value.nombre_completo}`, "success")
                else alerta("error", `Ha ocurrido un error en el registro`, "error")
            

            if(mode.value == "PUT") return

            setTimeout(resetData, 5000)
        }
    }



const resetData = async () => {
    UserInfo.value = {
        cedula: "", 
        ente: "",
        nombre_completo: "",
        telefono: "",
        unidad_Adscrita: "",
        direccion: '',
        centro: '0',
        parroquia: '0',
        unidad_adscrita: '0',
        municipio: '0',
        estado: '0',
        tipo_empleado_id: '',
        sexo: '0',
        fecha_nacimiento: '0'
    }

    mode.value = 'POST'
    step.value = 1
    estado.value = []
    
}

    return {
        step,
        estado,
        emitForm,
        UserInfo,
    }
}
