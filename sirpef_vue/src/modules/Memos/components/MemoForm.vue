<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4">
    <div class="space-y-4 bg-white p-6 rounded shadow">
      <h2 class="text-xl font-bold mb-4">Crear Memorándum</h2>
      
      <div>
        <label class="block text-sm font-medium text-gray-700">Imagen de Encabezado</label>
        <input type="file" @change="handleImage($event, 'header')" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
      </div>

      <div class="grid grid-cols-2 gap-4">
        <input v-model="form.codigo" placeholder="N° OAC-M 000/2026" class="border p-2 rounded w-full" />
        <input v-model="form.fecha" type="date" class="border p-2 rounded w-full" />
      </div>

      <input v-model="form.para" placeholder="PARA: (Nombre y Cargo)" class="border p-2 rounded w-full" />
      <input v-model="form.de" placeholder="DE: (Nombre y Cargo)" class="border p-2 rounded w-full" />
      <input v-model="form.asunto" placeholder="ASUNTO:" class="border p-2 rounded w-full" />
      
      <textarea v-model="form.cuerpo" rows="4" placeholder="Contenido del mensaje..." class="border p-2 rounded w-full"></textarea>

      <div>
        <label class="block text-sm font-medium text-gray-700">Imagen de Pie de Página</label>
        <input type="file" @change="handleImage($event, 'footer')" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
      </div>
    </div>

    <div class="bg-gray-100 p-4 overflow-auto sticky top-4" style="max-height: 90vh;">
      <MemoPreview :data="form" />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import MemoPreview from './MemoPreview.vue';

const form = ref({
  headerImg: null,
  footerImg: null,
  codigo: 'OAC-M N°000/2026',
  fecha: new Date().toISOString().substr(0, 10),
  para: 'TAVIANA ELAINE ALQUINZONES FERNÁNDEZ\nDirectora General (E) de la Oficina de Gestión Administrativa',
  de: 'OLIVER EZEQUIEL RIVAS PAREDES\nDirector General (E) de la Oficina de Atención al Ciudadano',
  asunto: 'Remisión de Punto de Cuenta N° 000/2026.',
  cuerpo: 'Tengo a bien dirigirme a usted, en la oportunidad de remitir Punto de Cuenta...',
});

const handleImage = (event, type) => {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      if (type === 'header') form.value.headerImg = e.target.result;
      if (type === 'footer') form.value.footerImg = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};
</script>
