<script lang="ts" setup>
import { onMounted, ref } from 'vue';
import { getHistorialCaso } from '../services';
import convertDateISO from '@/utils/convertDateISO';

const data = ref([])

const $emit = defineEmits(['close'])

const props = defineProps<{
    idRegister: string
}>()

const getHistory = async () => {
    try {
        const response = await getHistorialCaso(props.idRegister)
        data.value = response.data
    } catch (error) {
        $emit('close')
    }
}

onMounted(() => {
    getHistory()
})

</script>


<template>
    <section class="!fixed !inset-0 bg-black bg-opacity-50 flex items-center justify-center h-screen z-[500]"
        @click="$emit('close')">
        <div class="table-data__wrapper relative md:w-[60%]" @click.stop>
            <label class="absolute top-3 right-5 text-white" @click="$emit('close')">X</label>
            <table class="table-data">
                <thead>
                    <tr class="">
                        <th>Estatus</th>
                        <th>Observación</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in data" :key="row.id" class="text-center">
                        <td class="">
                            {{ row.estatus_tramite ? row.estatus_tramite.nombre_estatus : 'Sin info' }}
                        </td>
                        <td class="">
                            {{ row.observacion ? row.observacion : 'Sin info' }}
                        </td>
                        <td class="">
                            {{ convertDateISO(row.created_at) }}
                        </td>
                    </tr>
                    <tr v-if="data.length == 0">
                        <td class="" colspan="4">Data no encontrado.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>