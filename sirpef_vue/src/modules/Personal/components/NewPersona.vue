<script setup lang="ts">
import useformPersona from "@/composables/votos/useformPersona";
import { ref } from "vue";
import "@/assets/css/formWizard.scss";
import CardInfoUser from "@/components/Votos/CardInfoUser.vue"

const step = ref(1 as any);
const pharse = ref("");

const {
  Estados,
  municipios,
  Parroquia,
  Centros,
  SendData,
  Info_user,
  info_geo,
  info_geo_ids,
  changueValue,
  saveValue,
  Unidades
} = useformPersona();

const stepperProgress = () => {
  return (100 / 3) * (step - 1) + "%";
};

const checkform = (page: Number) => {
    pharse.value = ""
    if (step.value == 1) {
        if (Info_user.value.cedula == "" || Info_user.value.nombre_completo == "" ||
        Info_user.value.telefono == "" || Info_user.value.direccion == ""
        ) return pharse.value = "Complete todos los campos"

        setTimeout(() => {
            pharse.value = ""
        }, 500);
    } else if (page > step.value && step.value == 2 ) {
        if (info_geo.value.estado == "" || info_geo.value.municipio == "" || 
          info_geo.value.centro == "" ||  info_geo.value.parroquia == "" || 
          Info_user.value.unidad_Adscrita == "") return pharse.value = "Complete todos los campos" 

        setTimeout(() => {
            pharse.value = ""
        }, 500);
    }

    step.value = page
};


</script>

<template>
  <div class="wrapper-stepper">
    <div class="stepper">
      <div class="stepper-progress">
        <div
          class="stepper-progress-bar"
          :style="'width:' + stepperProgress"
        ></div>
      </div>

      <div
        class="stepper-item"
        :class="{ current: step == item, success: step > item }"
        v-for="item in 3"
        :key="item"
      >
        <button class="stepper-item-counter" @click="() => checkform(item)">
          <img
            class="icon-success"
            src="https://www.seekpng.com/png/full/1-10353_check-mark-green-png-green-check-mark-svg.png"
            alt=""
          />
          <span class="number">
            {{ item }}
          </span>
        </button>
        <span class="stepper-item-title">
          {{
            item == 1
              ? "Datos personales"
              : item == 2
              ? "Dirección"
              : "Registro"
          }}
        </span>
      </div>
    </div>

    <div class="stepper-content">
      <div class="grid grid-cols-1 gap-5 md:grid-cols-2 mt-5" v-if="step == 1">
        <input
          name="nombre_completo"
          class="w-full bg-gray-100 text-gray-900 mt-2 p-3 rounded-lg col-span-full"
          type="text"
          placeholder="Nombre Completo*"
          @change="(e) => saveValue(e as InputEvent, step)"
          :value="Info_user.nombre_completo"
          required
        />
        <input
          name="cedula"
          class="w-full bg-gray-100 text-gray-900 mt-2 p-3 rounded-lg"
          maxlength="8"
          type="text"
          placeholder="Cédula*"
          :value="Info_user.cedula"
          @change="(e) => saveValue(e as InputEvent, step)"
          required
        />
        <input
          name="telefono"
          class="w-full bg-gray-100 text-gray-900 mt-2 p-3 rounded-lg"
          type="text"
          maxlength="11"
          placeholder="Teléfono*"
          :value="Info_user.telefono"
          @change="(e) => saveValue(e as InputEvent, step)"
          required
        />

        <div class="my-4 col-span-full">
          <textarea
            name="direccion"
            placeholder="Dirección*"
            :value="Info_user.direccion"
            @change="(e) => saveValue(e as InputEvent, step)"
            class="w-full h-24 bg-gray-100 text-gray-900 mt-2 p-3 rounded-lg resize-none"
          ></textarea>
        </div>
      </div>

      <div
        class="grid grid-cols-1 gap-5 md:grid-cols-2 mt-5"
        v-else-if="step == 2"
      >
        <div class="relative z-0 w-full mb-5 group">
          <select
            :value="info_geo_ids.estado_id"
            name="estado"
            required
            @change="changueValue"
            class="border text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
          >
            <option value="0">Seleccione un estado</option>
            <option v-for="element in Estados" :value="element.id">
              {{ element.estado }}
            </option>
          </select>
        </div>
        <div class="relative z-0 w-full mb-5 group">
          <select
            :value="info_geo_ids.municipio_id"
            name="municipio"
            required
            @change="changueValue"
            class="border text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
          >
            <option value="0">Seleccione un municipio</option>
            <option v-for="element in municipios" :value="element.id">
              {{ element.municipio }}
            </option>
          </select>
        </div>
        <div class="relative z-0 w-full mb-5 group">
          <select
            :value="info_geo_ids.parroquia_id"
            name="parroquia"
            required
            @change="changueValue"
            class="border text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
          >
            <option value="0">Seleccione una parroquia</option>
            <option v-for="element in Parroquia" :value="element.id">
              {{ element.parroquias }}
            </option>
          </select>
        </div>

        <div class="relative z-0 w-full mb-5 group">
          <select
            :value="info_geo_ids.centro_id"
            name="centro"
            required
            @change="changueValue"
            class="border text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
          >
            <option value="0">Seleccione una centro</option>
            <option v-for="element in Centros" :value="element.id">
              {{ element.nombre }}
            </option>
          </select>
        </div>


        <div class="relative z-0 w-full mb-5 group">
          <select
            :value="info_geo_ids.unidad_id"
            name="unidad"
            required
            @change="changueValue"
            class="border text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
          >
            <option value="0">Seleccione una unidad adscrita</option>
            <option v-for="element in Unidades" :value="element.id">
              {{ element.nombre }}
            </option>
          </select>
        </div>

      </div>

      <div class="results my-10 w-full md:w-3/4" v-else>
        <CardInfoUser
          :UserData="Info_user"
          title="Datos personales"
          icon="fa-solid fa-user"
        />
        <CardInfoUser
          :UserData="info_geo"
          title="Ubicacion"
          icon="fa-solid fa-location-dot"
        />

      </div>
        <p class="text-red-600 my-2 text-center capitalize">{{ pharse }}</p>
      <div class="controls">
        <button class="btn" @click="() => checkform(step - 1)" :disabled="step == 1">
          Anterior
        </button>
        <button class="btn btn--green-1" @click="() => checkform(step + 1)" v-if="step <= 2">
          Siguiente
        </button>
        <button class="btn btn--green-1" @click="SendData" v-else>
          Registrar
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
input,
select,
textarea {
  border-color: #eca008;
  border-radius: 10px;
}

.results {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
  gap: 20px;
  margin: 30px auto;
}
</style>
