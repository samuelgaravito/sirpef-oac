<script setup lang="ts">

import type { unid_Adsc } from "@/types/votos/unidAds";
import {ref, computed} from "vue"

const props = defineProps<{
  Unidades_Ascritas: unid_Adsc[],
  SelectValue: Function,
  ActiveUnid: string | null,
  width: string
}>()

const searchText = ref("")
const activeList = ref(false)

const filteredOptions = computed(() => {
  activeList.value = true
  if (!searchText.value)  {
    return props.Unidades_Ascritas
  }
  const regex = new RegExp(searchText.value, 'i');
  return props.Unidades_Ascritas.filter(unid => regex.test(unid.nombre));
});

const ChangueValue = (id: Number) => {
  activeList.value = false
  searchText.value = ""
  props.SelectValue(id)
}


</script>

<template>
  <div :class="width" class="relative" @mousedown="activeList = true" @mouseleave="activeList = false">
    <input type="text" v-model="searchText" :placeholder="ActiveUnid" class="text-xs h-full placeholder:text-gray-700">
    <div v-if="activeList == true" class="absolute max-h-96 z-10 bg-white pb-5 w-full shadow-lg rounded-b-3xl overflow-auto">
      <button v-for="unid in filteredOptions" class="block hover:bg-gray-100 transition-all p-4 text-left indent-1 w-full" :key="unid.nombre" @click="() => ChangueValue(unid.id)">{{ unid.nombre }}</button>
    </div>
  </div>
</template>

<style scoped>
input[type="text"] {
  border-radius: 10px;
  height: 40px;
}
</style>