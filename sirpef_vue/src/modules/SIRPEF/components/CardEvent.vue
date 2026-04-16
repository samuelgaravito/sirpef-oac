<script lang="ts" setup>
import { useAuthStore } from '@/modules/Auth/stores';
import { ref } from 'vue';

const store = useAuthStore()

const { ChangueEvent, evento } = defineProps<{
    evento: any,
    ChangueEvent: Function,
    loadDes: boolean,
    evento_act?: string
}>()

const check = ref(evento.estatus)


const ChangueStatus = async () => {
    const { status } = await ChangueEvent(evento)
    if (!status) check.value = !check.value
}


</script>

<template>
    <article
        class="text-white bg-[#0a254b] p-6 shadow-sm rounded-2xl border grid transform hover:scale-[1.02] transition duration-200 ease">

        <div class="flex justify-between gap-[20px] items-start">
            <div class="status_hover text-white font-bold p-1 px-2 rounded-full h-[30px] w-auto text-center"
                :class="evento.estatus == null ? 'bg-orange-400' : evento.estatus == true ? 'bg-green-600' : 'bg-red-500'">
                <p class="capitalize">{{ evento.estatus == null ? 'pendiente' : evento.estatus == true ?
                    'Activado' : 'pendiente' }}</p>
            </div>


            <div class="flex justify-end gap-[20px] items-start" v-if="store.authUser.isAdmin">
                <button @click="$emit('resetEvento', evento.id)" title="Reestablecer el evento"
                    class="transform hover:scale-[1.3] transition duration-200 ease-in-out">
                    <font-awesome-icon icon="arrows-spin" />
                </button>
                <button title="Cargar personal" @click="$emit('loadPersonal', evento.id)"
                    class="transform hover:scale-[1.3] transition duration-200 ease-in-out">
                    <font-awesome-icon icon="fa-solid fa-user-plus" />
                </button>
                <!--button title="ver" class="transform hover:scale-[1.3] transition duration-200 ease-in-out"><font-awesome-icon icon="fa-solid fa-eye"/></-button-->
                <button @click="$emit('SetIdToEdit', evento.id)" title="editar"
                    class="transform hover:scale-[1.3] transition duration-200 ease-in-out"><font-awesome-icon
                        icon="fa-solid fa-pen-to-square" /></button>
                <!--button title="borrar" class="transform hover:scale-[1.3] transition duration-200 ease-in-out"><font-awesome-icon icon="fa-solid fa-trash" /></button-->
            </div>
        </div>

        <hr class="w-[100%] mx-auto my-4">

        <div class="row-span-2">

            <div class="grid grid-cols-2">
                <div>
                    <label class="text-[14px]">Inicio</label>
                    <p class="font-bold">{{ evento.fecha_inicio }}</p>
                </div>
                <div class="justify-self-end">
                    <label class="text-[14px]">Final</label>
                    <p class="font-bold">{{ evento.fecha_fin }}</p>
                </div>
            </div>


            <h3 class="capitalize mt-2 text-xl">{{ evento.titulo }}</h3>

            <p v-if="loadDes" class="my-5">{{ evento.descripcion }}</p>
            <div class="flex" v-if="store.authUser.isAdmin">
                <label class="inline-flex items-center mt-5" @click="ChangueStatus">
                    <input type="checkbox" v-model="check" class="sr-only peer">
                    <div
                        class="relative pointer-events-none w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                    </div>
                </label>


                <button @click="$emit('enterEvent', evento)" :title="evento_act == evento.id ? 'Entrar' : 'Salir'"
                    class="transform hover:scale-[1.1] transition duration-200 ease-in-out rounded-xl p-2 mt-4 mx-auto mr-0"
                    :class="evento_act == evento.id ? 'bg-red-500' : 'bg-green-500'">
                    <p v-if="evento_act == evento.id"> Salir <font-awesome-icon icon="fa-solid fa-right-to-bracket" />
                    </p>
                    <p v-else> Entrar <font-awesome-icon icon="fa-solid fa-right-to-bracket" /></p>
                </button>

            </div>

        </div>
    </article>
</template>

<style scoped>
/*
.status_hover:hover {
    width: auto;
    height: 30px;
}

.status_hover:hover p {
    opacity: 1;
    transition: .4s ease all;
    width: auto;
}*/
</style>