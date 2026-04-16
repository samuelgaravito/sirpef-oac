<script setup lang="ts">
import CardLink from "@/components/sirpef/cardLink.vue"

const props = defineProps<{
    enlaces: any[],
}>()
</script>

<template>
    <main
        class="w-[80%] lg:w-[90%] h-auto md:h-[350px] block md:flex mt-0 mx-auto gap-10 items-center justify-center">
        <!--h1
            class="mb-4 text-4xl font-extrabold text-gray-900 md:text-5xl lg:text-5xl text-center row-span-full col-span-full">
            Selecciona una opción</h1>
-->
        <CardLink :enlaces="enlaces"/>
    </main>
</template>
