import Http from "@/utils/Http";
import { ref } from "vue";
import { alerta } from "@/utils/alert"


export default () => {
    const types = ref([])
    const active = ref(false)
    const POST = ref(true)

    const data = ref([])

    const formData = ref({
        id: 0,
        tipo: '',
        tipo_caso_padre_id: '',
        color: ''
    })

    const GetTypes = async () => {
        const response = await Http.get("/api/oac/tipoCaso")
        data.value = response.data

        types.value = response.data.map(e => {
            let name = e.tipo
            if (e.tipo_caso_padre_id) {
                const padre = data.value.find(p => p.id == e.tipo_caso_padre_id)
                name = padre ? padre.tipo + ' - ' + e.tipo : e.tipo
            }

            return {
                ...e,
                tipo: name
            }
        })
    }

    const submit = async (e: any) => {
        if(POST.value) NewType(e)
            else updateType(e)
    }

    const NewType = async (e: any) => {
        try {
            const form = new FormData(e.target)
            const response = await Http.post("/api/oac/tipoCaso", formData.value)
            active.value = false
            alerta('Enviado', response.data.message, 'success')
            setTimeout(() => {
                location.reload()
            }, 1000);

        } catch (error) {
            alerta('error', "No se pudo registrar", 'error')
        }
    }

    const updateType = async (e: any) => {
        try {
            const response = await Http.put(`/api/oac/tipoCaso/${formData.value.id}`, formData.value)
            active.value = false
            alerta('Enviado', response.data.message, 'success')
            setTimeout(() => {
                location.reload()
            }, 1000);

        } catch (error) {
            alerta('error', "No se pudo registrar", 'error')
        }
    }


    const getType = async (id: any) => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
        const type = data.value.find(e => e.id == id)
        if (type) formData.value = type

        POST.value = false
    }

    const DeleteType = async (id: any) => {
        try {
            const response = await Http.delete(`/api/oac/tipoCaso/${id}`)
            GetTypes()
            alerta('Enviado', response.data.message, 'success')
        } catch (error) {
            alerta('error', "No se pudo registrar", 'error')
        }
    }

    return {
        active,
        types,
        formData,
        GetTypes,
        getType,
        NewType,
        updateType,
        submit,
        DeleteType
    }
}