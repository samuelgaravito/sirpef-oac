<script setup lang="ts">
import CardSolicitud from '../components/Card-solicitud.vue';
import useAutorizados from '../composables/useAutorizados';
import { onMounted, ref } from 'vue';

const seeImg = ref({
    img: '',
    id: ''
})
const {
    solicitudes,
    filter,
    GetSoli,
    setStatus
} = useAutorizados()

onMounted(() => {
    GetSoli()
})

const hidden = (e?: PointerEvent) => {
        const target = e.target as HTMLElement
        if (e && target.tagName === "SECTION") seeImg.value = {
               img: '',
               id: ''
        }
}

const setImg = (data: any) => {
    seeImg.value = data
}


</script>

<template>

<section @click="(e) => hidden(e as PointerEvent)" v-if="seeImg.img"
    class="fixed bg-black bg-opacity-30 w-full h-full grid top-0 left-0 cursor-pointer z-[200]" :class="seeImg.id == 'cedu' ? 'place-items-center' : ''">
    <img :src="seeImg.img" alt="solicitud" class="cursor-default" :class="seeImg.id == 'cedu' ? 'h-[50%]' : 'h-[40%] mx-auto'">
</section>

    <main class="w-full md:w-3/4 mx-auto"> 

        <h1 class="text-3xl text-center py-5">Solicitudes</h1>

        <nav class="filterSolicitudes capitalize flex gap-5 my-5 justify-items-start">
            <button @click="() => filter = null" class="flex gap-2 items-center justify-center px-2 text-white rounded-3xl w-[250px] h-[50px] capitalize" :class=" filter == null && 'active'">
                <font-awesome-icon icon="fa-solid fa-list-check" />
                <p>todos</p>
            </button>
            <button @click="() => filter = 'activo'" class="flex gap-2 items-center justify-center px-2 text-white rounded-3xl w-[250px] h-[50px] capitalize" :class=" filter == 'activo' && 'active'">
                <font-awesome-icon icon="fa-solid fa-check" />
                <p>activos</p>
            </button>
            <button @click="() => filter = 'rechazado'" class="flex gap-2 items-center justify-center px-2 text-white rounded-3xl w-[250px] h-[50px] capitalize" :class=" filter == 'rechazado' && 'active'">
                <font-awesome-icon icon="fa-solid fa-xmark" />
                <p>rechazados</p>
            </button>
        </nav>

        <ul class="w-full divide-gray-200">
            <li class="bg-white rounded-2xl overflow-hidden shadow-sm mt-3" v-if="solicitudes.length > 0 && filter == null" v-for="(solicitud, index) in solicitudes">
                <CardSolicitud :id="index" :solicitud="solicitud" @setImg="setImg" @setStatus="({id, status}) => setStatus(status, id)"/>
            </li>
            <li class="bg-white rounded-2xl overflow-hidden shadow-sm mt-3" v-else-if="solicitudes.filter((e: any) => e.estatus == filter).length > 0 && filter != null" v-for="(solicitud, index) in solicitudes.filter((e: any) => e.estatus == filter)">
                <CardSolicitud :id="index" :solicitud="solicitud" @setImg="setImg" @setStatus="({id, status}) => setStatus(status, id)"/>
            </li>
            <li class="bg-white rounded-2xl overflow-hidden shadow-sm p-6 text-center mt-3" v-else>
                <p>Sin solicitudes</p>
            </li>
        </ul>
    </main>
</template>

<style scoped>
.filterSolicitudes button{
    background-color: #041E42;
}

.filterSolicitudes button.active{
    background-color: #ec8a0a;
}

</style>