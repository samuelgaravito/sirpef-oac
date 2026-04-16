<script setup lang="ts">
// @ts-nocheck
import SectionFloat from "@/components/Votos/SectionFloat.vue";
import Welcome from '@/components/sirpef/welcome.vue';
import { onMounted} from "vue";
import UseTipos from "@/composables/votos/UseTipos"
import UseUnidAds from "@/composables/votos/UseUnidAds"
import Tabla from "@/components/Votos/tabla.vue";


const {
    Tipos,
    GetTipos
    } = UseTipos()

const {
  unids,
  GetUnidades,
  NewUnidad,
  active,
  DeleteUnidad
} = UseUnidAds()

onMounted(() => {
  GetUnidades()
});


const columnas= [
  ["Nombre", "nombre"],
]


const openForm = async () => {
  active.value = !active.value
  GetTipos()
}


</script>

<template>
  <Welcome
    title="Unidades Adscritas"
    subtitle="Agregue, edite o elimine las unidades adscritas"
    
  >
  <form id="FormUnids" class="bg-white p-10 rounded-3xl w-[70%] h-[400px] mx-auto" @submit.prevent="NewUnidad">
        <div class="relative z-0 w-full mb-5 group" v-for="campo in ['nombre', 'código', 'RIF']">
            <input type="text" :name="campo == 'código' ? 'codigo' : campo" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required />
            <label class="capitalize peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-8 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-8">{{campo}}</label>
        </div>
        <select name="tipo_id" required>
              <option value="">Seleccionar tipo de unidad</option>
              <option class="capitalize" :value="Tipo.id" v-for="Tipo in Tipos">{{Tipo.tipo_oficina}}</option>
        </select>

        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Registrar</button>
      </form>
</Welcome>

     <main id="TblUnids" class="w-3/4 mx-auto">
        <Tabla :data="unids" :Delete="DeleteUnidad" :columnas="columnas">
        </Tabla>

     </main>
</template>


<style scoped>

#FormUnids input, #FormUnids button{
  margin: 10px auto;
}

#FormUnids button{
  width: 80%;
  display: block;
  margin: 20px auto;
}

</style>