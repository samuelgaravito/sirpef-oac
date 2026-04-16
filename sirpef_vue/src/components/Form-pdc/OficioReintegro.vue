<script setup lang="ts">
import { ref } from 'vue';
import jsPDF from 'jspdf';
// 1. Usamos la importación recomendada por la documentación.
import autoTable from 'jspdf-autotable';
import type { UserOptions, RowInput } from 'jspdf-autotable';

// Props con valores por defecto
const props = defineProps({
  nroOficio: { type: String, default: '019-2023' },
  fechaDocumento: { type: String, default: 'xx/xx/202x' },
  destinatario: { type: String, default: 'CENTRO ÓPTICO CANAIMA II, C.A.' },
  ciudadanoNombre: { type: String, default: 'DOUGLAS ALEXANDER CUENCE CEDEÑO' },
  ciudadanoCedula: { type: String, default: '11.209.166' },
  montoTotalLetras: { type: String, default: 'BOLÍVARES SEIS MIL DOSCIENTOS CINCUENTA Y NUEVE CON NOVENTA CÉNTIMOS' },
  montoTotalNumeros: { type: String, default: '6.259,90' },
  nroFactura: { type: String, default: '0001202' },
  fechaFactura: { type: String, default: '27/04/2023' },
  montoUtilizadoLetras: { type: String, default: 'CINCO MIL SETECIENTOS QUINCE CON CINCUENTA Y NUEVE CÉNTIMOS' },
  montoUtilizadoNumeros: { type: String, default: '5.715,59' },
  saldoAFavorLetras: { type: String, default: 'BOLÍVARES QUINIENTOS CUARENTA Y CUATRO CON TREINTA Y UN CÉNTIMOS' },
  saldoAFavorNumeros: { type: String, default: '544,31' },
  firmanteNombre: { type: String, default: 'OLIVER EZEQUIEL RIVAS PAREDES' },
  firmanteCargo: { type: String, default: 'Director General (E) de la Oficina de Atención al Ciudadano' },
  firmanteResolucion: { type: String, default: 'Resolución No. 006-2024, publicada en la Gaceta Oficial de la República Bolivariana de Venezuela No. 42.958, de fecha 06 de septiembre de 2024.' },
  tabla1: {
    type: Array as () => RowInput[],
    default: () => [['Banesco', '26/4/2023', '74382', '6.259,90']]
  },
  tabla2: {
    type: Array as () => RowInput[],
    default: () => [['BCV', 'TESORO NACIONAL', 'G-200123516', '0001-0001-31-0005002017']]
  },
});

