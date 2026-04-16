<script setup lang="ts">
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
import Buttons from 'datatables.net-buttons';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import { useRouter } from 'vue-router';
DataTable.use(DataTablesCore);
DataTable.use(Buttons);

const props = defineProps<{
  personas: any[],
  text: string
}>()

const router = useRouter();

const irAlCaso = (id: number | string) => {
  router.push({
    name: 'case-fedevida',
    params: { casePersona_id: id }
  });
};


const columns = [
  { data: 'punto_n' },
  { data: 'estatus_caso' },
  { data: 'nombre_completo' },
  { data: 'cedula' },
  { data: 'tipo_caso' },
  { data: 'descripcion' },
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
      filename: `${props.text}`,
    }
  ]
}

</script>

<template>
  <div id="Tbl_dash_popup" class="relative overflow-x-auto rounded-xl !text-center">
    <DataTable :columns="columns" :data="personas" :options="options" id="tablaDash">
      <thead class="text-xs text-white uppercase bg-[#010c41]">
        <tr>
          <th scope="col" class="px-6 py-3">
            Número de punto
          </th>
          <th scope="col" class="px-6 py-3">
            Estado
          </th>
          <th scope="col" class="px-6 py-3">
            Nombre Completo
          </th>
          <th scope="col" class="px-6 py-3">
            Cédula
          </th>
          <th scope="col" class="px-6 py-3">
            Tipo
          </th>
          <th scope="col" class="px-6 py-3">
            Descripción
          </th>
        </tr>
      </thead>

            <template #column-5="props">
        <button @click="irAlCaso(props.rowData.registro_id)"
          class="bg-blue-600 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm transition-colors cursor-pointer">
            <font-awesome-icon icon="file-lines" />
        </button>
      </template>

    </DataTable>
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



#Tbl_dash_popup .btnTable svg {
  fill: white;
  scale: .7;
}


#Tbl_dash_popup .dt-container {
  padding-bottom: 20px;
  overflow: auto;
}

#Tbl_dash_popup .dt-container div:first-child {
  grid-column: 1/2;
}

#Tbl_dash_popup .dt-container .dt-search {
  width: 20vw;
  margin-bottom: 30px;
}


#Tbl_dash_popup .dt-container .dt-search label {
  font-size: 18px;
  margin: 10px auto;
  display: block;
}

#Tbl_dash_popup .dt-container .dt-search input {
  border-radius: 20px;
}

#Tbl_dash_popup .dt-buttons button {
  background-color: #ECA008;
  color: white;
  width: 130px;
  height: 50px;
  border-radius: 20px;
  transition: ease .3s;
}


#Tbl_dash_popup .dt-container>div:nth-child(1) {
  position: absolute;
  right: 0;
  top: 4vh;
}

#Tbl_dash_popup .dt-container div.dt-info {
  grid-column: 1/-1;
}


#Tbl_dash_popup tr {
  background-color: white;
  color: black;
  height: 16px;
  padding: 10px;
}

#Tbl_dash_popup .toolAndSvg {
  position: relative;
}

#Tbl_dash_popup .toolAndSvg:hover>h1 {
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

#Tbl_dash_popup td {
  padding: 10px;
}

#Tbl_dash_popup th {
  background-color: #010c41;
  font-weight: bold;
  color: white;
  margin-top: 20px;
  padding: 20px;
}

#Tbl_dash_popup td:first-child {
  text-align: left;
  text-indent: 20px;
}

#Tbl_dash_popup .dt-paging {
  grid-column: 2/-1;
  grid-row: 3/4;
  display: flex;
  justify-content: right;
}

#Tbl_dash_popup .dt-paging button {
  border: 1px #010c4160 solid;
  width: 40px;
  height: 40px;
  transition: .3s ease;
  margin-left: 2px;
  margin-right: 2px;
  background-color: white;
}

#Tbl_dash_popup .dt-paging button.current,
#Tbl_dash_popup .dt-paging button:hover,
#Tbl_dash_popup .dt-buttons button:hover {
  background-color: #010c41;
  color: white;
  border: #010c41;
  scale: 1.2;
}


@media (max-width: 900px) {
  #Tbl_dash_popup .dt-container {
    display: block;
  }

  #Tbl_dash_popup .dt-container div {
    margin: 10px auto;
  }

  #Tbl_dash_popup .dt-container .dt-search {
    width: 65%;
    margin-bottom: 30px;
    margin-left: 0px;
  }
}
</style>
