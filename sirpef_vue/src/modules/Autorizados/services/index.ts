import Http from "@/utils/Http"

export const getAutorizados = async () => {
    const response  = await Http.get(`/api/registro/autorizados`)
    return response.data
}

export const getAutorizado = async (cedula: string) => {
    const response  = await Http.get(`/api/registro/persona-autorizada/${cedula}`)
    return response.data
}

export const getSolicitudes = async () => {
    const response  = await Http.get(`/api/registro/solicitudes/autorizado`)
    return response.data
}


export const setSolicitud = async (id: string, payload: any) => {
    const response  = await Http.put(`/api/registro/evento-persona-solicitud/estatus/${id}`, payload)
    return response.data
}

export const sendAutorizado = async (payload: any) => {
    const response  = await Http.post(`/api/registro/evento-persona/asignar/`, payload)
    return response.data
}