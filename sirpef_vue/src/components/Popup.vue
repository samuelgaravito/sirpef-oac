<script setup lang="ts">

const props = defineProps<{
    children: any
}>()

</script>


<template>
    <section>
        {{ props.children }}
    </section>
</template>

<style scoped>
    section{
        position: absolute;
        background-color: rgba(0, 0, 0, 0.336);
        width: 100%;
        height: 100%;
        z-index: 200;
        display: grid;
        place-content: center;
        top: 0;
        left: 0;
 }
</style>