<script setup lang="ts">
import { ref } from 'vue';
import { jsPDF } from 'jspdf';

const form = ref({
  codigo: '',
  para: '',
  de: '',
  asunto: '',
  fecha: new Date().toISOString().substr(0, 10),
  cuerpo: ''
});

const generatePDF = () => {
  const { codigo, para, de, asunto, fecha, cuerpo } = form.value;

  if (!codigo || !para || !de || !asunto || !fecha || !cuerpo) {
    alert("Por favor complete todos los campos");
    return;
  }

  const doc = new jsPDF();
  
  // Diseño del PDF
  doc.setFontSize(18);
  doc.setFont("helvetica", "bold");
  doc.text("MEMORÁNDUM", 105, 20, { align: "center" });
  
  doc.setFontSize(12);
  doc.text(`Código: ${codigo}`, 20, 40);
  
  doc.line(20, 45, 190, 45); // Línea divisoria
  
  doc.text(`PARA:`, 20, 55);
  doc.setFont("helvetica", "normal");
  doc.text(para, 50, 55);
  
  doc.setFont("helvetica", "bold");
  doc.text(`DE:`, 20, 65);
  doc.setFont("helvetica", "normal");
  doc.text(de, 50, 65);
  
  doc.setFont("helvetica", "bold");
  doc.text(`ASUNTO:`, 20, 75);
  doc.setFont("helvetica", "normal");
  doc.text(asunto, 50, 75);
  
  doc.setFont("helvetica", "bold");
  doc.text(`FECHA:`, 20, 85);
  doc.setFont("helvetica", "normal");
  doc.text(fecha, 50, 85);
  
  doc.line(20, 90, 190, 90); // Segunda línea
  
  // Cuerpo del mensaje con ajuste de línea
  const splitText = doc.splitTextToSize(cuerpo, 170);
  doc.text(splitText, 20, 105);
  
  // Firma
  const pageHeight = doc.internal.pageSize.height;
  doc.text("__________________________", 105, pageHeight - 40, { align: "center" });
  doc.text("Firma", 105, pageHeight - 30, { align: "center" });

  doc.save(`Memorandum_${codigo}.pdf`);
};
</script>

<template>
  <div class="p-6 bg-white rounded-lg shadow-md max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Crear Memorándum</h2>
    <form @submit.prevent="generatePDF" class="space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Código/Número de Memo:</label>
          <input v-model="form.codigo" type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="Ej: MEMO-001-2024" required>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Fecha:</label>
          <input v-model="form.fecha" type="date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Para:</label>
        <input v-model="form.para" type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="Nombre del destinatario" required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">De:</label>
        <input v-model="form.de" type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="Tu nombre o cargo" required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Asunto:</label>
        <input v-model="form.asunto" type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="Motivo del memorándum" required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Contenido:</label>
        <textarea v-model="form.cuerpo" rows="6" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="Escriba el mensaje aquí..." required></textarea>
      </div>

      <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
          Descargar PDF
        </button>
      </div>
    </form>
  </div>
</template>
