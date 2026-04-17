<template>
  <div class="space-y-4">
    <div>
      <label class="block text-xs font-bold text-gray-700 uppercase">Logo Encabezado</label>
      <input type="file" @change="handleImage($event, 'header')" class="mt-1 block w-full text-xs text-gray-500 file:mr-4 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700" />
    </div>

    <div class="space-y-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
      <h3 class="text-sm font-bold text-blue-800 border-b pb-1">DESTINATARIO Y REMITENTE</h3>
      
      <div class="space-y-3">
        <div class="p-2 bg-white rounded border">
          <label class="block text-[10px] font-bold text-gray-500 uppercase">PARA (Nombre):</label>
          <input v-model="form.para_nombre" type="text" class="w-full text-xs p-1 border-b focus:border-blue-500 outline-none" placeholder="Ej: TAVIANA ELAINE ALQUINZONES FERNÁNDEZ" />
          <label class="block text-[10px] font-bold text-gray-500 uppercase mt-2">PARA (Cargo):</label>
          <textarea v-model="form.para_cargo" class="w-full text-xs p-1 border-b focus:border-blue-500 outline-none resize-none" rows="2" placeholder="Ej: Directora General (E) de la Oficina..."></textarea>
        </div>

        <div class="p-2 bg-white rounded border">
          <label class="block text-[10px] font-bold text-gray-500 uppercase">DE (Nombre):</label>
          <input v-model="form.de_nombre" type="text" class="w-full text-xs p-1 border-b focus:border-blue-500 outline-none" placeholder="Ej: OLIVER EZEQUIEL RIVAS PAREDES" />
          <label class="block text-[10px] font-bold text-gray-500 uppercase mt-2">DE (Cargo):</label>
          <textarea v-model="form.de_cargo" class="w-full text-xs p-1 border-b focus:border-blue-500 outline-none resize-none" rows="2" placeholder="Ej: Director General (E) de la Oficina..."></textarea>
        </div>
      </div>
    </div>

    <div class="space-y-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
      <h3 class="text-sm font-bold text-blue-800 border-b pb-1">TABLA DE DATOS</h3>
      <div class="grid grid-cols-2 gap-3">
        <div class="flex flex-col">
          <label class="text-[10px] font-bold text-gray-500">N° PUNTO CUENTA</label>
          <input v-model="form.tabla.pto_cta" placeholder="000/2026" class="border p-2 rounded text-xs focus:ring-1 focus:ring-blue-400 outline-none" />
        </div>
        <div class="flex flex-col">
          <label class="text-[10px] font-bold text-gray-500">FECHA PUNTO CUENTA</label>
          <input v-model="form.tabla.fecha" type="date" class="border p-2 rounded text-xs focus:ring-1 focus:ring-blue-400 outline-none" />
        </div>
        <div class="flex flex-col">
          <label class="text-[10px] font-bold text-gray-500">SOLICITANTE</label>
          <input v-model="form.tabla.solicitante" class="border p-2 rounded text-xs focus:ring-1 focus:ring-blue-400 outline-none" />
        </div>
        <div class="flex flex-col">
          <label class="text-[10px] font-bold text-gray-500">CÉDULA</label>
          <input v-model="form.tabla.ci" class="border p-2 rounded text-xs focus:ring-1 focus:ring-blue-400 outline-none" />
        </div>
        <div class="flex flex-col">
          <label class="text-[10px] font-bold text-gray-500">MONTO (BS.)</label>
          <input v-model="form.tabla.monto" class="border p-2 rounded text-xs focus:ring-1 focus:ring-blue-400 outline-none" />
        </div>
        <div class="flex flex-col">
          <label class="text-[10px] font-bold text-gray-500">PROVEEDOR</label>
          <input v-model="form.tabla.proveedor" class="border p-2 rounded text-xs focus:ring-1 focus:ring-blue-400 outline-none" />
        </div>
      </div>
      <div class="flex flex-col mt-2">
        <label class="text-[10px] font-bold text-gray-500">MONTO TOTAL</label>
        <input v-model="form.tabla.total" class="border p-2 rounded text-xs font-bold bg-blue-50 focus:ring-1 focus:ring-blue-400 outline-none" />
      </div>
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
