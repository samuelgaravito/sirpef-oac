<script lang="ts" setup>
import { useRouter } from 'vue-router';
import CardDashboard from './cardDashboard.vue';


const props = defineProps<{
  items: any
}>()

const router = useRouter()

const redirect = (estatus_caso: any) => {

  estatus_caso = estatus_caso.replace(/\s*Casos\s*/gi, '')

  switch (estatus_caso) {
    case 'En Trámite':
      estatus_caso = 'En Tramite';
      break;
    case 'Orientados':
      estatus_caso = 'Orientado';
      break;
    case 'con Resultado Directo':
      estatus_caso = 'Resultado Directo';
      break;
    case 'Remitidos a Otro':
      estatus_caso = 'Remitido a Otro';
      break;
    case 'Cerrados':
      estatus_caso = 'Cerrado';
      break;
  }

  router.push({
    path: '/cases',
    query: {
      estatus_caso,
    }
  });
}

</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 px-4 w-full h-full mx-auto xl:grid-cols-5 sm:px-8">
    <CardDashboard :title="item.label" :value="item.data" :color="item.bg" v-for="item in items"
      @click="redirect(item.label)" />
  </div>
</template>