<template>
  <div class="memo-paper bg-white shadow-2xl mx-auto p-12 text-gray-800 text-sm font-serif leading-tight w-[21cm] min-h-[29.7cm]">
    <!-- Header Image -->
    <div v-if="data.headerImg" class="mb-6">
      <img :src="data.headerImg" class="w-full h-auto" />
    </div>

    <!-- Metadata -->
    <div class="flex justify-between mb-8">
      <div class="font-bold">OAC-M N°{{ data.tabla?.pto_cta }}</div>
      <div>Caracas, {{ data.fecha }}</div>
    </div>

    <div class="text-center font-bold text-lg mb-6">MEMORÁNDUM</div>

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
        <div class="flex-1 font-bold">Remisión de Punto de Cuenta N° {{ data.tabla?.pto_cta }}.</div>
      </div>
    </div>

    <div class="border-t-4 border-double border-black mb-6"></div>

    <!-- Body -->
    <div class="text-justify mb-6 whitespace-pre-line">
      Tengo a bien dirigirme a usted, en la oportunidad de remitir Punto de Cuenta N°{{ data.tabla?.pto_cta }} de fecha {{ data.tabla?.fecha }}, para sufragar monto para intervención quirúrgica (cesárea), debidamente aprobado, el cual se especifica a continuación:
    </div>

    <!-- Table -->
    <table class="w-full border-collapse border border-gray-400 mb-6 text-[11px]">
      <thead>
        <tr class="bg-[#d32f2f] text-white font-bold">
          <th class="border border-gray-400 p-1">Pto/Cta</th>
          <th class="border border-gray-400 p-1">Fecha</th>
          <th class="border border-gray-400 p-1">Solicitante</th>
          <th class="border border-gray-400 p-1">C.I.</th>
          <th class="border border-gray-400 p-1">Monto (Bs.)</th>
          <th class="border border-gray-400 p-1">Proveedor</th>
        </tr>
      </thead>
      <tbody>
        <tr class="h-10 text-center">
          <td class="border border-gray-400 p-1">{{ data.tabla?.pto_cta }}</td>
          <td class="border border-gray-400 p-1">{{ data.tabla?.fecha }}</td>
          <td class="border border-gray-400 p-1">{{ data.tabla?.solicitante }}</td>
          <td class="border border-gray-400 p-1">{{ data.tabla?.ci }}</td>
          <td class="border border-gray-400 p-1">{{ data.tabla?.monto }}</td>
          <td class="border border-gray-400 p-1">{{ data.tabla?.proveedor }}</td>
        </tr>
        <tr>
          <td colspan="4" class="border border-gray-400 p-1 text-center font-bold">TOTAL</td>
          <td colspan="2" class="border border-gray-400 p-1 text-center">{{ data.tabla?.total }}</td>
        </tr>
      </tbody>
    </table>

    <div class="text-justify mb-10 whitespace-pre-line">
      {{ data.cuerpo_final }}
    </div>

    <!-- Signature -->
    <div class="mt-8 text-center uppercase">
      <div class="mb-8">Atentamente,</div>
      <div class="font-bold">{{ data.de.split('\n')[0] }}</div>
      <div class="text-[10px] leading-tight max-w-lg mx-auto">{{ data.de.split('\n')[1] }}</div>
      <div v-if="data.resolucion" class="text-[9px] mt-1 leading-tight max-w-sm mx-auto text-gray-600 font-sans">
        {{ data.resolucion }}
      </div>
    </div>

    <div v-if="data.iniciales" class="mt-8 text-[10px] text-left">
      {{ data.iniciales }}
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
