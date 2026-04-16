<script lang="ts" setup>

const props = defineProps<{
    action: () => void,
    text: string,
    icon: string,
}>()

</script>

<template>
       <button @click="action" class="block bg-white rounded-2xl text-gray-500 font-bold hover:scale-110 text-sm px-8 py-4 text-center capitalize transform transition duration-200 ease" :title="text">
          <font-awesome-icon :icon="icon" class="block mx-auto text-[#4E85C8] h-[60px]" />
          <p class="block text-base mt-2">{{text}}</p>
      </button>
</template>