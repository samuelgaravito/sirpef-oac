import { ref } from "vue"
import useCreateOrEdit from "./useCreateOrEdit";

export default () => {

    const {
        submit,
        GetOnlyUser
      } = useCreateOrEdit('0')

    

    const step = ref(1 as any);
    const estado = ref([] as number[])
    const UserInfo = ref({
         name: "", 
         email: "", 
         cedula: "",
         password: "", 
         role_id: "0", 
         oficinas_ids: [], 
         menus_id: [],
         eventos_id: []
    })

    const id_user = ref('0')

    const mode = ref('POST')

    const emitForm = async (e: FormDataEvent, items?: any[]) => {
        const form = new FormData(e.currentTarget as HTMLFormElement)
        const values : any = Object.fromEntries(form.entries());
        
        if (form.has('name')) {
            UserInfo.value = {
                ...values,
                oficinas_ids: UserInfo.value.oficinas_ids || [],
                menus_id: UserInfo.value.menus_id || [],
                eventos_id: UserInfo.value.eventos_id || [],
            }
            step.value = 2
            estado.value.push(1)

        } else if (form.has('oficinas')) {
            UserInfo.value.oficinas_ids = items
            estado.value.push(2)
            step.value = 3
        }
        else if (form.has('menus')) {
            UserInfo.value.menus_id = items
            estado.value.push(3)
            step.value = 4

        } else if(form.has('eventos')) {
            UserInfo.value.eventos_id = items
            estado.value.push(4)
            step.value = 5

            const value = id_user.value == '0' ? await submit(UserInfo.value) : await submit(UserInfo.value, id_user.value)

            if(value.status == false) return
            if(mode.value == "PUT") return

            setTimeout(resetData, 5000)

        }
    }



    const editFormLoad = async (id: string) => {
        const user = await GetOnlyUser(id)

        if (!user) return 

        id_user.value = id
        mode.value = 'PUT'

        UserInfo.value = {
            ...user.data.data,
        }


        window.scrollTo({
            top: 0,
            left: 0,
            behavior: 'smooth'
          });
          
    } 



const resetData = async () => {
    UserInfo.value = {
        name: "", 
        email: "", 
        cedula: "",
        password: "", 
        role_id: "", 
        oficinas_ids: [], 
        menus_id: [],
        eventos_id: []
    }

    mode.value = 'POST'
    step.value = 1
    estado.value = []
    
}

    return {
        step,
        estado,
        emitForm,
        editFormLoad,
        UserInfo
    }
}
