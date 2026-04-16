<script lang="ts" setup>
// @ts-nocheck
import UseEvents from "@/modules/SIRPEF/composables/UseEvents"
import CreateForm from '../../components/createForm.vue';
import { onMounted } from 'vue';
import FormMasive from "@/modules/SIRPEF/components/events/FormMasive.vue"
import CardEvent from "../../components/CardEvent.vue";
import Welcome from "@/components/sirpef/welcome.vue";
import { useAuthStore } from "@/modules/Auth/stores";
import { useEventsName } from "@/stores/nameEvent";


const {
    GetEvents,
    senddata,
    ChangueEvent,
    eventos,
    event_Active,
    id_event,
    id_event_edit,
    enterEvent,
    GetUserEvent,
    resetEvent
} = UseEvents()

const store = useAuthStore()
const NameEvent = useEventsName()

onMounted(() => {
    GetEvents()
    GetUserEvent()
})


const hidden = (e?: PointerEvent) => {
    const target = e.target as HTMLElement
    if (e && target.tagName === "SECTION") {
        id_event.value = 0
    }
}


</script>

<template>
    <main>
        <Welcome title="Gestión de eventos"
            subtitle="Esta pantalla permite a los usuarios crear y organizar eventos de manera eficiente, asegurando que toda la información necesaria esté disponible y bien estructurada"
            v-if="store.authUser.isAdmin">
            <CreateForm :id_event_edit="id_event_edit" />
        </Welcome>

        <Welcome title="Gestión de eventos"
            subtitle="Esta pantalla permite a los usuarios crear y organizar eventos de manera eficiente, asegurando que toda la información necesaria esté disponible y bien estructurada"
            v-else>

            <CardEvent v-if="eventos.length > 0 && event_Active.id" :evento="eventos.find(e => e.id == event_Active.id)"
                :id_event="id_event" :ChangueEvent="ChangueEvent" @SetIdToEdit="(id) => id_event_edit = id"
                @enterEvent="(evento) => enterEvent(evento)" 
                @resetEvento="(evento_id) => resetEvent(evento_id)" 
                @loadPersonal="(id) => id_event = id" :loadDes="true"
                :evento_act="NameEvent.id" />
        </Welcome>




        <section v-if="id_event != 0"
            class="fixed bg-black bg-opacity-30 w-full h-full z-50 grid place-content-center top-0 left-0 cursor-pointer"
            @click="(e) => hidden(e as PointerEvent)">
            <div class="w-[90vw] md:w-[30vw]">
                <FormMasive :senddata="senddata" />
            </div>
        </section>

        <h2 class="text-gray-800 text-4xl mt-10 w-[80%] mx-auto font-semibold">Eventos ({{ eventos.length }})</h2>

        <div id="containerEventos" class="w-[90%] mx-auto block md:grid">
            <CardEvent v-for="evento in eventos" :evento="evento" :id_event="id_event" :ChangueEvent="ChangueEvent"
                @SetIdToEdit="(id) => id_event_edit = id" @enterEvent="(evento) => enterEvent(evento)"
                @loadPersonal="(id) => id_event = id" :loadDes="false" :evento_act="NameEvent.id"
                v-if="eventos.length > 0" />
        </div>
    </main>
</template>

<style scoped>
#containerEventos article {
    background-position: right bottom 0px;
    background-image: url(/Bg-welcome.svg) !important;
    background-repeat: no-repeat;
    background-size: cover;
}

#containerEventos {
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 10px;
    margin-top: 40px;
    width: 80%;
    padding-bottom: 90px;

}
</style>