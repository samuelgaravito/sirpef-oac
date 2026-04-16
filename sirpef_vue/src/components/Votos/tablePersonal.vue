<script setup lang="ts">
import Loader from '@/components/Votos/loader.vue';
import UseEmployees from '@/composables/useEmployees';
import {onMounted, onUnmounted} from "vue"
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
import Buttons from 'datatables.net-buttons';
import { useAuthStore } from '@/modules/Auth/stores';
import 'datatables.net-buttons/js/buttons.html5.mjs';
DataTable.use(DataTablesCore);
DataTable.use(Buttons);


const props = defineProps<{
  loadall: Function,
  employees : any,
}>()

const store = useAuthStore()

const {confirm} = UseEmployees()


function renderDeleteButton(data, type, row) {
  if (type === 'display' && row.voto != null) {
    return `
    <button class="bg-[#ECA008] bg-[#010c41] rounded-md fill-white pl-1 py-1 transition-all hover:bg-[#010c41] mx-3 btnTable" title="Cambiar participación" onclick='FuncModify(${row.id}, ${row.voto})'><svg class="scale-[.6]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M0 64C0 28.7 28.7 0 64 0H224V128c0 17.7 14.3 32 32 32H384V299.6l-94.7 94.7c-8.2 8.2-14 18.5-16.8 29.7l-15 60.1c-2.3 9.4-1.8 19 1.4 27.8H64c-35.3 0-64-28.7-64-64V64zm384 64H256V0L384 128zM549.8 235.7l14.4 14.4c15.6 15.6 15.6 40.9 0 56.6l-29.4 29.4-71-71 29.4-29.4c15.6-15.6 40.9-15.6 56.6 0zM311.9 417L441.1 287.8l71 71L382.9 487.9c-4.1 4.1-9.2 7-14.9 8.4l-60.1 15c-5.5 1.4-11.2-.2-15.2-4.2s-5.6-9.7-4.2-15.2l15-60.1c1.4-5.6 4.3-10.8 8.4-14.9z"/></svg></button>
    ${store.authUser.isAdmin 
      ? `<button class="border-[#010c41] border-[1px] rounded-md border-solid fill-[#010c41] px-1 py-1 transition-all hover:fill-[#ECA008] btnTable" title="Borrar participación" onclick='FuncDelete(${row.id})'><svg class="scale-[.6]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M135.2 17.7C140.6 6.8 151.7 0 163.8 0H284.2c12.1 0 23.2 6.8 28.6 17.7L320 32h96c17.7 0 32 14.3 32 32s-14.3 32-32 32H32C14.3 96 0 81.7 0 64S14.3 32 32 32h96l7.2-14.3zM32 128H416V448c0 35.3-28.7 64-64 64H96c-35.3 0-64-28.7-64-64V128zm96 64c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16z"/></svg></button>`
      : ""
    }
    `;
  }
  return '';
}

onMounted(() => {
  (document as any).FuncDelete = (id: number) => confirm(id, props.loadall, "No se podra recuperar", "D");
  (document as any).FuncModify = (id: number, voto: boolean) => confirm(id, props.loadall, `La participación de esta persona ha sido marcada como:\n<strong>${voto ? `"Si participó"` : `"No participó"`}</strong> desea cambiarla a <strong>${!voto ? `"Si participó"` : `"No participó"`}</strong>`, "C");
});

onUnmounted(() => {
  delete (document as any).FuncDelete;
  delete (document as any).FuncModify;
});


const columns = [
  { data: 'icon' },
  { data: 'nombre_completo', width:"30%" },
  { data: 'cedula',  width:"10%"},
  { data: 'gender',  width:"10%"},
  { data: 'telefono' },
  { data: 'cargo', width:"40%" },
  { data: 'Accion', render: renderDeleteButton,} // Agrega esta línea
];

const options = {
  language: {
    search: "Buscar",
    info: "Mostrando del _START_ a _END_ de _TOTAL_ registros",
    zeroRecords: "No se encuentran resultados",
  },
  dom: 'Bfrtip',
  buttons: [
                {
                    extend: 'csv',
                    text: 'Exportar a CSV',
                    filename: "mi_personal",                    
                }
            ]
}

</script>

<template>
  <div v-if="props.employees.length > 0" id="Table_Personal">
    <DataTable :columns="columns" :data="employees" :options="options" id="table">
    <thead>
      <tr>
        <th>Voto</th>
        <th>Nombre completo</th>
        <th>Cédula</th>
        <th>Género</th>
        <th>Teléfono</th>
        <th>Direccón</th>
        <th>Acción</th>
      </tr>
    </thead>
  </DataTable>
  </div>
  <Loader v-else/>
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

#Table_Personal .dt-container{
  display: grid;
  grid-template-columns: 1fr 1fr;
  padding-bottom: 20px;
  overflow: auto;
}

#Table_Personal .dt-container div:first-child {
  grid-column: 1/2;
}


#Table_Personal .dt-container .dt-search label{
    font-size: 18px;
    margin: 10px auto;
    display: block;
}

#Table_Personal .dt-container .dt-search input{
    border-radius: 20px;
}

#Table_Personal .dt-buttons button {
    background-color: #ECA008;
    color: white;
    width: 130px;
    height: 50px;
    border-radius: 20px;
    transition: ease .3s;
}


#Table_Personal .dt-container > div:nth-child(1) {
  grid-column: 2/-1;
  grid-row: 1/2;
  display: flex;
  align-items: flex-end;
  justify-content: right;
}

#Table_Personal .dt-container div.dt-info {
  grid-column: 1/-1;
}


#Table_Personal tr {
  background-color: white;
  color: black;
  height: 16px;
  padding: 10px;
}

#Table_Personal .toolAndSvg{
 position: relative;
}

#Table_Personal .toolAndSvg:hover > h1{
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

#Table_Personal td{
  padding: 10px;
}

#Table_Personal th {
  background-color: #010c41;
  font-weight: bold;
  color: white;
  margin-top: 20px;
  padding: 20px;
}

#Table_Personal svg {
    height: 30px;
    margin: 0 auto;
}

#Table_Personal svg.anulado {
    fill:red;
}

#Table_Personal svg.confirmado {
    fill:green;
}

#Table_Personal svg.invalido {
    fill:#ECA008;
}

#Table_Personal svg.male {
    fill:#02b2f8;
}

#Table_Personal svg.female {
    fill:#dd75f7;
}

#Table_Personal .dt-info{
  grid-column: 1/2;
  grid-row: 3/4;
}

#Table_Personal .dt-paging{
  grid-column: 2/-1;
  grid-row: 3/4;
  display: flex;
  justify-content: right;
}

#Table_Personal .dt-paging button{
  border: 1px #010c4160 solid;
  width: 40px;
  height: 40px;
  transition: .3s ease;
  margin-left: 2px;
  margin-right: 2px;
  background-color: white;
}

#Table_Personal .dt-paging button.current, #Table_Personal .dt-paging button:hover, #Table_Personal .dt-buttons button:hover{
  background-color: #010c41;
  color: white;
  border: #010c41;
  scale: 1.2;
}


@media (max-width: 900px){
  #Table_Personal .dt-container{
  display: block;
}

#Table_Personal .dt-container div{
  margin: 10px auto;
}
}
</style>


