<script lang="ts" setup>
import { ref } from 'vue';
import { generatePDF } from '../utils/generatePDF';

const props = defineProps<{
    solicitud: any,
    id: any
}>()

const seeImages = ref(false)
const api_url = import.meta.env.VITE_APP_API_URL

</script>

<template>
    <div>
        <div class="flex items-center w-full space-x-4 rtl:space-x-reverse cursor-pointer hover:bg-slate-100 py-3 sm:py-4 px-5" >
            <div class="flex-shrink-0" @click="seeImages = !seeImages">
                {{ id + 1 }}
            </div>
            <div class="flex-1 min-w-0 cursor-pointer" @click="seeImages = !seeImages">
                <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                   Solicitud de <strong>{{solicitud.ministerio_nombre}}</strong>:
                </p>
                <p class="text-sm text-gray-500 mt-2">
                    El Jubilado {{solicitud.persona_nombre}} desea dar fe de vida {{ solicitud.autorizada_nombre }} en el evento
                    <strong>{{ solicitud.evento_titulo }}</strong>
                </p>
            </div>
            <div class="hoverbtn items-center text-base font-semibold text-gray-900">
               <!--div class="inline-flex gap-2" v-if="!solicitud.voto">
                <button class="bg-orange-400 hover:bg-orange-600 text-white px-2 py-1 rounded-2xl flex items-center gap-2" title="pendiente" @click="() =>  $emit('AcceptJubi', {id: solicitud.persona_id})">
                    <p class="capitalize">pendiente</p>
                    <font-awesome-icon icon="clock" />
                </button>
                <button class="bg-red-500 hover:bg-red-400 text-white px-2 py-1 rounded-2xl" title="negar" @click="() => $emit('AcceptJubi', {id: solicitud.id})">
                    <font-awesome-icon icon="fa-solid fa-ban" />
                </button>
               </div-->

                <div class="inline-flex gap-2" v-if="solicitud.voto == null">
                <button class="bg-green-600 hover:bg-green-400 text-white px-2 py-1 rounded-2xl" title="aceptar" @click="() => $emit('AcceptJubi', {id: solicitud.persona_id, status: true})">
                    <font-awesome-icon icon="fa-solid fa-check" />
                </button>
                <button class="bg-red-500 hover:bg-red-400 text-white px-2 py-1 rounded-2xl" title="negar" @click="() => $emit('AcceptJubi', {id: solicitud.persona_id, status: false})">
                    <font-awesome-icon icon="fa-solid fa-ban" />
                </button>
               </div>
               
               <div class="flex gap-2" v-else >

               <button :class="solicitud.voto == true ? 'bg-green-600' : 'bg-red-500'" 
               @click="() => $emit('AcceptJubi', {id: solicitud.persona_id, status: !solicitud.voto})"
               class=" text-white px-2 py-1 rounded-2xl flex items-center gap-2" :title="solicitud.voto == true ? 'Aprobado' : 'Rechazado'">
                   <p class="capitalize">{{ solicitud.voto == true ? 'Aprobado' : 'Rechazado' }}</p>
                   <font-awesome-icon icon="check" />
                </button>

                <button
                @click="() => generatePDF(solicitud.persona_cedula)"
                class="bg-red-500 text-white px-2 py-1 rounded-xl" title="Descargar planilla">
                   <font-awesome-icon icon="fa-solid fa-file-pdf" />
                </button>


               </div>

            </div>
        </div>

        <div class="text-gray-500 text-xl my-5 px-3 overflow-auto" v-if="seeImages">
            <p>Requerimientos:</p>
            <div class="flex mt-2">
                <img class="max-w-[280px] h-[200px] object-cover cursor-pointer" 
                :src="`${api_url}/storage/${solicitud.imagen1}`" 
                @click="$emit('setImg', {img: `${api_url}/storage/${solicitud.imagen1}`})" 
                alt="solicitud"
                loading="lazy"
                >
                <img class="max-w-[280px] h-[200px] object-cover cursor-pointer" 
                :src="`${api_url}/storage/${solicitud.imagen2}`" 
                @click="$emit('setImg', {img: `${api_url}/storage/${solicitud.imagen2}`})" 
                alt="solicitud"
                loading="lazy"
                >
                <img class="max-w-[280px] h-[200px] object-cover cursor-pointer" 
                :src="`${api_url}/storage/${solicitud.imagen3}`" 
                @click="$emit('setImg', {img: `${api_url}/storage/${solicitud.imagen3}`})" 
                alt="solicitud"
                loading="lazy"
                >
            </div>
        </div>
    </div>
</template>

<style scoped>
.hoverbtn button:hover{
    transition: .3s ease;
    transform: scale(1.2);
}
</style>