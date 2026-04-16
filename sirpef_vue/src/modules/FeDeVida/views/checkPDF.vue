<script lang="ts" setup>
import { useRoute, useRouter } from 'vue-router';
import { generatePDF } from '../utils/generatePDF';
import { onMounted } from 'vue';


const route = useRoute()
const cedula = route.params.cedula as string

onMounted(() => {
    generatePDF(cedula)
})

</script>

<template>
</template>