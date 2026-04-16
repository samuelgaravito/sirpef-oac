import Http from '@/utils/Http';
import { ref, onMounted } from "vue"
import type {estado} from "@/types/votos/estado"
import { alerta } from '@/utils/alert';


export default () => {

const Estados = ref([] as estado[])
const municipios = ref([])
const Parroquia = ref([])
const Centros = ref([])
const Unidades = ref([])

const Info_user = ref({
    cedula: "", 
    ente: "",
    nombre_completo: "",
    telefono: "",
    unidad_Adscrita: "",
    direccion: '',
    }, 
)

const info_geo = ref({
        estado: "",
        municipio: "",
        parroquia: '',
        centro: '',
    })

const info_geo_ids = ref({
        estado_id: "0",
        municipio_id: "0",
        parroquia_id: '0',
        centro_id: '0',
        unidad_id: '0'
    })


const getEstados = async () => {
    const response = await Http.get("/api/registro/getdireccion")
    Estados.value = response.data[0]
    Info_user.value.ente = response.data[1].entes
}

const getMunicipios = async (id: string, text: string) => {
    municipios.value = []
    Parroquia.value = []
    const response = await Http.get(`/api/registro/municipio/${id}`)
    municipios.value = response.data[0]
    info_geo_ids.value.estado_id = id
    info_geo.value.estado = text 
}

const getParroquias = async (id: string, text: string) => {
    const response = await Http.get(`/api/registro/parroquia/${id}`)
    Parroquia.value = response.data[0]
    info_geo_ids.value.municipio_id = id // es municipio
    info_geo.value.municipio = text 
}

const getCentros = async (id: string, text) => {
    const response = await Http.get(`/api/registro/centro/`)
    Centros.value = response.data[0]
    info_geo_ids.value.parroquia_id = id // es parroquia
    info_geo.value.parroquia = text 
}

const getUnidades = async () => {
    const response = await Http.get(`/api/registro/unidadAdscritas/`)
    Unidades.value = response.data

}

const SendData = async () => {
    const SendData = {
        ...Info_user.value,
        centro: info_geo_ids.value.centro_id ,
        parroquia: info_geo_ids.value.parroquia_id,
        unidad_adscrita: info_geo_ids.value.unidad_id
    }

    try {
        const response = await Http.post(`/api/registro/create`, SendData)
        if (response.status == 200) alerta("Registrado", `Se ha registrado correctamente a ${SendData.nombre_completo}`, "success")
    } catch (error) {
     alerta("error", `Ha ocurrido un error en el registro`, "error")
    }
}

const changueValue = (e: Event) => {
    const target = e.target as HTMLSelectElement
    const text = target.options[target.selectedIndex].text
    if(target.name == "estado") getMunicipios(target.value, text)
       else if(target.name == "municipio") getParroquias(target.value, text)
            else if(target.name == "parroquia") getCentros(target.value, text)
                else if(target.name == "centro") {
                    info_geo.value.centro = text // es texto
                    info_geo_ids.value.centro_id = target.value // es id
                } else {
                    Info_user.value.unidad_Adscrita = text
                    info_geo_ids.value = {
                        ...info_geo_ids.value,
                        [`${target.name}_id`]: target.value
                    }
                }
}

const saveValue = (e: InputEvent, step: number) => {
    const {value, name} = e.target as HTMLInputElement

    if(step == 1) {
        let newvalor = value
        if (name == "cedula" || name == "telefono") newvalor = parseInt(value).toString()
            
        Info_user.value = {
            ...Info_user.value,
            [name]:  newvalor === "NaN" ? '' : newvalor.toUpperCase()
        }
    } else {
        info_geo_ids.value = {
            ...info_geo_ids.value,
            [`${name}_id`]: value
        }

        info_geo.value = {
            ...info_geo.value,
            [name]: value
        }
    }
}

    onMounted(() => {
        getEstados()
        getUnidades()
    })

    return {
        Estados,
        municipios,
        Parroquia,
        Centros,
        SendData,
        changueValue,
        saveValue,
        Info_user,
        info_geo,
        info_geo_ids,
        Unidades
    }
}