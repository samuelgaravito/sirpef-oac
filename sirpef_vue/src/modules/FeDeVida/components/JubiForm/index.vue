<script setup lang="ts">
import "@/assets/css/formWizard.scss";
import Check from "@/components/sirpef/Check.vue";
import Formdatabasic from "./Formdatabasic.vue";
import FormLoadReacudos from "./FormLoadReacudos.vue";
import useFormRecaudos from "../../composables/useFormRecaudos";
import { onMounted } from "vue";
import { defineEmits } from 'vue';
import FormdataAdicional from "./FormdataAdicional.vue";


const props = defineProps<{
  values: any,
  where: string,
  enableRecaudos: boolean
}>()

const emits = defineEmits<{
    (e: 'hide'): void;
}>()

const {
    step,
    estado,
    emitForm,
    dataForm,
    setInfo,
    loading
} = useFormRecaudos(emits)


const stepperProgress = () => {
  return (100 / 3) * (step.value - 1) + "%";
};

onMounted(() => {
  setInfo(props.values, props.where)
})

</script>

<template>

<div class="wrapper-stepper w-full md:w-auto cursor-default fadeout">
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
        v-for="item in (enableRecaudos ? 3 : 2)"
        :key="item"
      >
        <button class="stepper-item-counter" @click="() => step = item">
          <img
            class="icon-success"
            src="/status/check.png"
            alt=""
            v-if="estado.find(e => e == item)"
          />

          <img
            class="icon-success"
            src="/status/mark.webp"
            alt=""
            v-else
          />
          <span class="number">
            {{ item }}
          </span>
        </button>
        <span class="stepper-item-title">
          {{
            item == 1
              ? "Datos del autorizado"
              : item == 2
                ? "Adicional"
                : 'Final'
          }}
        </span>
      </div>
    </div>

    <div class="stepper-content">

      <Formdatabasic :emitForm="emitForm" :values="dataForm" :step="step" v-if="step == 1"/>
      <FormdataAdicional :emitForm="emitForm" :values="dataForm" :step="step" v-else-if="step == 2"/>
      <FormLoadReacudos :emitForm="emitForm" :values="dataForm" :step="step" :loading="loading" v-else-if="enableRecaudos && step == 3"/>

      <div class="grid gap-5 mt-10 w-full md:w-full" v-else>

        <article class="w-full text-center mt-5" v-if="estado.find(e => e == 4)">
          <Check :animate="true"/>
          <h2 class="capitalize">registro completado</h2>
        </article>

        <div class="flex justify-between items-center gap-2 md:w-[60%] mx-auto mt-10">
          <p class="text-black text-base">Se ha completado los datos basicos</p>
          <font-awesome-icon icon="fa-solid fa-circle-check" class="text-green-700 h-[20px]" v-if="estado.find(e => e == 1)"/>
          <font-awesome-icon icon="fa-solid fa-circle-xmark" class="text-red-700 h-[20px]" v-else/>
        </div>

        <div class="flex justify-between items-center gap-2 md:w-[60%] mx-auto">
          <p class="text-black text-base">Se ha añadido las oficinas</p>
          <font-awesome-icon icon="fa-solid fa-circle-check" class="text-green-700 h-[20px]" v-if="estado.find(e => e == 2)"/>
          <font-awesome-icon icon="fa-solid fa-circle-xmark" class="text-red-700 h-[20px]"  v-else/>
        </div>

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
</style>

<style>

.inputForm{
    border: rgba(255, 255, 255, 0);
    border-bottom: solid 2px;
    outline: none;
}

.inputForm:focus{
    border-bottom: solid #041e42;
}



</style>
