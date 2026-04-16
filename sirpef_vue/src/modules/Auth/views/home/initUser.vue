  
<script setup lang="ts">
import { onMounted, ref } from "vue";
import Http from "@/utils/Http";
import Welcome from "@/components/sirpef/welcome.vue";
import QRcode from "../../components/QRcode.vue";
import CardEvent from "@/modules/SIRPEF/components/CardEvent.vue";

const events = ref([] as any[])
const EventoActual = ref({} as any)

const getEvents = async () => {
       try {
        const response = await Http.get('api/registro/eventos/asignados')
        const LastEvent = response.data.eventos_activos[0]
        events.value = response.data.ultimos_tres_eventos
        EventoActual.value = LastEvent
       } catch (error) {
        console.log(error.response.data)
       }
    }

    
onMounted(() => {
    getEvents()
})

</script>

<template>
    <Welcome
    :title="`QR Code y participacíon`"
    :subtitle="'Aqui podras observar tu codigo QR y los eventos donde has participado'"
  >
    <QRcode/>
</Welcome>

    <main class="md:w-[80%] mx-auto py-10">

       <h2 class="text-4xl font-extrabold my-4">Eventos pasados:</h2>
       <div class="block md:grid grid-cols-3">
            <CardEvent v-for="event in events" 
            :evento="event"
            :ChangueEvent="() => console.log('nada')"
            :loadDes="true"
            />
       </div>
    </main>
  </template>
