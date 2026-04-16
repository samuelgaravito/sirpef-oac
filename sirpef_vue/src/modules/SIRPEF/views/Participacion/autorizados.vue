<script setup lang="ts">
import useAuthParticipation from '@/modules/SIRPEF/composables/useAuthParticipation';
import {onMounted, onUnmounted} from "vue"
import FormInput from '@/modules/SIRPEF/components/FormInput.vue';
import {obtenerHoraActual} from "@/utils/GetHora"
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
import Welcome from '@/components/sirpef/welcome.vue';
import { useRoute } from 'vue-router';
DataTable.use(DataTablesCore);


const { personas, DeleteEmpleado, GetUser, Envio_Datos} = useAuthParticipation()


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
  { data: 'ministerio' },
  { data: 'telefono' },
  { data: 'estatus_evento' },
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
    title="Participación Autorizados"
    subtitle="Ingrese los números de cédulas de los autorizados que desea registrar en el sistema en el formulario"
  >
  <FormInput :FunGetUser="GetUser" :finger="false" :cort="false"/>
</Welcome>


 <div id="contenedor">
  <Banner v-if="personas.length == 0" text="Ingrese los números cédulas que desea registrar su participación en el sistema"/>
    <main id="container" class="xl:mt-10">
        <div v-if="personas.length > 0" class="mx-auto w-full md:w-3/4">
          
        <div class="overflow-x-auto overflow-y-hidden capitalize">
          <DataTable :columns="columns" :data="personas" :options="options" id="table">
        <thead>
            <tr>
                <th>Nombre completo</th>
                <th>Cédula</th>
                <th>Oficina</th>
                <th>Teléfono</th>
                <th>Estatus</th>
                <th>Acción</th>
            </tr>
        </thead>
        </DataTable>
        </div>
            <div class="w-full md:w-96 hg-30 block md:grid grid-cols-2 gap-1 mx-auto">
                <button name="si" class=" mx-auto bg-[#ECA008] hover:bg-[#010c41] block text-white font-bold py-2 px-10 rounded-3xl my-2 md:my-8 " @click="Envio_Datos">Si Participaron</button>
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