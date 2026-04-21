<template>
  <div class="p-4">
    <div class="card">
      <div class="card-header">
        <h5>Crear Memorándum</h5>
      </div>
      <div class="card-body">
        <form @submit.prevent="save">
          <div class="row mb-3">
            <div class="col-md-4">
              <label class="form-label">N° PUNTO CUENTA</label>
              <div class="input-group">
                <input 
                  type="text" 
                  v-model="form.numero_punto_cuenta" 
                  class="form-control" 
                  placeholder="Ej: 001-2024"
                  @keyup.enter="buscarPuntoCuenta"
                >
                <button 
                  class="btn btn-warning" 
                  type="button" 
                  @click="buscarPuntoCuenta"
                  :disabled="loadingSearch"
                >
                  <i v-if="!loadingSearch" class="fas fa-search"></i>
                  <i v-else class="fas fa-spinner fa-spin"></i>
                </button>
              </div>
              <small v-if="form.numero_punto_cuenta" :class="puntoCuentaValidado ? 'text-success' : 'text-danger'">
                <i :class="puntoCuentaValidado ? 'fas fa-check-circle' : 'fas fa-times-circle'"></i>
                {{ puntoCuentaValidado ? ' Vinculado' : ' No vinculado' }}
              </small>
            </div>
            <div class="col-md-4">
              <label class="form-label">Solicitante</label>
              <input type="text" v-model="infoPuntoCuenta.solicitante" class="form-control" readonly>
            </div>
            <div class="col-md-4">
              <label class="form-label">Cédula</label>
              <input type="text" v-model="infoPuntoCuenta.cedula" class="form-control" readonly>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Código Memorándum</label>
              <input type="text" v-model="form.codigo" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Fecha</label>
              <input type="date" v-model="form.fecha" class="form-control" required>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">De:</label>
              <input type="text" v-model="form.de" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Para:</label>
              <input type="text" v-model="form.para" class="form-control" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Asunto</label>
            <input type="text" v-model="form.asunto" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Cuerpo</label>
            <textarea v-model="form.cuerpo" class="form-control" rows="5" required></textarea>
          </div>

          <div class="text-end">
            <button type="submit" class="btn btn-primary" :disabled="saving || !puntoCuentaValidado">
              Guardar Memorándum
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import Http from '@/utils/Http/Http'
import Swal from 'sweetalert2'

const http = Http

const loadingSearch = ref(false)
const saving = ref(false)
const puntoCuentaValidado = ref(false)

const form = reactive({
  numero_punto_cuenta: '',
  codigo: '',
  de: '',
  para: '',
  asunto: '',
  fecha: new Date().toISOString().substr(0, 10),
  cuerpo: ''
})

const infoPuntoCuenta = reactive({
  solicitante: '',
  cedula: ''
})

const buscarPuntoCuenta = async () => {
  if (!form.numero_punto_cuenta) return

  loadingSearch.value = true
  puntoCuentaValidado.value = false
  
  try {
    const encodedNumero = encodeURIComponent(form.numero_punto_cuenta)
    const response = await http.get(`/api/oac/memorandum/buscar-punto-cuenta/${encodedNumero}`)
    if (response.data.success) {
      infoPuntoCuenta.solicitante = response.data.data.solicitante
      infoPuntoCuenta.cedula = response.data.data.cedula
      puntoCuentaValidado.value = true
    }
  } catch (error) {
    infoPuntoCuenta.solicitante = ''
    infoPuntoCuenta.cedula = ''
    puntoCuentaValidado.value = false
  } finally {
    loadingSearch.value = false
  }
}

const save = async () => {
  saving.value = true
  try {
    const response = await http.post('/api/oac/memorandum', form)
    if (response.data.success) {
      Swal.fire('Guardado', 'Memorándum creado correctamente', 'success')
      // Reset form or redirect
    }
  } catch (error) {
    Swal.fire('Error', 'No se pudo guardar el memorándum', 'error')
  } finally {
    saving.value = false
  }
}
</script>
