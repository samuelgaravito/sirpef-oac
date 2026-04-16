<script setup lang="ts">
import Swal from "sweetalert2";
import { onMounted, onUnmounted} from "vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
import Buttons from 'datatables.net-buttons';
import 'datatables.net-buttons/js/buttons.html5.mjs';
DataTable.use(DataTablesCore);
DataTable.use(Buttons);

const props = defineProps<{
    data: Array<any>,
    columnas: Array<any>,
    Delete: Function
}>()

function renderDeleteButton(data, type, row) {
  if (type === 'display') {
    return `<button class="bg-[#ECA008] rounded-lg text-white px-4 py-1 transition-all hover:bg-[#010c41] hover:px-5 btnTable" title="Borrar ente" onclick='FuncDelete(${row.id})'><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg></button>`;
  }
  return '';
}

onMounted(() => {
  (document as any).FuncDelete = (id: number) => {
    
    Swal.fire({
      title: "¿Seguro que quieres eliminarlo?",
      text:"No se podra recuperar la información",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Aceptar",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.isConfirmed) props.Delete(id);
  });
      
  }


});

onUnmounted(() => {
  delete (document as any).FuncDelete;
});


const columns: any = props.columnas.map(element => {
  return { data: element[1] }
})

columns.push({ data: 'Accion', render: renderDeleteButton,  width:"30%"},)

const options = {
  language: {
    search: "Buscar",
    info: "Mostrando del _START_ a _END_ de _TOTAL_ registros",
    zeroRecords: "No se encuentran resultados",
  },
}

</script>

<template>
    <section id="TableForAll">
        <DataTable :columns="columns" :data="data" :options="options" id="tabla">
        <thead>
            <tr>
                <th v-for="col in columnas">{{ col[0] }}</th>
                <th>Acción</th>
            </tr>
        </thead>
        </DataTable>
    </section>
</template>


<style scope>

#TableForAll .miInput {
  padding: 12px !important;
  border-radius: 20px !important;
}

#tabla {
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
  text-transform: capitalize;
}

#TableForAll .dt-container {
  padding-bottom: 20px;
  overflow: auto;
}

#TableForAll .dt-container>div:nth-child(1) {
  grid-column: 1 / -1;
  grid-row: 1 / 2;
  display: block;
  align-items: flex-end;
  justify-content: left;
}


#TableForAll .dt-length {
  display: none;
}

#TableForAll .dt-container .dt-search {
  display: block !important
}

#TableForAll .dt-container .dt-search label{
    font-size: 18px;
    margin: 10px auto;
    display: block;
}

#TableForAll .dt-container .dt-search input{
    border-radius: 20px;
    width: 50%;
}


#TableForAll tr {
  background-color: white;
  color: black;
  height: 16px;
  padding: 10px;
}

#TableForAll td {
  padding: 10px;
}

#TableForAll th {
  background-color: #010c41;
  font-weight: bold;
  color: white;
  margin-top: 20px;
  padding: 20px;
}

#TableForAll .dt-info {
  grid-column: 1/2;
  grid-row: 3/4;
}

#TableForAll .dt-paging {
  grid-column: 2/-1;
  grid-row: 3/4;
  display: flex;
  justify-content: right;
}

.dt-start > {

  border: solid red;
}

#TableForAll .dt-paging button {
  border: 1px #010c4160 solid;
  width: 40px;
  height: 40px;
  transition: .3s ease;
  margin-left: 2px;
  margin-right: 2px;
  background-color: white;
}

#TableForAll .dt-paging button.current,
#TableForAll .dt-paging button:hover,
#TableForAll .dt-buttons button:hover {
  background-color: #010c41;
  color: white;
  border: #010c41;
  scale: 1.2;
}

#TableForAll svg {
  height: 25px;
  margin: 0 auto;
  fill: white;
  padding: 2px;
}

#TableForAll .dt-info{
  grid-column: 1/2;
  grid-row: 3/4;
}

#TableForAll .dt-paging{
  grid-column: 2/-1;
  grid-row: 3/4;
  display: flex;
  justify-content: right;
}


#TableForAll .dt-paging button{
  border: 1px #010c4160 solid;
  width: 40px;
  height: 40px;
  transition: .3s ease;
  margin-left: 2px;
  margin-right: 2px;
  background-color: white;
}

#TableForAll .dt-paging button.current, 
#TableForAll .dt-paging button:hover, 
#TableForAll .dt-buttons button:hover{
  background-color: #010c41;
  color: white;
  border: #010c41;
  scale: 1.2;
}



@media (max-width: 900px) {
  #TableForAll .dt-container {
    display: block;
  }

  #TableForAll .dt-container div {
    margin: 10px auto;
  }

  #TableForAll .dt-container .dt-search input{
    border-radius: 20px;
    width: 100%;
}
}
</style>