<script setup lang="ts">
import CardSolicitud from '../components/Card-solicitud.vue';
import useJubilados from '../composables/useJubilados';
import { onMounted, ref } from 'vue';

const seeImg = ref({
    img: '',
    id: ''
})
const {
    solicitudes,
    filter,
    GetSoli,
    AcceptJubi
} = useJubilados()

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
    class="fixed bg-black bg-opacity-30 w-full h-full grid top-0 left-0 cursor-pointer z-[200] place-items-center">
    <img :src="seeImg.img" alt="solicitud" class="cursor-default mx-auto min-h-[30%] max-h-[60%] self-start">
</section>

    <main class="w-full md:w-3/4 mx-auto"> 

        <h1 class="text-3xl text-center py-5">Fe de vida (Solicitudes)</h1>

        <nav class="filterSolicitudes capitalize flex gap-5 my-5 justify-items-start">
            <button @click="() => filter = null" class="flex gap-2 items-center justify-center px-2 text-white rounded-3xl w-[250px] h-[50px] capitalize" :class=" filter == null && 'active'">
                <font-awesome-icon icon="fa-solid fa-xmark" />
                <p>Pendientes</p>
            </button>
            <button @click="() => filter = true" class="flex gap-2 items-center justify-center px-2 text-white rounded-3xl w-[250px] h-[50px] capitalize" :class=" filter == true && 'active'">
                <font-awesome-icon icon="fa-solid fa-check" />
                <p>Aceptados</p>
            </button>
            <button @click="() => filter = false" class="flex gap-2 items-center justify-center px-2 text-white rounded-3xl w-[250px] h-[50px] capitalize" :class=" filter == false && 'active'">
                <font-awesome-icon icon="fa-solid fa-check" />
                <p>Rechazados</p>
            </button>
            <button @click="() => filter = 'todos'" class="flex gap-2 items-center justify-center px-2 text-white rounded-3xl w-[250px] h-[50px] capitalize" :class=" filter == 'todos' && 'active'">
                <font-awesome-icon icon="fa-solid fa-list-check" />
                <p>todos</p>
            </button>
        </nav>

        <ul class="w-full divide-gray-200">
            <li class="bg-white rounded-2xl overflow-hidden shadow-sm mt-3" v-if="solicitudes.length > 0 && filter == 'todos'" v-for="(solicitud, index) in solicitudes">
                <CardSolicitud :id="index" :solicitud="solicitud" @setImg="setImg" @AcceptJubi="({id, status}) => AcceptJubi(id, status)"/>
            </li>
            <li class="bg-white rounded-2xl overflow-hidden shadow-sm mt-3" v-else-if="solicitudes.filter((e: any) => e.voto === filter).length > 0 && filter != 'todos'" v-for="(solicitud, index) in solicitudes.filter((e: any) => e.voto == filter)">
                <CardSolicitud :id="index" :solicitud="solicitud" @setImg="setImg" @AcceptJubi="({id, status}) => AcceptJubi(id, status)"/>
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