<script setup lang="ts">
import useMasiveLoad from '@/modules/SIRPEF/composables/useMasiveLoad';
import { obtenerHoraActual } from "@/utils/GetHora"
//import Banner from '../components/Votos/Banner.vue';
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
import Buttons from 'datatables.net-buttons';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import Welcome from '@/components/sirpef/welcome.vue';
DataTable.use(DataTablesCore);
DataTable.use(Buttons);


const {
  personas,
  hidden,
  popup,
  Envio_Datos,
  popupStatus,
  Loading,
  frase,
  SetFile,
  changueFile,
  is_parti,
  next
} = useMasiveLoad()

const buttons = [
  {
    extend: 'csv',
    text: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V160H256c-17.7 0-32-14.3-32-32V0H64zM256 0V128H384L256 0zM216 232V334.1l31-31c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-72 72c-9.4 9.4-24.6 9.4-33.9 0l-72-72c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l31 31V232c0-13.3 10.7-24 24-24s24 10.7 24 24z"/></svg>',
    filename: "cedulas_erroneas",
  }
]


const columns = [
  { data: 'nombre_completo', width: "30%" },
  { data: 'cedula', width: "10%" },
  { data: 'mensaje', width: "70%" },
];

const options = {
  language: {
    search: "Buscar",
    info: "Mostrando del _START_ a _END_ de _TOTAL_ registros",
    zeroRecords: "No se encuentran resultados",
  },
  dom: 'Bfrtip'
}

const options2 = {
  language: {
    search: "Buscar",
    info: "Mostrando del _START_ a _END_ de _TOTAL_ registros",
    zeroRecords: "No se encuentran resultados",
  },
  dom: 'Bfrtip',
  buttons: [
    {
      extend: 'csv',
      text: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V160H256c-17.7 0-32-14.3-32-32V0H64zM256 0V128H384L256 0zM216 232V334.1l31-31c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-72 72c-9.4 9.4-24.6 9.4-33.9 0l-72-72c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l31 31V232c0-13.3 10.7-24 24-24s24 10.7 24 24z"/></svg>',
      filename: "cedulas_correctas",
    }
  ]
}

</script>

