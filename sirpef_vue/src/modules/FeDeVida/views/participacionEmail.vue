<script setup lang="ts">
import useRegister from '@/modules/FeDeVida/composables/useRegister';
import CardInfoUser from '@/components/Votos/CardInfoUser.vue';
import JubiladoForm from '@/modules/FeDeVida/components/JubiForm/index.vue'
import FormInput from '@/modules/SIRPEF/components/FormInput.vue';
import Welcome from '@/components/sirpef/welcome.vue';
import { useRoute } from 'vue-router';
import { onMounted } from 'vue';
import { generatePDF } from '../utils/generatePDF';



const { GetUser, result, hidden, popupStatus, popup} = useRegister()

const route = useRoute()


onMounted(() => {
  if(route.params.ci) GetUser(route.params.ci as string)
})

const hideAndLoad = async () => {
  popupStatus.value = 0
  const cedula = result.value[3][0].cedula
  await GetUser(cedula)
}

</script>

<template>
  <Welcome
    title="Participación Fe de vida (Correo)"
    subtitle="Ingrese la cédula del personal jubilado para llevar a cabo el registro"
  >
  <FormInput :FunGetUser="GetUser" :finger="false" :cort="false" @setCortesia="null"/>
</Welcome>

<div id="contenedor">
  <main id="reportes">
      <section class="fixed bg-black bg-opacity-30 grid w-full h-full z-50 place-content-center top-0 left-0 cursor-pointer" 
      v-if="popupStatus == 1" @click="(e) => hidden(e as PointerEvent)" ref="popup">
        <div class="md:w-[100%]">
        <JubiladoForm :values="result[3][0]" :enableRecaudos="false" @hide="hideAndLoad" where="correo"/>
        </div>
      </section>
    <section class="results my-10 w-full md:w-3/4" v-if="Object.keys(result).length > 0">
      <CardInfoUser :UserData="result[0]" title="Datos personales" icon="fa-solid fa-user" />
      <CardInfoUser :UserData="result[1]" title="Ubicación" icon="fa-solid fa-location-dot" />
      <CardInfoUser :UserRegister="result[2]" title="Registro" buttonText="Actualizar" icon="fa-solid fa-check-to-slot" :funPDF="() => generatePDF(result[3][0].cedula)" :envioData="() => popupStatus = 1"/>
    </section>

  </main>
</div>
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