const generarOficioPDF = () => {
  const doc = new jsPDF();
  let y = 15;

  const cintilloWidth = doc.internal.pageSize.getWidth() - 15 * 2;
  const cintilloHeight = 15;
  try {
    doc.addImage('/cintillo.png', 'PNG', 15, y, cintilloWidth, cintilloHeight);
  } catch(e) {
    console.error("No se pudo cargar cintillo.png. Asegúrate que esté en la carpeta /public.", e);
  }
  y += cintilloHeight + 5;
  
  doc.setDrawColor(0);
  doc.setLineWidth(0.2);
  doc.line(15, y, 195, y);
  y += 10;
  
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(11);
  doc.text(`DG-OAC- N° ${props.nroOficio}`, 15, y);
  doc.text(`Caracas, ${props.fechaDocumento}`, 195, y, { align: 'right' });
  y += 15;
  doc.setFont('helvetica', 'normal');
  doc.setFontSize(12);
  doc.text('Señores,', 15, y);
  y += 5;
  doc.setFont('helvetica', 'bold');
  doc.text(props.destinatario.toUpperCase(), 15, y);
  y += 5;
  doc.setFont('helvetica', 'normal');
  doc.text('Presente.-', 15, y);
  y += 15;

  const renderRichText = (text, startX, startY, maxWidth) => {
    let x = startX;
    let currentY = startY;
    const parts = text.split(/(\*\*.*?\*\*)/g).filter(part => part.length > 0);
    doc.setFontSize(12);
    
    parts.forEach(part => {
        const isBold = part.startsWith('**') && part.endsWith('**');
        const cleanPart = isBold ? part.slice(2, -2) : part;
        doc.setFont('helvetica', isBold ? 'bold' : 'normal');
        const words = cleanPart.split(' ');
        words.forEach(word => {
            const wordWidth = doc.getTextWidth(word + ' ');
            if (x + wordWidth > startX + maxWidth) {
                x = startX;
                currentY += 6;
            }
            doc.text(word, x, currentY);
            x += wordWidth;
        });
    });
    return currentY;
  };

  const textBody1 = `Tengo el agrado de dirigirme a ustedes, en la oportunidad de enviarle un cordial saludo y a su vez informarle que este Ministerio, aprobó una ayuda económica, perteneciente a la ayuda económica para el ciudadano **${props.ciudadanoNombre}** titular de la Cédula de Identidad **N° ${props.ciudadanoCedula}**, por un monto total de **${props.montoTotalLetras} (Bs. ${props.montoTotalNumeros})**, para cubrir gastos de adquisición de lentes correctivos, según cuadro descriptivo:`;
  y = renderRichText(textBody1, 15, y, 180);
  y += 10;

  autoTable(doc, {
    startY: y,
    head: [['Banco', 'Fecha', 'Referencia', 'Monto']],
    body: props.tabla1,
    theme: 'grid',
    headStyles: { fillColor: [192, 0, 0], textColor: [255, 255, 255], fontStyle: 'bold' },
    bodyStyles: { fillColor: [255, 255, 255], textColor: [0, 0, 0], fontStyle: 'bold' },
    styles: { cellPadding: 2, fontSize: 11, halign: 'center' },
  });
  
  y = (doc as any).lastAutoTable.finalY + 10;

  const textBody2 = `Así mismo tengo el deber de informarle que al no ser fue utilizada en su totalidad, según factura **N° ${props.nroFactura}** de fecha **${props.fechaFactura}**, reflejando el uso de **${props.montoUtilizadoLetras} (Bs. ${props.montoUtilizadoNumeros})**, quedando un saldo a favor de este organismo por **${props.saldoAFavorLetras} (Bs. ${props.saldoAFavorNumeros})** por lo que se requiere su reintegro a la cuenta de este ente ministerial, a través de la vía **TRANSFERENCIA BANCARIA** según descripción:`;
  y = renderRichText(textBody2, 15, y, 180);
  y += 10;
  
  autoTable(doc, {
    startY: y,
    head: [['Banco', 'Titular', 'Rif', 'N° de Cuenta']],
    body: props.tabla2,
    theme: 'grid',
    headStyles: { fillColor: [192, 0, 0], textColor: [255, 255, 255], fontStyle: 'bold' },
      bodyStyles: { fillColor: [255, 255, 255], textColor: [0, 0, 0], fontStyle: 'bold' },
    styles: { cellPadding: 2, fontSize: 11, halign: 'center' },
  });
  y = (doc as any).lastAutoTable.finalY + 10;

  const textBody3 = "Sin otro particular a que hacer referencia y agradeciendo de antemano toda la colaboración que pueda prestar a la presente, se despide de usted.";
  y = renderRichText(textBody3, 15, y, 180);
  y += 15;
  
  doc.setFont('helvetica', 'normal');
  doc.text('Atentamente,', 95, y, { align: 'center' });
  y += 25;
  doc.setFont('helvetica', 'bold');
  doc.text(props.firmanteNombre, 95, y, { align: 'center' });
  y += 5;
  doc.setFont('helvetica', 'normal');
  const cargoLines = doc.splitTextToSize(props.firmanteCargo, 120);
  doc.text(cargoLines, 95, y, { align: 'center' });
  y += cargoLines.length * 5;
  const resolucionLines = doc.splitTextToSize(props.firmanteResolucion, 120);
  doc.text(resolucionLines, 95, y, { align: 'center' });
  
  doc.save(`oficio_reintegro_${props.nroOficio}.pdf`);
};