<template>

  <Welcome title="Carga CSV" subtitle="Cargue su archivo CSV con las cédulas del personal">
    <form class="p-5 mx-5 z-100 bg-white border rounded-2xl shadow " @submit.prevent="SetFile" @change="changueFile">
      <div class="p-5 py-10 relative border-4 border-dotted border-gray-300 rounded-lg bg-white my-3 w-[90%] mx-auto">
        <svg class=" w-24 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
          stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
        </svg>
        <div class="input_field flex flex-col w-3/4 mx-auto text-center">
          <label>
            <input class="text-sm cursor-pointer w-3/4 hidden" name="fileExcel" type="file" accept=".csv" />
            <div v-if="personas.length == 0"
              class="text bg-[#ECA008] text-white border border-gray-300 rounded font-semibold cursor-pointer p-1 px-3">
              Seleccionar</div>
          </label>
          <p class=" uppercase my-2 block">{{ frase ? frase : "No hay archivo seleccionado" }}</p>
        </div>

      </div>


      <div class="grid place-items-center" v-if="Loading">
        <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
          viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path
            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
            fill="currentColor" />
          <path
            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
            fill="currentFill" />
        </svg>
      </div>


      <div class="block my-5" v-else>

        <div class="grid">
          <button v-if="personas.length > 0" @click="next = true" type="button"
            class="py-2 mx-auto block bg-[#010c41] w-full hover:bg-[#ECA008] text-white rounded-lg">Enviar</button>
          <button v-else type="submit"
            class="py-2 mx-auto block bg-[#ECA008] border-solid w-full hover:bg-[#010c41] text-white rounded-lg">Validar
            Archivo</button>
        </div>

        <ul
          class="items-center w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex mt-4"
          v-if="personas.length == 0">
          <li :class="is_parti === null || is_parti === true ? 'w-full' : 'w-[0px] opacity-0'"
            class="h-[45px] border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600 transition-all">
            <div class="flex items-center justify-center">
              <label class="py-3 mr-2 text-sm font-bold text-green-500  uppercase">Si participaron</label>
              <input :value="is_parti" type="checkbox" @click="is_parti = is_parti === null ? true : null"
                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
            </div>
          </li>

          <li :class="is_parti === null || is_parti === false ? 'w-full' : 'w-[0px] opacity-0'"
            class="h-[45px] border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600 transition-all">
            <div class="flex items-center justify-center">
              <label class="py-3 mr-2 text-sm font-bold text-red-500 uppercase">No participaron</label>
              <input :value="is_parti" type="checkbox" @click="is_parti = is_parti === null ? false : null"
                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
            </div>
          </li>
        </ul>
      </div>
    </form>
  </Welcome>


  <div id="contenedor">
    <section
      class="fixed bg-black bg-opacity-30 w-full h-full z-50 hidden place-content-center top-0 left-0 cursor-pointer"
      @click="(e) => hidden(e as PointerEvent)" ref="popup">
      <img class="h-90 bg-white rounded-lg fadeout" src="@/assets/csv.jpeg" v-if="popupStatus == -1">

      <form @submit.prevent="Envio_Datos"
        class="bg-white dark:bg-slate-800 rounded-lg px-6 py-8 ring-1 ring-slate-900/5 shadow-xl w-96 fadeout"
        v-else-if="popupStatus >= 1">
        <h3 class="text-slate-900 dark:text-white text-base font-medium capitalize mx-auto p-10">{{ popupStatus == 1 ?
          "Ingrese la hora de participación:" : "Justifique"}}</h3>
        <input type="time" name="hora" id="" :value="obtenerHoraActual()" v-if="popupStatus == 1" disabled>
        <input type="text" placeholder="Enfermo, en otro estado, etc..." name="motivo" maxlength="255" required v-else>

        <div class="grid place-items-center my-10" v-if="Loading">
          <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
            viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
              fill="currentColor" />
            <path
              d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
              fill="currentFill" />
          </svg>
        </div>
        <button type="submit"
          class="bg-[#ECA008] hover:bg-[#010c41] text-white font-bold py-2 px-10 rounded-3xl mx-auto block my-5"
          v-else>Enviar</button>
      </form>
    </section>
    <main id="container" class="xl:mt-10">

      <div class="md:gap-6 md:w-1/3 mx-auto bg-white z-20 relative rounded-2xl shadow md:mt-[10vh]"
        v-if="(next == false)">

        <div class="tableBtnCsvExport" v-if="personas.length > 0">
          <DataTable :columns="columns" :data="personas.filter(elem => elem.id === null)"
            :options="{ ...options, buttons }" id="table">
            <thead>
              <tr>
                <th>Nombre completo</th>
                <th>Cédula</th>
                <th>Mensaje</th>
              </tr>
            </thead>
          </DataTable>

        </div>


      </div>


      <div v-else class="mx-auto w-full md:w-[90%]">

        <div class="overflow-x-auto overflow-y-hidden gap-10 text-center">
          <h1
            class="text-3xl text-normal text-white bg-[#010c41] hover:bg-[#010c41] text-center my-5 md:w-1/4 md:my-3 py-3 px-10 rounded-3xl">
            <strong>Cédulas</strong> encontradas</h1>

          <div class="tableToCsv hiddenBtn">
            <DataTable :columns="columns" :data="personas.filter(elem => elem.id !== null)" :options="options2"
              id="table">
              <thead>
                <tr>
                  <th>Nombre completo</th>
                  <th>Cédula</th>
                  <th>Mensaje</th>
                </tr>
              </thead>
            </DataTable>

          </div>

          <h1
            class="text-3xl text-normal text-white bg-[#010c41] hover:bg-[#010c41] text-center my-5 md:w-1/4 md:my-3 py-3 px-10 rounded-3xl">
            <strong>Cédulas</strong> con errores</h1>

          <div class="tableToCsv">
            <DataTable :columns="columns.filter(elem => elem.data != 'nombre_completo')"
              :data="personas.filter(elem => elem.id === null)" :options="{ ...options, buttons }" id="table">
              <thead>
                <tr>
                  <th>Cédula</th>
                  <th>Mensaje</th>
                </tr>
              </thead>
            </DataTable>
          </div>

        </div>
        <div class="w-full hg-25 block mx-auto my-5 overflow-hidden">
          <button name="si"
            class="mx-auto bg-[#ECA008] hover:bg-[#010c41] block text-white font-bold py-2 px-10 rounded-3xl"
            @click="(e) => hidden(e as PointerEvent, is_parti ? 1 : 2)">Registrar</button>
        </div>
      </div>
    </main>


  </div>
