<script setup lang="ts">
import Welcome from "@/components/sirpef/welcome.vue";
import JubiladoForm from '@/modules/FeDeVida/components/JubiForm/index.vue'
import FormInput from "@/modules/SIRPEF/components/FormInput.vue";
import { alerta } from "@/utils/alert";
import { ref } from "vue";
import { getJubilado } from "../services";
import { generatePDF } from "../utils/generatePDF";


const dataUser = ref({})
const seePDF = ref(false)
const cedulaToPDF = ref('')
const popup = ref(1)

const getAuth = async (cedula: string) => {
  dataUser.value = {}
  seePDF.value = false
  cedulaToPDF.value = ''
  try {
    const response = await getJubilado(cedula)
    if (Object.keys(response).length == 0) return

    if (response.status) {
      seePDF.value = true
      cedulaToPDF.value = cedula
    }
    else if (response.status == false) dataUser.value = response.data[0]

    if (response.msg.length > 0) return alerta('Info', response.msg, 'info')
  } catch (error) {
    alerta('info', error.response.data.msg || 'No se encontro la persona', 'info')
  }
}


const hidden = (e: PointerEvent) => {
        const target = e.target as HTMLElement
        if (e && target.tagName === "SECTION") {
            popup.value = 0
        }
    }

</script>

<template>
  <Welcome title="Fe de vida" subtitle="Registro de actualización Fe de vida para el personal jubilado o pensionado">
    <FormInput :FunGetUser="getAuth" :finger="false" :cort="false" @setCortesia="null" />
  </Welcome>

  <section v-if="popup == 1" class="fixed bg-black bg-opacity-30 w-full h-full z-50 place-items-center grid top-0 left-0 cursor-pointer" @click="(e) => hidden(e as PointerEvent)">
      <img src="/popupFe.png" class="w-full md:w-[40%]"/>
  </section>

  <div v-if="seePDF" class="bg-white grid place-items-center h-[25vh] mx-auto rounded-3xl md:w-[30%] md:mt-[-9vh] p-6 shadow-lg slide-in-left">
        <h3 class="text-xl font-semibold text-gray-800 mb-2">¡Tu solicitud ha sido aprobada por el Ministerio!</h3>
        <p class="text-gray-600 mb-4">Visita tu comprobante en el siguiente enlace</p>
        <button @click="() => generatePDF(cedulaToPDF)" class="bg-[#1F52C7] text-white px-4 py-2 rounded-xl mx-auto block hover:bg-red-600 transition duration-300" title="Descargar planilla">
            <p>Descarga tu comprobante aquí</p>
        </button>
    </div>

  <div v-if="seePDF == false && Object.keys(dataUser).length > 0"
    class="grid md:grid-cols-2 items-center gap-y-8 bg-white w-full md:max-w-[80%] mx-auto rounded-3xl md:mt-[-9vh] md:mb-[10vh] overflow-hidden">
    <div
      class="max-md:order-1 flex flex-col justify-center sm:p-14 p-4 bg-gradient-to-r from-[#0A274D] to-[#1a529b] w-full h-full slide-in-left">
      <div class="max-w-md space-y-12 mx-auto">
        <div>
          <div class="flex gap-4 items-center text-white hover:text-[#ECA008]">
            <font-awesome-icon icon="camera" class="scale-[1.5]" />
            <h4 class="text-[22px] font-semibold">Requisitos de la Foto</h4>
          </div>

          <p class="text-[18px] text-white mt-2">El solicitante debe suministrar una foto de su cédula, foto tipo carnet
            y una foto con su documento de identidad.
            La imagen debe ser clara, legible y en color, mostrando a la persona sosteniendo su documento de identidad.
            NO SE DEBEN HACER MONTAJES.</p>
        </div>
        <div>

          <div class="flex gap-4 items-center text-white hover:text-[#ECA008]">
            <font-awesome-icon icon="fa-solid fa-clipboard-check" class="scale-[1.5]" />
            <h4 class="text-[22px] font-semibold">Proceso de Validación</h4>
          </div>

          <p class="text-[18px] text-white mt-2">
            Luego de enviar los datos, el solicitante debe consultar el estatus del trámite: Aprobado, Rechazado o en
            observación.
            En caso de rechazo, debe repetir el proceso. Y en caso de aprobación debe descargar su comprobante</p>
        </div>
        <div>
          <div class="flex gap-4 items-center text-white hover:text-[#ECA008]">
            <font-awesome-icon icon="address-card" class="scale-[1.5]" />
            <h4 class="text-[22px] font-semibold">Reenvío de Registro</h4>
          </div>
          <p class="text-[18px] text-white mt-2">Si la foto no cumple con las condiciones mencionadas, el proceso será
            rechazado y el jubilado o pensionado debe realizar nuevamente el registro con una foto mejorada.</p>
        </div>
      </div>
    </div>

    <JubiladoForm :values="dataUser" :enableRecaudos="true" where="Formulario" @hide="() => dataUser = {}" />
  </div>


</template>
