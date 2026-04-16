<script lang="ts" setup>
import {Doughnut} from 'vue-chartjs'

const {PieSex, DonutSex, TableAges, ChangueSexData} = defineProps<{
  PieSex: any;
  DonutSex: any;
  TableAges: any,
  ChangueSexData: Function
}>()


</script>

<template>
    <article id="barGender" v-if="Object.keys(PieSex).length > 0" class="dona_and_leyend md:h-[100%!important] relative block md:flex items-center gap-2 rounded-xl bg-white text-[#010c41] shadow-lg border col-start-1 col-end-3 overflow-hidden">
          <div class="btn-Floats grid grid-cols-1 content-center top-[-50%] gap-3 md:content-end md:grid-cols-2 z-10">
            <button class="bg-[#C80036] hover:bg-[#FF7676] text-white p-2 block rounded w-full cursor-pointer h-full" title="Cargar Mujeres" @click="() => ChangueSexData('F')">F</button>
            <button class="bg-[#03a9f4] hover:bg-[#1976d2] text-white p-2 block rounded w-full cursor-pointer h-full" title="Cargar Hombres" @click="() => ChangueSexData('M')">M</button>
          </div>
          <div id="dona" class="relative mx-auto">
            <p class="absolute pointer-events-none inset-0 flex items-center justify-center text-2xl">{{ (((PieSex.data[PieSex.active].Si / PieSex.data.Total_de_Personal  ) * 100)).toFixed(2) }}%</p>
            <Doughnut :data="PieSex.graph" :options="{maintainAspectRatio: false, plugins: { legend: {display: false}, datalabels: {display: false} }}" v-if="Object.keys(PieSex).length > 0"/>
          </div>

            <div id="leyend" class="w-full md:w-3/4">
              <div
              v-for="(item, index) in Object.keys(PieSex.data[PieSex.active])"
              :style="{'background': `linear-gradient(to right, ${PieSex.backgroundColor[index]} ${((PieSex.data[PieSex.active][item] / PieSex.data.Total_de_Personal  ) * 100).toFixed(2)}%, #F0F0F0 10%)`}"
              class="bg-[#F0F0F0] w-[95%] grid grid-cols-6 gap-10 text-center items-center my-3 border mx-auto justify-beeteen rounded-md overflow-hidden" 
              >
              <h1
              :style="{'background-color': PieSex.backgroundColor[index]}"
              class="w-16 p-2.5 text-white font-bold col-start-1 col-end-2 text-2xl transition-all" 
              >{{ PieSex.data[PieSex.active][item].toLocaleString('es-ES', { useGrouping: true })}}</h1>

              <p class="text-lg font-bold w-max col-start-3 col-end-5  text-gray-600 text-center" 
               :class="(((PieSex.data[PieSex.active][item] / PieSex.data.Total_de_Personal  ) * 100)) > 45 ? 'text-white ' : null"
              >{{PieSex.graph.labels[index]}}</p>
              <label class="text-xl col-start-5 col-end-7 font-bold" :class="(((PieSex.data[PieSex.active][item] / PieSex.data.Total_de_Personal  ) * 100)) > 80 ? 'text-white ' : null">
                {{((PieSex.data[PieSex.active][item] / PieSex.data.Total_de_Personal  ) * 100).toFixed(2)}}%
              </label>
            </div>
            </div>
    </article>


    <article v-if="Object.keys(PieSex).length > 0" id="donaSex" class="bg-white rounded-b-2xl text-white text-center md:rounded-2xl md:grid md:content-center rounded-xl p-2 shadow-lg border relative col-start-3 col-end-4">

    <Doughnut :data="DonutSex" :options="{maintainAspectRatio: false, plugins: { legend: {display: true}, datalabels: {display: true, color: 'white', font: {weight: 'bold', size: 20} ,formatter: (value) => value > 0 ? `${((value / PieSex.data.Total_de_Personal) * 100).toFixed(1)}%` : ''}}}" v-if="Object.keys(DonutSex).length > 0"/>
      <div v-if="Object.keys(DonutSex).length > 0" class="absolute pointer-events-none inset-0 flex items-center mt-10 justify-center text-2xl text-black">
        <p class="text-4xl font-bold md:text-1xl text-gray-600">{{ DonutSex.datasets[0].data.reduce((anterior, actual) => anterior + actual, 0).toLocaleString('es-ES', { useGrouping: true })}} 
        <small class="block w-3/4 text-sm font-bold text-light-lavender mx-auto">Total de participación géneros</small></p>
      </div>
    </article>
    
    
    <article id="tableAge" class="bg-white rounded-b-2xl text-white text-center md:rounded-2xl md:grid md:content-center rounded-xl p-2 shadow-lg border col-start-1 col-end-4">
      
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg" v-if="Object.keys(PieSex).length > 0">
      
    <p class="text-black text-xl my-2 text-left pl-4">{{ PieSex.active == 'F' ? 'Femenino' : 'Masculino'}}</p>
    <table class="w-full text-sm text-left rtl:text-right text-gray-500">
        <thead class="text-xs uppercase text-center text-white" :class=" PieSex.active == 'F' ? 'bg-[#BA0001]' :'bg-[#0d47a1]'">
            <tr>
                <th scope="col" class="px-3 py-3">
                  edades
                </th>
                <th scope="col" class="px-3 py-3">
                  Participó
                </th>
                <th scope="col" class="px-3 py-3">
                  No Participó
                </th>
                <th scope="col" class="px-3 py-3">
                  Faltantes
                </th>
                <th scope="col" class="px-3 py-3">
                  Total
                </th>
            </tr>
        </thead>
        <tbody class="text-center">
            <tr class="border-b border-gray-200 dark:border-gray-700">
              <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-200">
                    18-25
                </th>
                
                <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-50" v-for="element in Object.keys(TableAges[PieSex.active])">
                    {{ TableAges[PieSex.active][element]["18-25"].toLocaleString("es-ES", { useGrouping: true }) }}
                </th>
            </tr>
            <tr class="border-b border-gray-200 dark:border-gray-700">
              <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-200">
                26-33
              </th>
                
                <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-50" v-for="element in Object.keys(TableAges[PieSex.active])">
                    {{ TableAges[PieSex.active][element]["26-33"].toLocaleString("es-ES", { useGrouping: true }) }}
                </th>
            </tr>
            <tr class="border-b border-gray-200 dark:border-gray-700">
              <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-200">
                34-41
                </th>
                
                <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-50" v-for="element in Object.keys(TableAges[PieSex.active])">
                    {{ TableAges[PieSex.active][element]["34-41"].toLocaleString("es-ES", { useGrouping: true }) }}
                </th>
            </tr>
            <tr class="border-b border-gray-200 dark:border-gray-700">
              <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-200">
                42-49
                </th>
                
                <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-50" v-for="element in Object.keys(TableAges[PieSex.active])">
                    {{ TableAges[PieSex.active][element]["42-49"].toLocaleString("es-ES", { useGrouping: true }) }}
                </th>
            </tr>
            <tr class="border-b border-gray-200 dark:border-gray-700">
              <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-200">
                50-55
                </th>
                
                <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-50" v-for="element in Object.keys(TableAges[PieSex.active])">
                    {{ TableAges[PieSex.active][element]["50-55"].toLocaleString("es-ES", { useGrouping: true }) }}
                </th>
            </tr>
            <tr class="border-b border-gray-200 dark:border-gray-700">
              <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-200">
                56+
                </th>
                
                <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-50" v-for="element in Object.keys(TableAges[PieSex.active])">
                    {{ TableAges[PieSex.active][element]["56+"].toLocaleString("es-ES", { useGrouping: true }) }}
                </th>
            </tr>
            <tr class="border-b border-gray-200 dark:border-gray-700">
              <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-200">
                Total
                </th>
                
                <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-50">
                </th>
                <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-50">
                </th>
                <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-50">
                </th>
                <th class="px-6 py-4 text-2xl" :class=" PieSex.active == 'F' ? 'text-[#BA0001]' : 'text-[#0d47a1]'">
                  {{ Object.values(TableAges[PieSex.active].Total).reduce((acc : number, value: number) => acc + value, 0) }}
                </th>
            </tr>
        </tbody>
    </table>
</div>


    </article>
</template>