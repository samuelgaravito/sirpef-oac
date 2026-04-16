<script setup lang="ts">
import Controls from '@/components/sirpef/form/Controls.vue';

const props = defineProps<{
    emitForm: (event: Event) => void,
    step: number,
    values: any,
    getAuth: () => void
}>()

</script>


<template>
    <form @submit.prevent="emitForm">

        <div class="lg:grid-cols-2 lg:grid my-5 gap-x-10">
            <div>
                <label class="block my-2">Cédula</label>
                <input class="inputForm" type="text" name="name" placeholder="Ingrese la cédula" v-model="values.cedula" @focusout="getAuth" required>
            </div>
            <div>
                <label class="block my-2">Estatus</label>
                <select name="estatus" v-model="values.estatus">
                    <option value="interno">Interno</option>
                    <option value="externo">Externo</option>
                </select>
            </div>
        </div>

        <div class="lg:grid-cols-2 lg:grid my-5 gap-x-10">
            <div>
                <label class="block my-2">Nombre</label>
                <input class="inputForm" type="text" name="email" placeholder="Ingrese el nombre" v-model="values.nombre_completo" required>
            </div>
            <div>
                <label class="block my-2">Teléfono</label>
                <input class="inputForm" type="text" name="cedula" placeholder="Ingrese la cedula" v-model="values.telefono" required>
            </div>
        </div>

        <label class="block my-2">Motivo</label>
        <textarea name="motivo" class="resize-none h-[100px] mb-10" placeholder="Ingrese el motivo" v-model="values.motivo" required></textarea>

        <Controls :step="step"/>
    </form>
</template>