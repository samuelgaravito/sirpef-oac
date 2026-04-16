import { getAutorizado } from "@/modules/Autorizados/services";
import { ref } from "vue"

export default () => {

    const step = ref(1 as any);
    const estado = ref([] as number[])
    const UserInfo = ref({
         nombre_completo: "", 
         cedula: "",
         telefono: "",
         motivo: ""
    })

    const id_user = ref('0')

    const mode = ref('POST')

    const emitForm = async (e: FormDataEvent, items?: any[]) => {
        const form = new FormData(e.currentTarget as HTMLFormElement)
        const values : any = Object.fromEntries(form.entries());
        
        if (step.value == 1) {
            step.value = 2
            estado.value.push(1)

        } else if (step.value == 2) {
            estado.value.push(2)
            step.value = 3

            /* const value = id_user.value == '0' ? await submit(UserInfo.value) : await submit(UserInfo.value, id_user.value)

            if(value.status == false) return
            if(mode.value == "PUT") return

            setTimeout(resetData, 5000)*/

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
        motivo: ""
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
