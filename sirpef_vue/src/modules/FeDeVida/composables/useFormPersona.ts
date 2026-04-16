import { alerta } from "@/utils/alert";
import { onMounted, ref } from "vue"
import { editCase, getCaseSingle, registerCase } from "../services";
import { useRoute, useRouter } from "vue-router";

export default () => {
    const router = useRouter()
    const route = useRoute()
    const id = route.params.id as string

    const step = ref(1 as any);
    const estado = ref([] as number[])
    const UserInfo = ref({
        cedula: "",
        nombre_completo: "",
        telefono: "",
        tipo_caso_id: '',
        descripcion: '',
        parroquia: '0',
        unidad_adscrita: '0',
        municipio: '0',
        estado: '0',
        sexo: '0',
        fecha_nacimiento: '0',
        recaudos: []
    })

    const mode = ref('POST')

    const emitForm = async (e: FormDataEvent) => {
        if (step.value == 1) {
            step.value = 2
            estado.value.push(1)

        } else if (step.value == 2) {
            estado.value.push(2)
            step.value = 3

        } else if (step.value == 3) {
            estado.value.push(3)
            step.value = 4


            const formData = new FormData();

            formData.append('cedula', UserInfo.value.cedula);
            formData.append('nombre_completo', UserInfo.value.nombre_completo);
            formData.append('telefono', UserInfo.value.telefono);
            formData.append('tipo_caso_id', UserInfo.value.tipo_caso_id);
            formData.append('descripcion', UserInfo.value.descripcion);
            formData.append('parroquia', UserInfo.value.parroquia);
            formData.append('unidad_adscrita', UserInfo.value.unidad_adscrita);
            formData.append('municipio', UserInfo.value.municipio);
            formData.append('estado', UserInfo.value.estado);
            formData.append('sexo', UserInfo.value.sexo);
            formData.append('fecha_nacimiento', UserInfo.value.fecha_nacimiento);

            UserInfo.value.recaudos.forEach((recaudo, index) => {
                formData.append(`recaudos[${index}][nombre]`, recaudo.nombre);
                if (recaudo.archivo) {
                    formData.append(`recaudos[${index}][archivo]`, recaudo.archivo);
                }
            });

            try {
                if (mode.value == 'POST') await registerCase(formData)
                else if (mode.value == 'PUT') await editCase(id, formData)

                alerta("Registrado", `Se ha registrado correctamente a ${UserInfo.value.nombre_completo}`, "success")
                router.push('/cases')
            } catch (error) {
                const { response } = error
                if (response.data) return alerta("error", `
                    ${response.data.message || 'Ocurrio un error'}
                    <br><p>${response.data.errors[Object.keys(response.data.errors)[0]] || 'Ocurrio un error'}</p>
                    `, "info")
                alerta("error", 'Ocurrio un error', "info")
            }
        }
    }

    const getInfo = async (id: string) => {
        try {
            const response = await getCaseSingle(id)
            const data = response.data

            const newRecaudos = data.recaudos.map(e => {
                return {
                    ...e,
                    type: e.mime_type
                }
            })

            UserInfo.value = {
                cedula: data.persona.cedula,
                nombre_completo: data.persona.nombre_completo,
                telefono: data.persona.telefono,
                tipo_caso_id: data.tipo_caso.id || '',
                descripcion: data.descripcion,
                parroquia: data.persona.parroquia.id || '',
                unidad_adscrita: data.persona.ministerio_id,
                municipio: data.persona.municipio.id || '',
                estado: data.persona.estado.id || '',
                sexo: data.persona.sexo,
                fecha_nacimiento: data.persona.fecha_nacimiento,
                recaudos: newRecaudos
            }

            mode.value = 'PUT'
        } catch (error) {
            console.log(error)
            alerta("error", `Ha ocurrido un error en el registro`, "error")
        }
    }



    onMounted(() => {
        if (id) {
            getInfo(id)
        }
    })

    return {
        step,
        estado,
        emitForm,
        UserInfo,
        getInfo,
    }
}
