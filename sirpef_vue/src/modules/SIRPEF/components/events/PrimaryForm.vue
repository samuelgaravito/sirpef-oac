<script setup lang="ts">
import { getDay } from '@/utils/GetDay';
import Controls from '../../../../components/sirpef/form/Controls.vue';

const props = defineProps<{
    FormEvent: (event: Event) => void,
    step: number,
    values: any
}>()


</script>

<template>
    <form @submit.prevent="FormEvent" class="grid grid-cols-1 mt-5">
        <label class="block my-2">Título</label>
        <input class="inputForm" type="text" name="titulo" placeholder="Ingrese el titulo" :value="values.titulo"
            required>
        <label class="block my-2">Descripción</label>
        <input class="inputForm" type="text" name="descripcion" placeholder="Ingrese el descripcion" :value="values.descripcion" required>

        <div class="lg:grid-cols-3 lg:grid my-5 gap-x-10 w-[90%] justify-center mx-auto">
            <div>
                <label class="block my-4">Fecha de inicio</label>
                <input class="inputForm" type="date" name="fecha_inicio" :min="getDay()" :value="values.fecha_inicio" required>
            </div>
            <div>
                <label class="block my-4">Fecha de Fin</label>
                <input class="inputForm" type="date" name="fecha_fin" :min="getDay()" :value="values.fecha_fin" required>
            </div>
            <div>
                <label class="block my-4">Cortesia</label>
                <input class="inputForm text-center" type="number" name="cortesia" :min="0" :value="values.cortesia" required>
            </div>
        </div>

        <Controls :step="step" />
    </form>
</template>

<style scoped>
label {
    color: rgb(65, 65, 65);
    text-transform: capitalize;
}
</style>
