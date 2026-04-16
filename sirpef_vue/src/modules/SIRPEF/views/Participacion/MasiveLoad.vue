<script setup lang="ts">
import useMasiveLoad from '@/modules/SIRPEF/composables/useMasiveLoad';
import {onMounted, onUnmounted} from "vue"
import FormInput from '@/modules/SIRPEF/components/FormInput.vue';
import {obtenerHoraActual} from "@/utils/GetHora"
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
import Welcome from '@/components/sirpef/welcome.vue';
import { useRoute } from 'vue-router';
DataTable.use(DataTablesCore);


const { personas, DeleteEmpleado, hidden, popup, GetUser, Envio_Datos_Masive, popupStatus, Loading} = useMasiveLoad()


function renderDeleteButton(data, type, row) {
  if (type === 'display') {
    return `<button class="bg-[#ECA008] rounded-lg text-white px-4 py-1 transition-all hover:bg-[#010c41] hover:px-5 btnTable" title="Borrar voto" onclick='FuncDelete(${row.cedula})'><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg></button>`;
  }
  return '';
}

onMounted(() => {
  (document as any).FuncDelete = (cedula: string) => DeleteEmpleado(cedula);
});

onUnmounted(() => {
  delete (document as any).FuncDelete;
});

const columns = [
  { data: 'nombre_completo', width:"30%" },
  { data: 'cedula',  width:"10%"},
  { data: 'telefono' },
  { data: 'unidad_Adscrita' },
  { data: 'Accion', render: renderDeleteButton,  width:"20%"} // Agrega esta línea
];

const options = {
  language: {
    search: "Buscar",
    info: "Mostrando del _START_ a _END_ de _TOTAL_ registros",
    zeroRecords: "No se encuentran resultados",
  },
}

const route = useRoute()


onMounted(() => {
  if(route.params.ci) GetUser(route.params.ci as string)
})


</script>

<template>
  <Welcome
    title="Registro múltiple"
    subtitle="Lleve acabo un registro múltiple cargando una a una las cédula del personal"
  >
  <FormInput :FunGetUser="GetUser"/>
</Welcome>


 <div id="contenedor">
  <Banner v-if="personas.length == 0" text="Ingrese los números cédulas que desea registrar su participación en el sistema"/>
    <section class="fixed bg-black bg-opacity-30 w-full h-full z-50 hidden place-content-center top-0 left-0 cursor-pointer" @click="(e) => hidden(e as PointerEvent)" ref="popup">
        <img class="h-90 bg-white rounded-lg fadeout" src="@/assets/masive.jpeg" v-if="popupStatus == -1">
            
        <form @submit.prevent="Envio_Datos_Masive" class="bg-white dark:bg-slate-800 rounded-lg px-6 py-8 ring-1 ring-slate-900/5 shadow-xl w-96 fadeout" v-else-if="popupStatus >= 1">
            <h3 class="text-slate-900 dark:text-white text-base font-medium capitalize mx-auto p-10">{{ popupStatus == 1 ? "Ingrese la hora de participación:" : "Justifique"}}</h3>
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
            <button type="submit" class="bg-[#ECA008] hover:bg-[#010c41] text-white font-bold py-2 px-10 rounded-3xl mx-auto block my-5"
                v-else>Enviar</button>
        </form>
    </section>
    <main id="container" class="xl:mt-10">
        <div v-if="personas.length > 0" class="mx-auto w-full md:w-3/4">
        <div class="overflow-x-auto overflow-y-hidden">
          <DataTable :columns="columns" :data="personas" :options="options" id="table">
        <thead>
            <tr>
                <th>Nombre completo</th>
                <th>Cédula</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Acción</th>
            </tr>
        </thead>
        </DataTable>
        </div>
  

            <div class="w-full md:w-96 hg-30 block md:grid grid-cols-2 gap-1 mx-auto">
                <button name="si" class=" mx-auto bg-[#ECA008] hover:bg-[#010c41] block text-white font-bold py-2 px-10 rounded-3xl my-2 md:my-8 " 
                @click="(e) => hidden(e as PointerEvent, 1)">Si Participaron</button>
                <button name="si" class="mx-auto bg-[#010c41] hover:bg-[#ECA008] block text-white font-bold py-3 px-10 rounded-3xl my-2 md:my-8" 
                @click="(e) => hidden(e as PointerEvent, 2)">No Participaron</button>
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
  text-align:center;
}

#table .dt-container{
  padding-bottom: 20px;
  overflow: auto;
}


#container .dt-search, #container .dt-length{
    display: none;
}

#table tr {
  background-color: white;
  color: black;
  height: 16px;
  padding: 10px;
}

#table td{
  padding: 10px;
}

#table th {
  background-color: #010c41;
  font-weight: bold;
  color: white;
  margin-top: 20px;
  padding: 20px;
}

#table .dt-container .dt-layout-table {
  grid-column: 1/3;
}


#table .dt-info{
  grid-column: 1/2;
  grid-row: 3/4;
}

#container .dt-paging{
  grid-column: 2/-1;
  grid-row: 3/4;
  display: flex;
  justify-content: right;
}

#container .dt-paging button{
  border: 1px #010c4160 solid;
  width: 40px;
  height: 40px;
  transition: .3s ease;
  margin-left: 2px;
  margin-right: 2px;
  background-color: white;
}

#container .dt-paging button.current, 
#containerle .dt-paging button:hover, 
#container .dt-buttons button:hover{
  background-color: #010c41;
  color: white;
  border: #010c41;
  scale: 1.2;
}



#table svg {
    height: 25px;
    margin: 0 auto;
    fill: white;
    padding: 2px;
}


@media (max-width: 900px){
  #table .dt-container{
  display: block;
}

#table .dt-container div{
  margin: 10px auto;
}

#contenedor #banner {
  display: none;
}
}
</style>