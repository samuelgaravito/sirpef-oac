<script setup lang="ts">
import { ref, reactive } from 'vue';
import { Http } from '@/utils/Http';
import init from '@/utils/Http/init';

const http = new Http(init);
const searchNumero = ref('');
const puntoCuentaInfo = ref(null);
const loading = ref(false);

const form = reactive({
  punto_cuenta_id: '',
  codigo: '',
  de: '',
  para: '',
  asunto: '',
  fecha: '',
  cuerpo: ''
});

const buscarPuntoCuenta = async () => {
  if (!searchNumero.value) return;
  loading.value = true;
  puntoCuentaInfo.value = null;
  
  try {
    const response = await http.get(`/oac/punto-cuenta-numero/${searchNumero.value}`);
    if (response.data.success) {
      const data = response.data.data;
      form.punto_cuenta_id = data.id;
      puntoCuentaInfo.value = data;
    }
  } catch (error) {
    console.error('Error al buscar Punto de Cuenta', error);
    alert('Punto de Cuenta no encontrado');
    form.punto_cuenta_id = '';
  } finally {
    loading.value = false;
  }
};

const guardarMemorandum = async () => {
  try {
    const response = await http.post('/oac/memorandum', form);
    if (response.data.success) {
      alert('Memorándum guardado exitosamente');
      // Reset form or redirect
    }
  } catch (error) {
    console.error('Error al guardar', error);
    alert('Error al guardar el memorándum');
  }
};
</script>

<template>
  <div class="p-4">
    <h1 class="text-2xl font-bold mb-4">Crear Memorándum</h1>
    
    <!-- Buscador de Punto de Cuenta -->
    <div class="mb-6 p-4 border rounded bg-gray-50">
      <label class="block mb-2 font-semibold">Buscar Punto de Cuenta por Número</label>
      <div class="flex gap-2">
        <input 
          v-model="searchNumero" 
          type="text" 
          class="border p-2 flex-1 rounded" 
          placeholder="Ej: 001-2024"
        />
        <button 
          @click="buscarPuntoCuenta" 
          class="bg-blue-600 text-white px-4 py-2 rounded"
          :disabled="loading"
        >
          {{ loading ? 'Buscando...' : 'Buscar' }}
        </button>
      </div>
      
      <div v-if="puntoCuentaInfo" class="mt-3 text-sm text-green-700">
        <strong>Punto Encontrado:</strong> {{ puntoCuentaInfo.asunto }} (ID: {{ puntoCuentaInfo.id }})
      </div>
    </div>

    <!-- Formulario de Memorandum -->
    <form @submit.prevent="guardarMemorandum" class="space-y-4">
      <div>
        <label class="block">ID Punto de Cuenta (Vinculado)</label>
        <input v-model="form.punto_cuenta_id" type="text" class="border p-2 w-full bg-gray-100" readonly required />
      </div>
      
      <div>
        <label class="block">Código Memorándum</label>
        <input v-model="form.codigo" type="text" class="border p-2 w-full" required />
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block">De</label>
          <input v-model="form.de" type="text" class="border p-2 w-full" required />
        </div>
        <div>
          <label class="block">Para</label>
          <input v-model="form.para" type="text" class="border p-2 w-full" required />
        </div>
      </div>

      <div>
        <label class="block">Asunto</label>
        <input v-model="form.asunto" type="text" class="border p-2 w-full" required />
      </div>

      <div>
        <label class="block">Fecha</label>
        <input v-model="form.fecha" type="date" class="border p-2 w-full" required />
      </div>

      <div>
        <label class="block">Cuerpo</label>
        <textarea v-model="form.cuerpo" class="border p-2 w-full h-32" required></textarea>
      </div>

      <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded font-bold">
        Guardar Memorándum
      </button>
    </form>
  </div>
</template>
