<script lang="ts" setup>
import { onMounted, watch } from "vue";
import useGeo from "../../../Personal/composables/useGeo"
import Controls from "@/components/sirpef/form/Controls.vue";

const props = defineProps<{
  emitForm: (event: Event) => void,
  step: number,
  values: any,
}>()

const {
  estados,
  municipios,
  parroquias,
  Unidades,
  getEstados,
  getMunicipios,
  getParroquias,
  getUnidades,
} = useGeo()


onMounted(() => {
  getEstados()
  getUnidades()

  if(props.values.estado) getMunicipios(props.values.estado)
  if(props.values.municipio) getParroquias(props.values.municipio)
})

watch(props.values, (newValues) => {
  if (newValues.estado) getMunicipios(newValues.estado);
  if (newValues.municipio) getParroquias(newValues.municipio);
});

</script>

<template>
  <form @submit.prevent="emitForm">
    
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 mt-5">

      <div class="relative z-0 w-full mb-5 group">
        <select v-model="values.estado" name="estado" required
          class="border text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
          <option value="0">Seleccione un estado</option>
          <option v-for="element in estados" :value="element.id">
            {{ element.estado }}
          </option>
        </select>
      </div>
      <div class="relative z-0 w-full mb-5 group">
        <select v-model="values.municipio" name="municipio" required
          class="border text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
          <option value="0">Seleccione un municipio</option>
          <option v-for="element in municipios" :value="element.id">
            {{ element.municipio }}
          </option>
        </select>
      </div>

    <div class="relative z-0 w-full mb-5 group">
      <select v-model="values.parroquia" name="parroquia" required
        class="border text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        <option value="0">Seleccione una parroquia</option>
        <option v-for="element in parroquias" :value="element.id">
          {{ element.parroquias }}
        </option>
      </select>
    </div>

    <div class="relative z-0 w-full mb-5 group">
      <select v-model="values.unidad_adscrita" name="unidad" required
        class="border text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        <option value="0">Seleccione una unidad adscrita</option>
        <option v-for="element in Unidades" :value="element.id">
          {{ element.nombre }}
        </option>
      </select>
    </div>

    <div class="relative z-0 w-full mb-5 group">
      <select v-model="values.sexo" name="sexp" required
        class="border text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        <option value="0">Seleccione su sexo</option>
        <option value="M">Masculino</option>
        <option value="F">Femenino</option>
      </select>
    </div>
    </div>

    <Controls :step="step"/>
  </form>
</template>