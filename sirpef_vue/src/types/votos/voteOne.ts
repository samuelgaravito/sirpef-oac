export interface UserData {
    id?: string,
    nombre_completo: string,
    cedula: number | string,
    ente: string,
    unidad_Adscrita: string,
    telefono: string,
    direccion?: string,
}

export interface UserGeografia {
    estado: string,
    municipio: string,
    parroquia: number | string,
    centro: string,
}

export interface UserRegister {
    registro_existente: string,
}