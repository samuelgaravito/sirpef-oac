<script setup lang="ts">
// @ts-nocheck
import Welcome from "@/components/sirpef/welcome.vue";
import { ref } from "vue";
import { useAuthStore } from '@/modules/Auth/stores';
import AppPaginationD from "@/components/AppPaginationD.vue";
import FormInput from "@/modules/SIRPEF/components/FormInput.vue";
import ListOptions from "@/modules/Personal/components/ListOptions.vue";
import useCasesTable from "../composables/useCasesTable";
import ModalInfo from "../components/modalInfo.vue";
import ModalDescripcion from "../components/ModalDescripcion.vue";
import CardInfoUser from '@/components/Votos/CardInfoUser.vue';

const store = useAuthStore()

const casePersona_id = ref(null)
const descripcion = ref(null)

const {
  errors,
  data,
  router,
  result,
  confirmacion,
  setSearch,
  setSort,
  GetUser,
  deleteCaso
} = useCasesTable()

</script>


<template>

  <Welcome title="Gestión de Casos" subtitle="Aquí encontrarás el caso">
    <FormInput :FunGetUser="GetUser" :finger="false" :cort="false" @setCortesia="null" />
  </Welcome>

  <ModalDescripcion v-if="descripcion" :descripcion="descripcion" @close="descripcion = null" />

  <ModalInfo v-if="casePersona_id" :casePersona_id="casePersona_id" @close="casePersona_id = null" />

  <div class="col-start-2 col-end-4 mx-auto w-[90%] panel" v-if="Object.keys(result).length == 0">
    <div class="mb-6 flex justify-between items-center rounded-2xl">
      <div class="flex items-center">
        <form class="flex w-full rounded" @submit.prevent="setSearch">
          <input class="miinput" type="text" name="search" v-model="data.search" placeholder="Buscar..." />
        </form>
      </div>
    </div>

    <div class="table-data__wrapper">
      <table class="table-data">
        <thead>
          <tr class="">
            <th>Estado</th>
            <th>Punto de cuenta</th>
            <th class="w-[30%]">
              <AppLink to="#" @click.prevent="setSort('nombre_completo')">Nombre completo</AppLink>
            </th>
            <th class="">
              <AppLink to="#" @click.prevent="setSort('cedula')">Cédula</AppLink>
            </th>
            <th class="">
              <AppLink to="#" @click.prevent="setSort('sexo')">Género</AppLink>
            </th>
            <th class="">
              <AppLink to="#" @click.prevent="setSort('telefono')">Tipo de Caso</AppLink>
            </th>

            <th class="ubi_ads">
              Descripción de caso
            </th>
            <th class="">Ficha</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="data.rows.length > 0" v-for="row in data.rows" :key="row.id" class="">
            <td class="capitalize font-bold text-center">
              {{ row.estatus_caso || "sin información" }}
              <p class="text-orange-600">{{ row?.estatus == false ? '(Por revisión)' : '' }}</p>
            </td>
            <td class="text-center">
              <font-awesome-icon title="Tiene punto" v-if="row.havePunto" class="text-green-500 scale-[1.6]"
                icon="check" />

              <font-awesome-icon title="No tiene" v-else class="text-red-600 scale-[1.6]" icon="xmark" />
            </td>
            <td class="text-center">
              {{ row.nombre_completo }}
            </td>
            <td class="text-center">
              {{ row.cedula }}
            </td>
            <td class="text-center">
              <font-awesome-icon title="Femenino" v-if="row.genero == 'Femenino'" class="text-pink-600 scale-[1.6]"
                icon="fa-solid fa-person-dress" />
              <font-awesome-icon title="Masculino" v-else-if="row.genero == 'Masculino'"
                class="text-blue-500 scale-[1.6]" icon="fa-solid fa-person" />
              <p v-else>Sin género</p>
            </td>
            <td class="text-center">
              {{ row.tipo_caso || "sin información" }}
            </td>

            <td class="text-center ubi_ads">

              <p class="hidden md:block">
                {{ row.descripcion || "sin información" }}
              </p>

              <button class="flex items-center justify-center gap-2 bg-[#2052C7] text-white rounded-2xl p-3 md:hidden" @click="descripcion = row.descripcion">
                <font-awesome-icon icon="eye" />
              </button>
            </td>

            <td class="w-[50px] text-center">
              <button title="Ver Ficha"
                class="bg-[#ECA008] text-white font-bold py-2 px-5 mr-2 rounded-3xl my-8 hover:bg-[#010c41]"
                @click="() => casePersona_id = row.registro_id">
                <font-awesome-icon icon="file-lines" />
              </button>

              <button title="eliminar caso"
                class="bg-[#010c41] text-white font-bold py-2 px-5 rounded-3xl my-8 hover:bg-[#ECA008]"
                @click="() => deleteCaso(row.registro_id)">
                <font-awesome-icon icon="trash-can" />
              </button>
            </td>
          </tr>
          <tr v-if="data.rows.length == 0">
            <td colspan="8">Casos no encontrados.</td>
          </tr>
        </tbody>
      </table>
    </div>
    <span v-if="Object.keys(errors).length > 0" class="text-red-500">{{ errors }}</span>
    <AppPaginationD v-if="data.links" :links="data.links"></AppPaginationD>
  </div>

  <section class="results my-10 w-full md:w-3/4" v-if="Object.keys(result).length > 0">
    <CardInfoUser :UserData="result[0]" title="Datos personales" icon="fa-solid fa-user" />
    <CardInfoUser :UserData="result[1]" title="Punto de cuenta" icon="fa-solid fa-location-dot"
      @seePDC="({ registro_id }) => casePersona_id = registro_id"
      @deletePDC="({ registro_id }) => deleteCaso(registro_id)" />

  </section>

</template>



<style scoped>
.ubi_ads {
  width: 900px !important;
  white-space: wrap;

}


.results {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
  gap: 20px;
  margin: 30px auto;
}
</style>