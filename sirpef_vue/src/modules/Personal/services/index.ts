import Http from "@/utils/Http"

export const postPersona = async (payload: any) => {
    const response = await Http.post(`/api/registro/create`, payload)
    return response
}