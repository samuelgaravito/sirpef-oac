import { getDay } from "@/utils/GetDay";
import { ref } from "vue";
import UseEvents from "./UseEvents";


export default () => {

    const {
        SendDataEvent,
        GetOnlyEvent,
        id_event,
        editEvent
    } = UseEvents()

    const EventToSend = ref({
        titulo: "",
        descripcion: "",
        fecha_fin: getDay(),
        fecha_inicio: getDay(),
        oficinas_ids: [],
        cortesia: 0
    })

    const step = ref(1 as any);
    const estado = ref([] as number[])
    const mode = ref('POST')

    const emitForm = async (e: FormDataEvent, ministerios?: any[]) => {
        const form = new FormData(e.currentTarget as HTMLFormElement)
        const values : any = Object.fromEntries(form.entries());
        
        if (form.has('titulo')) {
            EventToSend.value = {
                ...values,
                oficinas_ids: EventToSend.value.oficinas_ids || [],
            }
            step.value = 2
            estado.value.push(1)

        } else if (form.has('oficinas')) {
            EventToSend.value.oficinas_ids = ministerios
            console.log(EventToSend.value)
            const value = mode.value == 'POST' ? await SendDataEvent(EventToSend.value) : await editEvent(EventToSend.value)
    
            if(!value.status) return 

            estado.value.push(2)
            step.value = 3
            id_event.value = value.id


            setTimeout( () => location.reload(), 1000)
        }
    }

    const editFormLoad = async (id: number) => {
        const event = await GetOnlyEvent(id)
        if (!event) return 

        id_event.value = id
        mode.value = 'PUT'
        EventToSend.value = {
                ...event.evento,
                oficinas_ids: event.ministerios,
        }

        window.scrollTo({
            top: 0,
            left: 0,
            behavior: 'smooth'
          });
          
    } 

    const resetData = async () => {
        EventToSend.value = {
            titulo: "",
            descripcion: "",
            fecha_fin: getDay(),
            fecha_inicio: getDay(),
            oficinas_ids: [],
            cortesia: 0
        }

        mode.value = 'POST'
        step.value = 1
        estado.value = []
    }

    return {
        emitForm,
        editFormLoad,
        step,
        EventToSend,
        estado
    }
}