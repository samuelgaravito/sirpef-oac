<script setup lang="ts">
import Welcome from '@/components/sirpef/welcome.vue';
import { onMounted } from "vue";
import useTypesCases from "../composables/useTypesCases";
import TablaFe from '../components/tablaFe.vue';

const {
  active,
  types,
  formData,
  GetTypes,
  getType,
  submit,
  DeleteType
} = useTypesCases()

onMounted(() => {
  GetTypes()
});

const columnas = [
  ["Tipo", "tipo"],
]


const openForm = async () => {
  active.value = !active.value
}


</script>

<template>
  <Welcome title="Tipos de casos" subtitle="Agregue, edite o elimine los tipos de caso">
    <form id="FormUnids" class="bg-white p-10 rounded-3xl w-[70%] grid place-items-center mx-auto"
      @submit.prevent="submit">
      <p class="text-gray-600">Nombre</p>
      <div class="relative z-0 w-full mb-5 group">
        <input type="text" name="tipo" v-model="formData.tipo"
          class="block py-5 !rounded-2xl px-0 w-full pl-5 text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
          placeholder="" required />
        <label
          class="capitalize peer-focus:font-medium pl-5 absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-8 scale-75 top-5 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-8">Tipo</label>
      </div>

      <p class="text-gray-600">Categoría</p>
      <small class="text-gray-600">(si es una Categoría sin padre se debe dejar vacio)</small>
      <select class="capitalize my-4" name="tipo_caso_padre_id" v-model="formData.tipo_caso_padre_id">
        <option :value="null">Sin selección</option>
        <option :value="type.id" v-for="type in types.filter(e => e.tipo_caso_padre_id == null)">{{ type.tipo }}
        </option>
      </select>

      <p class="text-gray-600">Color para la gráfica</p>
      <input type="color" name="color" v-model="formData.color">

      <button type="submit"
        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Registrar</button>
    </form>
  </Welcome>

  <main id="TblUnids" class="w-3/4 mx-auto">
    <TablaFe :data="types" :Delete="DeleteType" :editFunc="getType" :columnas="columnas">
    </TablaFe>
  </main>
</template>


<style scoped>
#FormUnids input,
#FormUnids button {
  margin: 10px auto;
}

#FormUnids button {
  width: 80%;
  display: block;
  margin: 20px auto;
}
</style>