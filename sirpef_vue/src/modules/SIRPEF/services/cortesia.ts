import Http from "@/utils/Http";


export const getCorte = async () => {
    const response = await Http.get("/api/registro/obtener-cortesias");
    return response
}

export const sendCorte = async (payload: any) => {
    const response = await Http.post("/api/registro/cortesia-entregada", payload);
    return response
}