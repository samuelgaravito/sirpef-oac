<script setup lang="ts">
import Controls from '@/components/sirpef/form/Controls.vue';

const props = defineProps<{
    emitForm: (event: Event) => void,
    step: number,
    values: any,
}>()

const validateTelefono = () =>  {
    props.values.telefono = props.values.telefono.replace(/(?!^\+)\D/g, '');
}

const validateCedula = () =>  {
    props.values.cedula = props.values.cedula.replace(/\D/g, '');
}

</script>


<template>
    <form @submit.prevent="emitForm">

        <div class="lg:grid-cols-2 lg:grid mt-5 gap-x-10">
            <div>
                <label class="block my-2">Cédula</label>
                <input class="inputForm" type="text" name="cedula" @input="validateCedula" maxlength="8" placeholder="Ingrese la cédula" v-model="values.cedula" required>
            </div>
            <div>
                <label class="block my-2">Teléfono</label>
                <input class="inputForm" type="text" name="telefono" maxlength="100" placeholder="Ingrese su celular" @input="validateTelefono" v-model="values.telefono" required>
            </div>
        </div>

        <div class="grid-cols-1 lg:grid mb-5 gap-x-10">
            <div>
                <label class="block my-2">Nombre</label>
                <input class="inputForm" type="text" name="nombre_completo" placeholder="Ingrese el nombre" v-model="values.nombre_completo" disabled required>
            </div>

            <div>
                <label class="block my-2">Tipo de Jubilado</label>
                <select name="tipo_empleado" v-model="values.tipo_empleado_id">
                    <option :value="null">Sin selección</option>
                    <option :value="1">JUBILADO EMPLEADO</option>
                    <option :value="2">JUBILADO OBRERO</option>
                    <option :value="3">PENSIONADO DISCAPACIDAD EMPLEADO</option>
                    <option :value="4">PENSIONADO DISCAPACIDAD OBRERO</option>
                    <option :value="5">PENSIONADO SOBREVIVIENTE EMPLEADO</option>
                    <option :value="6">PENSIONADO SOBREVIVIENTE OBRERO</option>
                    <option :value="12">JUBILADO OBRERO - SOBREVIVIENTE OBRERO</option>
                    <option :value="11">JUBILADO OBRERO - SOBREVIVIENTE EMPLEADO</option>
                    <option :value="10">JUBILADO EMPLEADO - SOBREVIVIENTE EMPLEADO</option>
                    <option :value="9">JUBILADO EMPLEADO - SOBREVIVIENTE OBRERO</option>

                </select>
            </div>
        </div>

      <div v-if="values.tipo_empleado_id >= 3 && values.tipo_empleado_id <=4">
        <label class="block my-2">Tipo de discapacidad</label>
        <input type="text" name="causa_pension" class="mb-10" placeholder="tipo de discapacidad" v-model="values.causa_pension" required />
      </div>

      <div v-if="values.tipo_empleado_id >= 5 && values.tipo_empleado_id <=6">
        <label class="block my-2">Datos del causante de pensión</label>
        <textarea name="causa_pension" class="resize-none h-[100px] mb-10" placeholder="Cédula, parentesco, nombre y apellido del causante" v-model="values.causa_pension" required></textarea>
      </div>

      <div v-if="values.tipo_empleado_id >= 9 && values.tipo_empleado_id <=12">
        <label class="block my-2">Datos del causante de pensión</label>
        <textarea name="causa_pension" class="resize-none h-[100px] mb-10" placeholder="Cédula, parentesco, nombre y apellido del causante" v-model="values.causa_pension" required></textarea>
      </div>
      
        <Controls :step="step" />
    </form>
</template>