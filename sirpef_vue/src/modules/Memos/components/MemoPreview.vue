<template>
  <div class="memo-paper bg-white shadow-2xl mx-auto p-12 text-gray-800 text-sm font-serif leading-tight w-[21cm] min-h-[29.7cm]">
    <!-- Header Image -->
    <div v-if="data.headerImg" class="mb-6">
      <img :src="data.headerImg" class="w-full h-auto" />
    </div>

    <!-- Metadata -->
    <div class="flex justify-between mb-8">
      <div class="font-bold">{{ data.codigo }}</div>
      <div>Caracas, {{ data.fecha }}</div>
    </div>

    <div class="text-center font-bold text-lg underline mb-6">MEMORÁNDUM</div>

    <!-- Receiver/Sender -->
    <div class="space-y-4 mb-8">
      <div class="flex">
        <span class="font-bold w-24">PARA:</span>
        <div class="flex-1 whitespace-pre-line">{{ data.para }}</div>
      </div>
      <div class="flex">
        <span class="font-bold w-24">DE:</span>
        <div class="flex-1 whitespace-pre-line">{{ data.de }}</div>
      </div>
      <div class="flex">
        <span class="font-bold w-24">ASUNTO:</span>
        <div class="flex-1 font-bold uppercase">{{ data.asunto }}</div>
      </div>
    </div>

    <hr class="border-t-2 border-black mb-6" />

    <!-- Body -->
    <div class="text-justify mb-12 whitespace-pre-line">
      {{ data.cuerpo }}
    </div>

    <!-- Signature -->
    <div class="mt-20 text-center uppercase">
      <div class="mb-2">Atentamente,</div>
      <div class="font-bold mt-12">{{ data.de.split('\n')[0] }}</div>
      <div class="text-xs">{{ data.de.split('\n')[1] }}</div>
    </div>

    <!-- Footer Image -->
    <div v-if="data.footerImg" class="mt-auto pt-10">
      <img :src="data.footerImg" class="w-full h-auto" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  data: Object
});
</script>

<style scoped>
.memo-paper {
  aspect-ratio: 1 / 1.414;
}
@media print {
  .memo-paper {
    box-shadow: none;
    margin: 0;
    width: 100%;
  }
}
</style>