</template>




<style scope>
#table {
  width: 100%;
  margin-left: auto;
  margin-right: auto;
  border: 1px solid #d1d5db;
  border-radius: 1.5rem;
  margin-top: 2.5rem;
  margin-bottom: 2.5rem;
  overflow: hidden;
  grid-column: 1/-1;
  text-align: center;
}

.tableToCsv .btnTable svg {
  fill: white;
  scale: .7;
}


.tableToCsv .dt-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  padding-bottom: 20px;
  overflow: auto;
}

.tableToCsv .dt-container div:first-child {
  grid-column: 1/2;
}


.tableToCsv .dt-container .dt-search label {
  font-size: 18px;
  margin: 10px auto;
  display: block;
  text-align: left;
}

.tableToCsv .dt-container .dt-search input {
  border-radius: 20px;
}



.tableToCsv .dt-buttons button {
  background-color: #ECA008;
  color: white;
  width: 130px;
  height: 50px;
  border-radius: 20px;
  transition: ease .3s;
}


.tableToCsv .dt-container>div:nth-child(1) {
  grid-column: 2/-1;
  grid-row: 1/2;
  display: flex;
  align-items: flex-end;
  justify-content: right;
}

.tableToCsv .dt-container div.dt-info {
  grid-column: 1/-1;
}


.tableToCsv tr {
  background-color: white;
  color: black;
  height: 16px;
  padding: 10px;
}

.tableToCsv .toolAndSvg {
  position: relative;
}

.tableToCsv .toolAndSvg:hover>h1 {
  display: block;
  position: absolute;
  background-color: white;
  padding: 10px;
  border-radius: 20px;
  border: 1px rgb(197, 197, 197) solid;
  top: 0;
  font-size: 12px;
  margin-left: 15px;
}

.tableToCsv td {
  padding: 10px;
}

.tableToCsv th {
  background-color: #010c41;
  font-weight: bold;
  color: white;
  margin-top: 20px;
  padding: 20px;
}

.tableToCsv td:first-child {
  text-align: left;
  text-indent: 20px;
}

.tableToCsv .dt-info {
  grid-column: 1/2;
  grid-row: 3/4;
}

.tableToCsv .dt-paging {
  grid-column: 2/-1;
  grid-row: 3/4;
  display: flex;
  justify-content: right;
}

.tableToCsv .dt-paging button {
  border: 1px #010c4160 solid;
  width: 40px;
  height: 40px;
  transition: .3s ease;
  margin-left: 2px;
  margin-right: 2px;
  background-color: white;
}

.tableBtnCsvExport .dt-buttons button,
.tableToCsv .dt-buttons button {
  background-color: #ECA008;
  color: white;
  width: 50px;
  height: 50px;
  border-radius: 20px 0% 0px 50%;
  transition: ease .3s;
  fill: white;
}

.tableBtnCsvExport .dt-buttons button svg,
.tableToCsv .dt-buttons button svg {
  width: 100%;
  height: 60%;
}

.tableToCsv .dt-paging button.current,
.tableToCsv .dt-paging button:hover,
.tableToCsv .dt-buttons button:hover,
.tableBtnCsvExport .dt-buttons button:hover {
  background-color: #010c41;
  color: white;
  border: #010c41;
  scale: 1.1;
}

.tableBtnCsvExport {
  position: absolute;
  left: 300px;
  top: -550px;
}


.tableBtnCsvExport .dt-search,
.tableBtnCsvExport #table,
.tableBtnCsvExport .dt-info,
.tableBtnCsvExport .dt-paging {
  display: none;
}


@media (max-width: 900px) {
  .tableToCsv .dt-container {
    display: block;
  }

  .tableToCsv .dt-container div {
    margin: 10px auto;
  }

  .tableBtnCsvExport .dt-buttons button {
    border-radius: 20px 50% 0% 0%;
  }


  .tableBtnCsvExport {
    position: absolute;
    left: 10px;
    top: -50px;
  }
}
</style>
