<script setup lang="ts">
import { onMounted, ref} from "vue";
import Welcome from '@/components/sirpef/welcome.vue';
import Http from "@/utils/Http";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
import Buttons from 'datatables.net-buttons';
import 'datatables.net-buttons/js/buttons.html5.mjs';
DataTable.use(DataTablesCore);
DataTable.use(Buttons);

const registro = ref([]);

onMounted(() => {
    GetREgistro()
});


const columns = [
  { data: 'descripcion', width:"80%" },
  { data: 'Hora' },
  { data: 'fecha',  },
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
                    filename: "auditoria",                    
                }
            ]
}


const GetREgistro = async () => {
    const response = await Http.get("/api/auditorias")

    registro.value = response.data.auditorias.map(element => {

        const date = new Date(element.created_at);
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const seconds = String(date.getSeconds()).padStart(2, '0');
        return {
            descripcion: element.descripcion,
            fecha: element.created_at.split("T")[0],
            Hora: `${hours}:${minutes}:${seconds}`
        }
    })
    
}

</script>

<template>
  <Welcome
    title="Auditoria"
    subtitle="quí podras observar todos los movimientos dentro del sistema"
    img="Auditoria.png"
  />
     <main id="Tbl_Audi" class="w-3/4 mx-auto">
        <DataTable :columns="columns" :data="registro" :options="options" id="table">
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Hora</th>
                <th>Fecha</th>
            </tr>
        </thead>
        </DataTable>
     </main>
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

#Tbl_Audi .btnTable svg{
  fill: white;
  scale: .7;
}


#Tbl_Audi .dt-container{
  display: grid;
  grid-template-columns: 1fr 1fr;
  padding-bottom: 20px;
  overflow: auto;
}

#Tbl_Audi .dt-container div:first-child {
  grid-column: 1/2;
}


#Tbl_Audi .dt-container .dt-search label{
    font-size: 18px;
    margin: 10px auto;
    display: block;
}

#Tbl_Audi .dt-container .dt-search input{
    border-radius: 20px;
}

#Tbl_Audi .dt-buttons button {
    background-color: #ECA008;
    color: white;
    width: 130px;
    height: 50px;
    border-radius: 20px;
    transition: ease .3s;
}


#Tbl_Audi .dt-container > div:nth-child(1) {
  grid-column: 2/-1;
  grid-row: 1/2;
  display: flex;
  align-items: flex-end;
  justify-content: right;
}

#Tbl_Audi .dt-container div.dt-info {
  grid-column: 1/-1;
}


#Tbl_Audi tr {
  background-color: white;
  color: black;
  height: 16px;
  padding: 10px;
}

#Tbl_Audi .toolAndSvg{
 position: relative;
}

#Tbl_Audi .toolAndSvg:hover > h1{
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

#Tbl_Audi td{
  padding: 10px;
}

#Tbl_Audi th {
  background-color: #010c41;
  font-weight: bold;
  color: white;
  margin-top: 20px;
  padding: 20px;
}

#Tbl_Audi td:first-child {
  text-align: left;
  text-indent: 20px;
}

#Tbl_Audi svg {
    height: 30px;
    margin: 0 auto;
}

#Tbl_Audi svg.anulado {
    fill:red;
}

#Tbl_Audi svg.confirmado {
    fill:green;
}

#Tbl_Audi svg.invalido {
    fill:#ECA008;
}

#Tbl_Audi .dt-info{
  grid-column: 1/2;
  grid-row: 3/4;
}

#Tbl_Audi .dt-paging{
  grid-column: 2/-1;
  grid-row: 3/4;
  display: flex;
  justify-content: right;
}

#Tbl_Audi .dt-paging button{
  border: 1px #010c4160 solid;
  width: 40px;
  height: 40px;
  transition: .3s ease;
  margin-left: 2px;
  margin-right: 2px;
  background-color: white;
}

#Tbl_Audi .dt-paging button.current, #Tbl_Audi .dt-paging button:hover, #Tbl_Audi .dt-buttons button:hover{
  background-color: #010c41;
  color: white;
  border: #010c41;
  scale: 1.2;
}


@media (max-width: 900px){
  #Tbl_Audi .dt-container{
  display: block;
}

#Tbl_Audi .dt-container div{
  margin: 10px auto;
}
}
</style>


