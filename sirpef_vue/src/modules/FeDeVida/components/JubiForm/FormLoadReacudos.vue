<script lang="ts" setup>
import Controls from '@/components/sirpef/form/Controls.vue';


const props = defineProps<{
    emitForm: (event: Event) => void,
    step: number,
    values: any,
    loading: boolean
}>()

</script>

<template>
    <form @submit.prevent="emitForm">
        <div class="lg:grid-cols-2 lg:grid my-5 gap-x-10">
            <div class="font-[sans-serif] max-w-md mx-auto">
                <label class="text-base text-gray-500 font-semibold mb-2 block">Cédula del Jubilado</label>
                <input type="file" name="ceduJubilado" accept=".png, .jpg, .jpeg" required
                    class="w-full text-gray-400 font-semibold text-sm bg-white border file:cursor-pointer cursor-pointer file:border-0 file:py-3 file:px-4 file:mr-4 file:bg-gray-100 file:hover:bg-gray-200 file:text-gray-500 rounded" />
            </div>
            <div class="font-[sans-serif] max-w-md mx-auto">
                <label class="text-base text-gray-500 font-semibold mb-2 block">Foto Jubilado</label>
                <input type="file" name="jubilado" accept=".png, .jpg, .jpeg" required
                    class="w-full text-gray-400 font-semibold text-sm bg-white border file:cursor-pointer cursor-pointer file:border-0 file:py-3 file:px-4 file:mr-4 file:bg-gray-100 file:hover:bg-gray-200 file:text-gray-500 rounded" />
            </div>
        </div>
        <div class="font-[sans-serif] max-w-md mx-auto">
            <label class="text-base text-gray-500 font-semibold mb-2 block">Jubilado con Cédula</label>
            <input type="file" name="jubiAndCedu" accept=".png, .jpg, .jpeg" required
                class="w-full text-gray-400 font-semibold text-sm bg-white border file:cursor-pointer cursor-pointer file:border-0 file:py-3 file:px-4 file:mr-4 file:bg-gray-100 file:hover:bg-gray-200 file:text-gray-500 rounded" />
        </div>
        <p class="text-xs text-gray-400 mt-10 text-center">PNG o JPG son permitidos</p>
        <Controls :step="step" :loading="loading || false"/>
    </form>
</template>