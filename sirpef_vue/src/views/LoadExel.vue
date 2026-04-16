<script setup lang="ts">
import Http from '@/utils/Http';
import { alerta } from '@/utils/alert';
import { ref, onMounted } from "vue"
import Select from '@/components/Votos/Select.vue';
import Welcome from '@/components/sirpef/welcome.vue';

const frase = ref("o arrasta el archivo")
const Loading = ref(false)
const Unidades_Ascritas = ref([]);
const selectedUnid = ref("")
const selectedUnidID = ref("")

const SendFile = (e: FormDataEvent) => {
    const form = new FormData(e.currentTarget as HTMLFormElement)
    const { name }: any = form.get("fileExcel")

    const ext = name.split(".")[name.split(".").length - 1]

    if (ext !== "csv") return alerta("Error", "Solo son permitidos los archivos .CSV", "info")

    Loading.value = true
    senddata(form)
}


const senddata = async (form: FormData) => {
    try {
        const findedUnidad = Unidades_Ascritas.value.find(e => e.id == selectedUnidID.value)
        if(!selectedUnidID.value || !findedUnidad) return alerta("Error", "Debe seleccionar una unidad", "info")

        form.set("unid_id", selectedUnidID.value)
        const response = await Http.post("/api/registro/load-excel-final", form)
        frase.value = "o arrasta el archivo"
        if (response.status == 200) return alerta("Correcto", "Se ha importado correctamente el archivo", "success")
    } catch (error) {
        console.log(error)
        return alerta("Error", "Ocurrio un error en la carga", "error")
    } finally {
        Loading.value = false
    }
}

const changueName = (e: any) => {
    const archivo = e.target.files[0]
    frase.value = archivo.name
}

const GetUnidAsc = async () => {
    const response = await Http.get("/api/registro/unidades-adscritas");
    Unidades_Ascritas.value = response.data.unidad_adscrita
    selectedUnid.value = 'Sin selección'
}


const setData = (id) => {
    selectedUnidID.value = id
    selectedUnid.value = Unidades_Ascritas.value.find(element => element.id == id).nombre

}


onMounted(() => {
    GetUnidAsc()
})

</script>


<template>

    <Welcome title="Carga masiva" subtitle="Cargue un archivo excel con la información del personal">
        <form class="p-10 border rounded-2xl shadow col-span-2 bg-white" @submit.prevent="SendFile">
            <div class="p-5 relative border-4 border-dotted border-gray-300 rounded-lg bg-white my-3">
                <svg class="text-[#010c41] w-24 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                <div class="input_field flex flex-col w-max mx-auto text-center">
                    <label>
                        <input class="text-sm cursor-pointer w-36 hidden" name="fileExcel" type="file" accept=".csv"
                            @change="changueName" />
                        <div
                            class="text bg-[#ECA008] text-white border border-gray-300 rounded font-semibold cursor-pointer p-1 px-3">
                            Seleccionar</div>
                    </label>
                    <div class="title text-[#010c41] uppercase my-2">{{ frase }}</div>
                </div>

            </div>


            <div class="grid gap-10 my-8">
                <h1 class="text-xl mt-5 text-center">Seleccione la oficina que pertenece esta carga</h1>
                <Select :width="'w-full'" :Unidades_Ascritas="Unidades_Ascritas" :SelectValue="setData" :ActiveUnid="selectedUnid"></Select>
            </div>

            <div class="grid place-items-center" v-if="Loading">
                <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                    viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                        fill="currentColor" />
                    <path
                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                        fill="currentFill" />
                </svg>
            </div>
            <button type="submit" class="py-2 mx-auto block bg-[#010c41] w-2/4 hover:bg-[#ECA008] text-white rounded-lg"
                v-else>Enviar</button>
        </form>
    </Welcome>
</template>
