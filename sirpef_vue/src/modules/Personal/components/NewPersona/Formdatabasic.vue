<script setup lang="ts">
import Controls from '@/components/sirpef/form/Controls.vue';

const props = defineProps<{
    emitForm: (event: Event) => void,
    step: number,
    values: any,
}>()

</script>


<template>
    <form @submit.prevent="emitForm">
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 mt-3">
        <input
          name="nombre_completo"
          class="w-full bg-gray-100 text-gray-900 mt-2 p-3 rounded-lg"
          type="text"
          placeholder="Nombre Completo*"
          v-model="values.nombre_completo"
          required
        />

        <input
          name="fecha_nacimiento"
          class="w-full bg-gray-100 text-gray-900 mt-2 p-3 rounded-lg"
          type="date"
          placeholder="Nombre Completo*"
          v-model="values.fecha_nacimiento"
          required
        />

        <input
          name="cedula"
          class="w-full bg-gray-100 text-gray-900 mt-2 p-3 rounded-lg"
          maxlength="8"
          type="text"
          placeholder="Cédula*"
          v-model="values.cedula"
          required
        />
        <input
          name="telefono"
          class="w-full bg-gray-100 text-gray-900 mt-2 p-3 rounded-lg"
          type="text"
          maxlength="11"
          placeholder="Teléfono*"
          v-model="values.telefono"
          required
        />

        <div class="col-span-2">
                <label class="block">Tipo de Jubilado</label>
                <select name="tipo_empleado_id" v-model="values.tipo_empleado_id" required>
                    <option :value="''">Sin selección</option>
                    <option :value="1">JUBILADO EMPLEADO</option>
                    <option :value="2">JUBILADO OBRERO</option>
                    <option :value="3">PENSIONADO DISCAPACIDAD EMPLEADO</option>
                    <option :value="4">PENSIONADO DISCAPACIDAD OBRERO</option>
                    <option :value="5">PENSIONADO SOBREVIVIENTE EMPLEADO</option>
                    <option :value="6">PENSIONADO SOBREVIVIENTE OBRERO</option>
                    <option :value="7">EMPLEADO</option>
                </select>
            </div>

        <div class="col-span-full">
          <textarea
            name="direccion"
            placeholder="Dirección*"
            v-model="values.direccion"
            class="w-full h-24 bg-gray-100 text-gray-900 p-3 rounded-lg resize-none"
          ></textarea>
        </div>
      </div>
        <Controls :step="step"/>
    </form>
</template>