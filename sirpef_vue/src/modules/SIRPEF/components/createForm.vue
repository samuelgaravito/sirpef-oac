<script setup lang="ts">
import "@/assets/css/formWizard.scss";
import PrimaryForm from "../components/events/PrimaryForm.vue";
import Welcome from "@/components/sirpef/welcome.vue";
import UseCreateEvents from "../composables/UseCreateEvents";
import SecondForm from "../../../components/sirpef/form/OficinasForm.vue";
import Check from "../../../components/sirpef/Check.vue";
import { onMounted, watch } from "vue";


const {
    emitForm,
    editFormLoad,
    step,
    EventToSend,
    estado
} = UseCreateEvents()



const stepperProgress = () => {
  return (100 / 3) * (step - 1) + "%";
};

const props = defineProps<{
  id_event_edit: number
}>()


watch(() => props.id_event_edit, (newId, oldId) => {
      if (newId !== 0) {
        editFormLoad(newId);
      }
    });

</script>

<template>






  <div class="wrapper-stepper ">
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
              ? "Datos basicos"
              : item == 2
                ? "Oficinas"
                : item == 3
                    ? "Final"
                    : "Estado"
          }}
        </span>
      </div>
    </div>

    <div class="stepper-content">
        <PrimaryForm :values="EventToSend" :FormEvent="emitForm" :step="step" v-if="step == 1"/>
        <SecondForm :values="EventToSend.oficinas_ids" :FormEvent="emitForm" :step="step" v-else-if="step == 2"/>
      <div class="grid gap-5 my-10 w-full md:w-full" v-else>

        <article class="w-full text-center mt-5" v-if="estado.find(e => e == 3)">
          <Check :animate="true"/>
          <h2 class="capitalize">registro completado</h2>
        </article>

        <div class="flex justify-between items-center gap-2 md:w-[60%] mx-auto mt-10">
          <p class="text-black text-base">Se ha completado los datos basicos</p>
          <font-awesome-icon icon="fa-solid fa-circle-check" class="text-green-700 h-[20px]" v-if="estado.find(e => e == 1)"/>
          <font-awesome-icon icon="fa-solid fa-circle-xmark" class="text-red-700 h-[20px]" v-else/>
        </div>

        <div class="flex justify-between items-center gap-2 md:w-[60%] mx-auto">
          <p class="text-black text-base">Se ha añadido las oficinas que participaran</p>
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
