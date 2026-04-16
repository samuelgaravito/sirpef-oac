<script setup lang="ts">
import useReportes from '@/modules/SIRPEF/composables/useOneVote';
import CardInfoUser from '@/components/Votos/CardInfoUser.vue';
import FormInput from '@/modules/SIRPEF/components/FormInput.vue';
import {obtenerHoraActual} from "@/utils/GetHora"
import Banner from '@/components/Votos/Banner.vue';
import Welcome from '@/components/sirpef/welcome.vue';
import { useRoute } from 'vue-router';
import { onMounted } from 'vue';

const { GetUser, result, hidden, popupStatus, popup, envioData, Loading} = useReportes()

const route = useRoute()


onMounted(() => {
  if(route.params.ci) GetUser(route.params.ci as string)
})

</script>

<template>
  <Welcome
    title="Registro individual"
    subtitle="Ingrese la cédula del personal para llevar a cabo el registro"
  >
  <FormInput :FunGetUser="GetUser"/>
</Welcome>

<div id="contenedor">
  <Banner v-if="Object.keys(result).length == 0" text="Ingrese el número de cédula que desea registrar su participación en el sistema"/>
  <main id="reportes">
    <section id="form">
      <section class="fixed bg-black bg-opacity-30 w-full h-full z-50 hidden place-content-center top-0 left-0 cursor-pointer" @click="(e) => hidden(e as PointerEvent)" ref="popup">
        <img class="h-90 bg-white rounded-lg fadeout" src="@/assets/onevote.jpeg" alt="" v-if="popupStatus == -1">
        
        <form @submit.prevent="envioData" class="bg-white dark:bg-slate-800 rounded-lg px-6 py-8 ring-1 ring-slate-900/5 shadow-xl w-96 fadeout" v-else-if="popupStatus >= 1">
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
    </section>
    <section class="results my-10 w-full md:w-3/4" v-if="Object.keys(result).length > 0">
      <CardInfoUser :UserData="result[0]" title="Datos personales" icon="fa-solid fa-user" />
      <CardInfoUser :UserData="result[1]" title="Ubicacion" icon="fa-solid fa-location-dot" />
      <CardInfoUser :UserRegister="result[2]" title="Registro" icon="fa-solid fa-check-to-slot" :hidden="hidden" :save="envioData"/>
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

@media (max-width: 900px){

#contenedor #banner {
  display: none;
}
}
</style>
