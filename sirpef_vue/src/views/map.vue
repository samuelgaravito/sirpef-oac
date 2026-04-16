<script lang="ts" setup>
import UseMap from '@/composables/votos/UseMap';
import mapa from '@/modules/Auth/components/votos/mapa.vue';
import cardRanking from '@/components/Votos/cardRanking.vue';
import TableDashToSee from '@/components/Votos/TableDashToSee.vue';
import Welcome from '@/components/sirpef/welcome.vue';
import FilteredDashboard from '@/modules/Auth/components/sirpef/FilteredDashboard.vue';
import { onMounted } from 'vue';

const {
    hidden,
    loadData,
    SetDates,
    PostUnidAsc,
    GetUnidAsc,
    Coordenadas,
    Top_Estados,
    SectionFloat,
    DataFiltered,
    Unidades_Ascritas
} = UseMap()

 

onMounted(() => GetUnidAsc())


</script>

<template>
    <Welcome
    title="Mapa"
    subtitle="aquí podrás ver la participación de tu personal por estados"
   
  >
    <div class="h-[600px]">
        <mapa :Coordenadas="Coordenadas" :loadData="loadData" id_Html="map"/>
     </div>
</Welcome>






    <main class="py-10 relative">
      <FilteredDashboard
        v-if="Unidades_Ascritas.length > 0"
        :PostUnidAsc="PostUnidAsc" 
        :Unidades_Ascritas="Unidades_Ascritas"
        @ChangueDate="SetDates"
      />
        <section v-if="SectionFloat" class="fixed bg-black bg-opacity-30 w-full h-full z-50 grid place-content-center top-0 left-0 cursor-pointer" @click="(e) => hidden(e as PointerEvent)">
        <div class="fadeout w-full h-[90%] mt-[5vh] md:w-[40vw] md:h-[60vh] bg-white rounded-2xl shadow overflow-auto p-10 cursor-default">
          <Loader class="mt-[25vh]" v-if="DataFiltered.total == -1"/>
          <div v-else>
            <h3 class="mx-5 bg-[#ECA008] hover:bg-[#010c41] text-white font-bold py-3 px-10 rounded-3xl my-5 text-center">{{DataFiltered.texto}}</h3>
            <p class="text-center my-3">Total: {{ DataFiltered.total.toLocaleString("es-ES") }}</p>
            <TableDashToSee v-if="DataFiltered.data.length > 0" :personas="DataFiltered.data" :text="'dskjdjk'" />
            <p v-else class="text-center my-[17vh]">No hay datos</p>
          </div>
        </div>
     </section>

     <div class="w-[90%] h-[600px] mx-auto">
        <mapa :Coordenadas="Coordenadas" :loadData="loadData" id_Html="map2"/>
     </div>

    <section class="w-[90%] mx-auto block md:grid grid-cols-2 gap-3 mx-auto text-white fadeout text-center mt-5 relative">
        <article class="bg-white rounded-b-2xl p-10 text-white text-center md:rounded-2xl content-center rounded-xl p-2 shadow-lg border grid">
            <h3 class="text-black mx-5 bg-[#010c41] hover:bg-[#ECA008] text-white font-bold py-3 px-10 rounded-3xl my-5 text-center">Estados con mayor Participación</h3>
            <cardRanking :data="Top_Estados.mayor" :loadData="loadData" whatis="estados"/>
        </article>

        <article class="bg-white rounded-b-2xl p-10 text-white text-center md:rounded-2xl content-center rounded-xl p-2 shadow-lg border grid">
            <h3 class="text-black mx-5 bg-[#ECA008] hover:bg-[#010c41] text-white font-bold py-3 px-10 rounded-3xl my-5 text-center">Estados con menor ParticipaciónPosición</h3>
            <cardRanking :data="Top_Estados.menor" :loadData="loadData" whatis="estados"/>
        </article>
    </section>
    </main>
</template>