<template>
  <div class="space-y-4">
    <div>
      <label class="block text-xs font-bold text-gray-700 uppercase">Logo Encabezado</label>
      <input type="file" @change="handleImage($event, 'header')" class="mt-1 block w-full text-xs text-gray-500 file:mr-4 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700" />
    </div>

    <div class="grid grid-cols-1 gap-2">
      <input v-model="form.fecha" placeholder="Fecha (Caracas, 00/00/2026)" class="border p-2 rounded w-full text-sm" />
    </div>

    <div class="border p-3 rounded bg-gray-50">
      <label class="block text-xs font-bold mb-2">Tabla de Datos</label>
      <div class="grid grid-cols-2 gap-2">
        <input v-model="form.tabla.pto_cta" placeholder="Pto/Cta" class="border p-1 text-xs" />
        <input v-model="form.tabla.fecha" placeholder="Fecha" class="border p-1 text-xs" />
        <input v-model="form.tabla.solicitante" placeholder="Solicitante" class="border p-1 text-xs" />
        <input v-model="form.tabla.ci" placeholder="C.I." class="border p-1 text-xs" />
        <input v-model="form.tabla.monto" placeholder="Monto" class="border p-1 text-xs" />
        <input v-model="form.tabla.proveedor" placeholder="Proveedor" class="border p-1 text-xs" />
      </div>
      <input v-model="form.tabla.total" placeholder="TOTAL" class="border p-1 text-xs w-full mt-2" />
    </div>


    <div>
      <label class="block text-xs font-bold text-gray-700 uppercase">Logo Pie de Página</label>
      <input type="file" @change="handleImage($event, 'footer')" class="mt-1 block w-full text-xs text-gray-500 file:mr-4 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700" />
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  form: Object
});

const handleImage = (event, type) => {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      if (type === 'header') props.form.headerImg = e.target.result;
      if (type === 'footer') props.form.footerImg = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};
</script>
