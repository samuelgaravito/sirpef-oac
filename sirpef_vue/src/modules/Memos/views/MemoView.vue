<template>
  <div class="flex flex-col h-screen bg-gray-50 overflow-hidden">
    <div class="p-4 bg-white border-b shadow-sm flex justify-between items-center no-print">
      <h1 class="text-xl font-bold text-gray-800">Generador de Memorándum</h1>
      <button @click="printMemo" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center transition-colors">
        Imprimir / PDF
      </button>
    </div>
    
    <div class="flex flex-1 overflow-hidden">
      <!-- Form Side -->
      <div class="w-1/3 overflow-y-auto border-r bg-white p-6 no-print">
        <MemoForm :form="memoData" />
      </div>

      <!-- Preview Side -->
      <div class="w-2/3 overflow-y-auto bg-gray-200 p-8 flex justify-center print:bg-white print:p-0">
        <div id="memo-printable">
          <MemoPreview :data="memoData" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import MemoForm from '../components/MemoForm.vue';
import MemoPreview from '../components/MemoPreview.vue';

const memoData = ref({
  headerImg: null,
  footerImg: null,
  codigo: 'OAC-M N°000/2026',
  fecha: '00/00/2026',
  para: 'TAVIANA ELAINE ALQUINZONES FERNÁNDEZ\nDirectora General (E) de la Oficina de Gestión Administrativa',
  de: 'OLIVER EZEQUIEL RIVAS PAREDES\nDirector General (E) de la Oficina de Atención al Ciudadano',
  asunto: 'Remisión de Punto de Cuenta N° 000/2026.',
  cuerpo: 'Tengo a bien dirigirme a usted, en la oportunidad de remitir Punto de Cuenta N°000/2026 de fecha 00/00/2026, para sufragar monto para intervención quirúrgica (cesárea), debidamente aprobado, el cual se especifica a continuación:',
  tabla: {
    pto_cta: '000/2026',
    fecha: '01/01/2026',
    solicitante: '',
    ci: '',
    monto: '',
    proveedor: '',
    total: ''
  },
  cuerpo_final: 'Agradeciendo la receptividad que tenga a bien dispensar a la presente, en girar la instrucción correspondiente a fin de realizar el trámite de orden de pago, quedo de usted.',
  resolucion: 'Resolución N° 006-2024 publicada en la Gaceta Oficial de la República Bolivariana de Venezuela N° 42.958 ambos de fecha 06 de septiembre de 2024',
  iniciales: 'AP/NM/YB/da'
});

const printMemo = () => {
  window.print();
};
</script>

<style>
@media print {
  .no-print {
    display: none !important;
  }
}
</style>
