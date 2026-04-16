<script setup lang="ts">
// @ts-nocheck
import useEmployesTable from "../../Auth/composables/useEmployesTable";
import AppPaginationD from "@/components/AppPaginationD.vue";
import tablePersonal from "@/components/Votos/tablePersonal.vue";
import { useAuthStore } from '@/modules/Auth/stores';
import useEmployees from '@/composables/useEmployees';
import Welcome from "@/components/sirpef/welcome.vue";
//import NewPersona from "../components/NewPersona.vue";
import RecaudosForm from "../components/formRecaudos/index.vue";
import { ref } from "vue";
import ListOptions from "../components/ListOptions.vue";
import FormPersona from "../components/NewPersona/index.vue"
const store = useAuthStore()

const persona = ref({} as any)


const {
  errors,
  data,
  router,
  confirmacion,
  setSearch,
  setSort  
} = useEmployesTable()

</script>


<template>

  <Welcome
    title="Gestión de Personal"
    subtitle="Aquí encontrarás el personal asignado"
  >
  <FormPersona/>
</Welcome>


  <RecaudosForm v-if="Object.keys(persona).length > 0" @clear="() => persona = {}" :persona="persona"/>


    <div class="col-start-2 col-end-4 mx-auto w-[90%] panel">  
      <div class="mb-6 flex justify-between items-center rounded-2xl">
        <div class="flex items-center">
          <div class="flex w-full rounded">
            <input
              class="miinput"
              type="text"
              v-model="data.search"
              @input="setSearch"
              placeholder="Buscar..."
            />
          </div>
        </div>
      </div>

      <div class="table-data__wrapper">
        <table class="table-data">
          <thead>
            <tr class="">
              <th class="">
                
                <AppLink to="#" @click.prevent="setSort('nombre_completo')">Nombre completo</AppLink>
              </th>
              <th class="">
                <AppLink to="#" @click.prevent="setSort('cedula')">Cédula</AppLink>
              </th>
              <th class="">
                <AppLink to="#" @click.prevent="setSort('sexo')">Género</AppLink>
              </th>
              <th class="">
                <AppLink to="#" @click.prevent="setSort('telefono')">Teléfono</AppLink>
              </th>

              <th class="ubi_ads">
                <AppLink to="#" @click.prevent="setSort('ubicacion_administrativa')">Ubicación administrativa</AppLink>
              </th>
              <th>Estado</th>
              <th v-if="store.authUser.isAdmin" class="">Acción</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in data.rows" :key="row.id" class="text-center">
              <!--td>
                <div v-if="row.data.length > 0">
                  <font-awesome-icon title="Si participó" class="text-green-700 scale-[1.4]" v-if="row.data.voto" icon="fa-solid fa-user-check" />
                  <font-awesome-icon title="No participó" class="text-red-600 scale-[1.4]" v-else-if="row.data.voto == false" icon="fa-solid fa-user-xmark" />
              </div>
              <font-awesome-icon title="Sin participación" class="text-[#ECA008] scale-[1.4]" v-else icon="fa-solid fa-user-clock" />
              </td-->

              <td class="">
                {{ row.nombre_completo }}
              </td>
              <td class="">
                {{ row.cedula }}
              </td>
              <td class="">
                <font-awesome-icon title="Femenino" v-if="row.sexo == 'F'" class="text-pink-600 scale-[1.6]"  icon="fa-solid fa-person-dress" />
                <font-awesome-icon title="Masculino" v-else-if="row.sexo == 'M'" class="text-blue-500 scale-[1.6]"  icon="fa-solid fa-person" />
                <p v-else>Sin género</p>
              </td>
              <td class="">
                {{ row.telefono || "sin información" }}
              </td>

              <td class="ubi_ads">
                {{ row.ubicacion_administrativa || "sin información"}}
              </td>

              <td class="capitalize">
                {{ row.estatus || "sin información"}}
              </td>
              <td v-if="store.authUser.isAdmin" class="w-[50px]"> 
                <ListOptions :row="row" :confirmacion="confirmacion" @setPersona="(new_persona) => persona = new_persona"/>
            </td>
            </tr>
            <tr v-if="data.rows.length == 0">
              <td class="" colspan="4">Usuarios no encontrados.</td>
            </tr>
          </tbody>
        </table>
      </div>
      <span v-if="Object.keys(errors).length > 0" class="text-red-500">{{ errors }}</span>
      <AppPaginationD v-if="data.links" :links="data.links"></AppPaginationD>      
    </div>

 <!-- <div class="col-start-2 col-end-4 mx-auto w-[90%]">
    <h1 class="text-3xl text-normal text-white bg-[#010c41] hover:bg-[#010c41] text-center my-5 md:w-1/4 md:my-3 py-3 px-10 rounded-3xl"><strong>Personal</strong> Asignados</h1>
    <tablePersonal :loadall="GetEmployes" :employees="employees"/>
  </div>
  -->

</template>



<style scoped>

.ubi_ads{
  width: 300px!important;
  white-space: wrap;
}
</style>