</script>
<template>
  <div class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white shadow-lg p-10">
      <!-- Previsualización HTML actualizada con el cintillo -->
      <header class="flex justify-between items-center mb-4">
        <img src="/cintillo.png" alt="Cintillo del Gobierno" class="w-full">
      </header>
      <hr class="border-gray-400 mb-6">
      
      <!-- El resto de la previsualización no necesita cambios -->
      <section class="flex justify-between mb-8">
        <p class="font-bold">DG-OAC- N° {{ nroOficio }}</p>
        <p>Caracas, {{ fechaDocumento }}</p>
      </section>
      <section class="mb-8">
        <p>Señores,</p>
        <p class="font-bold">{{ destinatario.toUpperCase() }}</p>
        <p>Presente.-</p>
      </section>
      <section class="text-justify space-y-6 text-sm">
        <p>
          Tengo el agrado de dirigirme a ustedes, en la oportunidad de enviarle un cordial saludo y a su vez informarle que este Ministerio, aprobó una ayuda económica, perteneciente a la ayuda económica para el ciudadano <strong class="font-bold">{{ ciudadanoNombre }}</strong> titular de la Cédula de Identidad <strong class="font-bold">N° {{ ciudadanoCedula }}</strong>, por un monto total de <strong class="font-bold">{{ montoTotalLetras }} (Bs. {{ montoTotalNumeros }})</strong>, para cubrir gastos de adquisición de lentes correctivos, según cuadro descriptivo:
        </p>
        <table class="w-full border-collapse text-center border border-gray-400">
          <thead>
            <tr class="bg-red-700 text-white font-bold">
              <th class="border border-gray-300 p-2">Banco</th>
              <th class="border border-gray-300 p-2">Fecha</th>
              <th class="border border-gray-300 p-2">Referencia</th>
              <th class="border border-gray-300 p-2">Monto</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(row, index) in tabla1" :key="index">
              <td class="border border-gray-300 p-2 text-center we font-bold" v-for="(cell, i) in row" :key="i">{{ cell }}</td>
            </tr>
          </tbody>
        </table>
        <p>
          Así mismo tengo el deber de informarle que al no ser fue utilizada en su totalidad, según factura <strong class="font-bold">N° {{ nroFactura }}</strong> de fecha <strong class="font-bold">{{ fechaFactura }}</strong>, reflejando el uso de <strong class="font-bold">{{ montoUtilizadoLetras }} (Bs. {{ montoUtilizadoNumeros }})</strong>, quedando un saldo a favor de este organismo por <strong class="font-bold">{{ saldoAFavorLetras }} (Bs. {{ saldoAFavorNumeros }})</strong> por lo que se requiere su reintegro a la cuenta de este ente ministerial, a través de la vía <strong class="font-bold">TRANSFERENCIA BANCARIA</strong> según descripción:
        </p>
        <table class="w-full border-collapse border text-center border-gray-400">
          <thead>
            <tr class="bg-red-700 text-white font-bold">
              <th class="border border-gray-300 p-2">Banco</th>
              <th class="border border-gray-300 p-2">Titular</th>
              <th class="border border-gray-300 p-2">Rif</th>
              <th class="border border-gray-300 p-2">N° de Cuenta</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(row, index) in tabla2" :key="index">
              <td class="border border-gray-300 p-2 text-center font-bold" v-for="(cell, i) in row" :key="i">{{ cell }}</td>
            </tr>
          </tbody>
        </table>
        <p>
          Sin otro particular a que hacer referencia y agradeciendo de antemano toda la colaboración que pueda prestar a la presente, se despide de usted.
        </p>
      </section>
      <section class="mt-16 text-center">
        <p>Atentamente,</p>
        <div class="mt-16">
          <p class="font-bold">{{ firmanteNombre }}</p>
          <p class="text-sm">{{ firmanteCargo }}</p>
          <p class="text-xs mt-2">{{ firmanteResolucion }}</p>
        </div>
      </section>
    </div>
    
    <button @click="generarOficioPDF" class="mt-8 bg-blue-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-blue-700 transition-colors">
      Generar Oficio de Reintegro
    </button>
  </div>
</template>