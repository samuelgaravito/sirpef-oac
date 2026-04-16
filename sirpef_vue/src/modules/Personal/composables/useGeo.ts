import { ref } from "vue"
import { getCentrosService, getEstadosService, getMunicipiosService, getPaisesService, getParroquiasService, getUnidadesPublicaService, getUnidadesService } from "../services/geoService"

export default () => {
    const estados = ref([] as any)
    const paises = ref([] as any)
    const municipios = ref([] as any)
    const parroquias = ref([] as any)
    const centros = ref([] as any)
    const Unidades = ref([] as any)

    const getPaises = async () => {
        try {
            const response = await getPaisesService()
            paises.value = response
        } catch (error) {
            console.log(error)
        }
    }

    const getEstados = async () => {
        try {
            const response = await getEstadosService()
            estados.value = response[0]
        } catch (error) {
            console.log(error)
        }
    }

    const getMunicipios = async (id: string) => {
        try {
            const response = await getMunicipiosService(id)
            municipios.value = response[0]
        } catch (error) {
            console.log(error)
        }
    }

    const getParroquias = async (id: string) => {
        try {
            const response = await getParroquiasService(id)
            parroquias.value = response[0]
        } catch (error) {
            console.log(error)
        }
    }

    const getCentros = async () => {
        try {
            const response = await getCentrosService()
            centros.value = response[0]
        } catch (error) {
            console.log(error)
        }
    }

    const getUnidades = async () => {
        try {
            const response = await getUnidadesService()
            Unidades.value = response.unidad_adscrita
        } catch (error) {
            console.log(error)
        }
    }

    const getUnidadesPublica = async () => {
        try {
            const response = await getUnidadesPublicaService()
            Unidades.value = response
        } catch (error) {
            console.log(error)
        }
    }



    return {
        estados,
        municipios,
        parroquias,
        centros,
        Unidades,
        paises,
        getEstados,
        getMunicipios,
        getParroquias,
        getCentros,
        getUnidades,
        getPaises,
        getUnidadesPublica
    }
}
