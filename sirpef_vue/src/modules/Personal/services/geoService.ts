import Http from "@/utils/Http";

export const getPaisesService = async () => {
    const response = await Http.get("/api/registro/getpaises")
    return response.data
}

export const getEstadosService = async () => {
    const response = await Http.get("/api/registro/getdireccion")
    return response.data
}

export const getMunicipiosService = async (id: string) => {
    const response = await Http.get(`/api/registro/municipio/${id}`)
    return response.data
}

export const getParroquiasService = async (id: string) => {
    const response = await Http.get(`/api/registro/parroquia/${id}`)
    return response.data
}

export const getCentrosService = async () => {
    const response = await Http.get(`/api/registro/centro/`)
    return response.data
}

export const getUnidadesService = async () => {
    const response = await Http.get(`/api/registro/unidades-adscritas`)
    return response.data
}

export const getUnidadesPublicaService = async () => {
    const response = await Http.get(`/api/unids/ministerio`)
    return response.data
}
