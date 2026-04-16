import { ref } from "vue"
import { getAutorizado, sendAutorizado } from "../services";
import { alerta } from "@/utils/alert";
import { useEventsName } from "@/stores/nameEvent";

export default () => {

    const step = ref(1 as any);
    const estado = ref([] as number[])
    const UserInfo = ref({
         nombre_completo: "", 
         cedula: "",
         telefono: "",
         motivo: "",
         estatus: "interno",
         empleado_id: '0'
    })

    const id_user = ref('0')

    const evento = useEventsName()

    const mode = ref('POST')

    const emitForm = async (e: FormDataEvent, items?: any[]) => {
        const form = new FormData(e.currentTarget as HTMLFormElement)
        
        if (step.value == 1) {
            step.value = 2
            estado.value.push(1)

        } else if (step.value == 2) {
            estado.value.push(2)
            step.value = 3

            for (const key in UserInfo.value) {
                if (UserInfo.value.hasOwnProperty(key)) {
                    form.append(key, UserInfo.value[key]);
                }
            }

            form.append('evento_id', evento.id)

            const response = await sendAutorizado(form)
            alerta('completado', response.message, 'success')
        }
    }



    const getAuth = async () => {
        try {
            const response = await getAutorizado(UserInfo.value.cedula)
            if(Object.keys(response).length == 0) return
            UserInfo.value = {
                ...UserInfo.value,
                ...response
            }
        } catch (error) {
            console.log(error)
        }
    }






const resetData = async () => {
    UserInfo.value = {
        nombre_completo: "", 
        cedula: "",
        telefono: "",
        motivo: "",
        estatus: "interno",
        empleado_id: '0'
    }

    mode.value = 'POST'
    step.value = 1
    estado.value = []
}

    return {
        step,
        estado,
        UserInfo,
        emitForm,
        getAuth
    }
}
