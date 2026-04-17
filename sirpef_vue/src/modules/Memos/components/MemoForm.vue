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
          <label class="block text-[10px] font-bold text-gray-500 uppercase">PARA:</label>
          <select v-model="form.para_nombre" @change="updateCargo('para')" class="w-full text-xs p-1 border-b bg-transparent outline-none">
            <option v-for="opt in personasPara" :key="opt.nombre" :value="opt.nombre">{{ opt.nombre }}</option>
          </select>
          <div class="mt-2 p-1 bg-gray-50 text-[10px] text-gray-600 italic border rounded">
            {{ form.para_cargo }}
          </div>
        </div>

        <div class="p-2 bg-white rounded border">
          <label class="block text-[10px] font-bold text-gray-500 uppercase">DE:</label>
          <select v-model="form.de_nombre" @change="updateCargo('de')" class="w-full text-xs p-1 border-b bg-transparent outline-none">
            <option v-for="opt in personasDe" :key="opt.nombre" :value="opt.nombre">{{ opt.nombre }}</option>
          </select>
          <div class="mt-2 p-1 bg-gray-50 text-[10px] text-gray-600 italic border rounded">
            {{ form.de_cargo }}
          </div>
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

    <div class="space-y-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
      <h3 class="text-sm font-bold text-blue-800 border-b pb-1">CUERPO DEL MEMORÁNDUM</h3>
      <div class="space-y-2">
        <label class="block text-[10px] font-bold text-gray-500 uppercase">TEXTO ANTES DE DATOS:</label>
        <div class="text-[10px] text-blue-600 bg-blue-50 p-2 rounded border border-blue-100 mb-1">
          Nota: El N° de Punto de Cuenta y la Fecha se insertarán automáticamente.
        </div>
        <textarea v-model="form.cuerpo_prefix" class="w-full text-xs p-2 border rounded focus:ring-1 focus:ring-blue-400 outline-none resize-none" rows="3"></textarea>
      </div>
    </div>

    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
      <label class="block text-[10px] font-bold text-gray-500 uppercase">RESOLUCIÓN (Firma):</label>
      <textarea v-model="form.resolucion" class="w-full text-[10px] p-2 mt-1 border rounded focus:ring-1 focus:ring-blue-400 outline-none resize-none" rows="3"></textarea>
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

const personasPara = [
  { nombre: 'TAVIANA ELAINE ALQUINZONES FERNÁNDEZ', cargo: 'Directora General (E) de la Oficina de Gestión Administrativa' },
  { nombre: 'PERSONAL ADMINISTRATIVO 1', cargo: 'Cargo del personal 1' }
];

const personasDe = [
  { nombre: 'OLIVER EZEQUIEL RIVAS PAREDES', cargo: 'Director General (E) de la Oficina de Atención al Ciudadano' },
  { nombre: 'RESPONSABLE 2', cargo: 'Cargo del responsable 2' }
];

const updateCargo = (type) => {
  if (type === 'para') {
    const selected = personasPara.find(p => p.nombre === props.form.para_nombre);
    if (selected) props.form.para_cargo = selected.cargo;
  } else {
    const selected = personasDe.find(p => p.nombre === props.form.de_nombre);
    if (selected) props.form.de_cargo = selected.cargo;
  }
};

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
