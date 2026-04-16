import router from "@/router";
import { useEventsName } from "@/stores/nameEvent";
import { alerta } from "@/utils/alert";
import { alertQuestion } from "@/utils/alertQuestion";
import Http from "@/utils/Http";
import Swal from "sweetalert2";
import { ref } from "vue";

export default () => {

    const eventos = ref([] as any)
    const event_Active = ref({} as any)
    const id_event = ref(0)
    const id_event_edit = ref(0)
    const NameEvent = useEventsName()

    const diseabledEvent = () => {
        return false;
    }


    const GetEvents = async () => {
        const response = await Http.get(`/api/registro/evento/`)
        eventos.value = response.data
    }

    const GetOnlyEvent = async (id: number) => {
        try {
            const response = await Http.get(`/api/registro/evento/edit/${id}`)
            return response.data
        } catch (error) {
            return undefined
        }
    }

    const SendDataEvent = async (info: any) => {
        for (const key in info) {
            if (info.hasOwnProperty(key) && info[key] === '') {
                alerta("Atención", "Faltan datos por registrar", "info")
                return { status: false };
            }
        }

        try {
            const response = await Http.post("api/registro/evento/store", {
                ...info,
                ministerio_ids: info.oficinas_ids.map(e => e.id)
            })

            alerta("correcto", response.data.mensaje, "success")

            return { status: true, id: response.data.id }
        } catch (error) {
            alerta("Atención", "ocurrio un error", "info")
        }
    }


    const senddata = async (form: FormData) => {
        try {
            const response = await Http.post(`/api/registro/evento/carga-masiva/${id_event.value}`, form)
            let html = ''

            if (response.data.errores) {
                html = response.data.errores.map(e => `<br/>${e}`)
            } else {
                html = response.data.msg
            }

            alerta("Correcto",
                `
                <div class="text-left">
                    ${html}
                </div>
                `
                , "success")
        } catch (error) {
            console.log(error)
            alerta("Error", "Ocurrio un error en la carga", "error")
        }
    }

    const ChangueEvent = async (evento: any) => {

        const result = await Swal.fire({
            title: "¿Estas seguro?",
            html: `Se cambiara el estado del evento <p class="font-bold">${evento.titulo}</p>`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#041E42",
            cancelButtonColor: "#ECA008",
            confirmButtonText: "Confirmar",
            cancelButtonText: "Cancelar"
        });

        if (!result.isConfirmed) return { status: false }

        try {
            const response = await Http.put(`/api/registro/evento/${evento.id}/status`, {})
            alerta("Correcto", response.data.mensaje, "success")
            GetEvents()
            return { status: true }
        } catch (error) {
            alerta("Correcto", error.data.mensaje, "success")
            return { status: false }
        }
    }

    const enterEvent = async (evento: any) => {

        const result = await Swal.fire({
            title: "¿Estas seguro?",
            html: `Quieres entrar al evento: <p class="font-bold">${evento.titulo}</p>`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#041E42",
            cancelButtonColor: "#ECA008",
            confirmButtonText: "Confirmar",
            cancelButtonText: "Cancelar"
        });

        if (!result.isConfirmed) return { status: false }


        try {
            const response = await Http.put(`/api/registro/activarEvento/${evento.id}`, {})
            await NameEvent.GetUserInfo()

            alerta("Correcto", response.data.mensaje || 'logrado', "success")

            window.scrollTo({
                top: 880,
                left: 0,
                behavior: 'smooth'
            });

            router.push({ path: "/dashboard" })

        } catch (error) {
            alerta("info", error.data.mensaje, "info")
            return { status: false }
        }
    }


    const editEvent = async (info: any) => {

        try {
            const response = await Http.put(`/api/registro/evento/update/${id_event.value}`, {
                ...info,
                ministerio_ids: info.oficinas_ids.map(e => e.id)
            })

            alerta("Correcto", `${response.data.mensaje}`, "success")

            return { status: true, id: response.data.id }

        } catch (error) {
            console.log(error)
            alerta("Error", "Ocurrio un error en la carga", "error")
        }
    }


    const resetEvent = async (id: any) => {

        const result = await alertQuestion('Alerta', '¿Quieres limpiar el evento?', 'warning')

        if (!result) return

        try {
            const response = await Http.put(`/api/registro/evento/reset/${id}`, {})

            alerta("Correcto",
                `
                ${response.data.mensaje}
                `
                , "success")

        } catch (error) {
            console.log(error)
            alerta("Error", "Ocurrio un error en la carga", "error")
        }
    }


    const GetUserEvent = async () => {
        const response = await Http.get("/api/registro/user/info");
        event_Active.value = response.data[0]
    }

    return {
        diseabledEvent,
        SendDataEvent,
        senddata,
        GetEvents,
        ChangueEvent,
        GetOnlyEvent,
        editEvent,
        enterEvent,
        eventos,
        id_event,
        id_event_edit,
        event_Active,
        GetUserEvent,
        resetEvent
    }
}