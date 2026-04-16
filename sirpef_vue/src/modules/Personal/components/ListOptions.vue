<script setup lang="ts">
import { ref } from 'vue';

const activeMenu = ref(false)

const props = defineProps<{
    row: any,
    confirmacion: (id: string, text:string, action: string) => void,
    id_evento: string,
    seeFicha?: string
}>()


</script>

<template>

<div class="relative" @pointerleave="activeMenu = false" title="opciones">
    <button @click="activeMenu = !activeMenu" class="inline-flex items-center p-2 text-sm font-medium text-center text-gray-900 bg-white rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none dark:text-white focus:ring-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-600" type="button">
    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 4 15">
    <path d="M3.5 1.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 6.041a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 5.959a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/>
    </svg>
</button>
    
<div class="z-10 bg-white rounded-lg shadow w-44 absolute right-[28px] top-[-8px]" v-if="activeMenu">
    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200 z-[200]">
      <li v-if="Object.keys(row).length > 0 && row.participacion != null">
        <button
            @click="() => confirmacion(row.id, `La participación de esta persona ha sido marcada como:\n<strong>${row.participacion ? `'Si participó'` : `'No participó'`}</strong> desea cambiarla a <strong>${!row.participacion ? `'Si participó'` : `'No participó'`}</strong>`, 'M')"
            class="px-4 py-2 hover:bg-gray-100 flex gap-2 aling-center items-center">
            <font-awesome-icon icon="fa-solid fa-rotate" /> 
            <p>Cambiar participación</p>
        </button>
      </li>
      <li v-if="id_evento">
        <button
            class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white flex gap-2 aling-center items-center"
            @click="$emit('setPersona',row)">
            <font-awesome-icon icon="fa-solid fa-hand" />
            <p>Autorizar para evento</p>
        </button>
      </li>

      <li v-if="seeFicha" class="relative !z-[10000]">
        <button
            class="relative px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white flex gap-2 aling-center items-center"
            @click="$emit('seeFicha')">
            <font-awesome-icon icon="file-lines" />
            <p>Ver ficha</p>
        </button>
      </li>
    </ul>
</div>
</div>




        <!--button 
                        @click="() => confirmacion(row.id, 'No se podra recuperar', 'D')"
                        class="border-[#010c41] border-[1px] rounded-md border-solid fill-[#010c41] px-2 py-2 transition-all hover:text-[#ECA008] btnTable" title="ELiminar participación">
                            <font-awesome-icon icon="fa-solid fa-trash-can" />
                        </-button-->
</template>