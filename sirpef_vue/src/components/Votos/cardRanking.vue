<script setup lang="ts">
import type { cardRangink } from '@/types/votos/CardRanking';

const props = defineProps<{
    data: cardRangink[],
    loadData?: Function,
    whatis: string 
}>()

const SearchData = (name: string) => {
    props.loadData(undefined, undefined, name, props.whatis)
}
</script>


<template>
<div class="px-6 overflow-auto">
    <dl v-for="element in data">
        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
            <strong :class="'hover:text-blue-600 hover:cursor-pointer'" @click="() => SearchData(element.name)" class="font-black">{{element.name}}</strong>
            &nbsp; Participación: {{ element.total_votos }} </dt>
        <dd class="flex items-center mb-3">
            <div class="w-full bg-gray-200 rounded h-2.5 dark:bg-gray-700 me-2">
                <div class="bg-blue-600 h-2.5 rounded dark:bg-blue-500" :style="{'width': `${element.porcentaje_participacion}%`}"></div>
            </div>
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{  element.porcentaje_participacion.toFixed(1)}}%</span>
        </dd>
    </dl>
  </div>    
</template>

