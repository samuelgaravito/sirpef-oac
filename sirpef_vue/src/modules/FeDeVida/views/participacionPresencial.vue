<script setup lang="ts">
import Welcome from '@/components/sirpef/welcome.vue';
import FormPersona from '../components/NewPersona/index.vue';

</script>

<template>
  <Welcome
    title="Registro de casos"
    subtitle="Ingrese la información del personal para llevar a cabo el registro"
  >
  <FormPersona/>
</Welcome>
</template>

<style scoped>
#reportes{
  width: 100%;
  max-height: 90vh;
  margin:0 auto;
}

#busqueda input:hover~button {
  background-color: #0057b3;
}

.results {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
  gap: 20px;
  margin: 30px auto;
}

</style>