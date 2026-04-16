<script lang="ts" setup>
import Loader from '@/components/Votos/loader.vue';


const props = defineProps<{
    step: number,
    loading?: boolean
}>()

</script>

<template>
    <div class="controls mt-2">
      
      <div class="mx-auto" v-if="loading">
        <Loader />
      </div>

        <button v-else class="block w-full bg-[#1F52C7] text-white py-2 rounded-2xl transition-all hover:bg-[#010C41] md:w-[50%] md:ml-[50%]">
          {{ step <= 2 ? 'Siguiente' : 'Registrar' }}
        </button>

      </div>
</template>