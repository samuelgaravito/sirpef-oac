<script lang="ts" setup>
import { ref } from 'vue';

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
                    El funcionario {{solicitud.persona_nombre}} desea autorizar a {{ solicitud.autorizada_nombre }} en el evento
                    <strong>{{ solicitud.evento_titulo }}</strong>
                </p>
            </div>
            <div class="hoverbtn items-center text-base font-semibold text-gray-900">
               <div class="inline-flex gap-2" v-if="!solicitud.estatus">
                <button class="bg-green-600 hover:bg-green-400 text-white px-2 py-1 rounded-2xl" title="aceptar" @click="() => $emit('setStatus', {id: solicitud.registro_evento, status: 'activo'})">
                    <font-awesome-icon icon="fa-solid fa-check" />
                </button>
                <button class="bg-red-500 hover:bg-red-400 text-white px-2 py-1 rounded-2xl" title="negar" @click="() => $emit('setStatus', {id: solicitud.registro_evento, status: 'rechazado'})">
                    <font-awesome-icon icon="fa-solid fa-ban" />
                </button>
               </div>

               <button v-else :class="solicitud.estatus == 'activo' ? 'bg-green-600 hover:bg-green-300' : 'bg-red-500 hover:bg-red-300'" class=" text-white px-2 py-1 rounded-2xl flex items-center gap-2" title="Cambiar estatus" @click="() => $emit('setStatus', {id: solicitud.registro_evento, status: solicitud.estatus == 'rechazado' ? 'activo' : 'rechazado'})">
                    <font-awesome-icon icon="fa-solid fa-rotate" />
                    <p class="capitalize">{{ solicitud.estatus }}</p>
                </button>
            </div>
        </div>

        <div class="text-gray-500 text-xl my-5 px-3 overflow-auto" v-if="seeImages">
            <p>Requerimientos:</p>
            <div class="flex mt-2">
                <img class="max-w-[280px] h-[200px] object-cover cursor-pointer" 
                :src="`${api_url}/storage/${solicitud.imagen1}`" 
                @click="$emit('setImg', {img: `${api_url}/storage/${solicitud.imagen1}`, id: 'cedu'})" 
                alt="solicitud"
                loading="lazy"
                >
                <img class="max-w-[280px] h-[200px] object-cover cursor-pointer" 
                :src="`${api_url}/storage/${solicitud.imagen2}`" 
                @click="$emit('setImg', {img: `${api_url}/storage/${solicitud.imagen2}`, id: 'cedu'})" 
                alt="solicitud"
                loading="lazy"
                >
                <img class="max-w-[280px] h-[200px] object-cover cursor-pointer" 
                :src="`${api_url}/storage/${solicitud.imagen3}`" 
                @click="$emit('setImg', {img: `${api_url}/storage/${solicitud.imagen3}`, id: 'cart'})" 
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