<script setup lang="ts">
import { ref, computed } from 'vue';
import { jsPDF } from 'jspdf';

// Props con valores por defecto para la prueba
const props = defineProps({
  numeroPunto: {
    type: String,
    default: '307/2025'
  },
  remitenteNombre: {
    type: String,
    default: 'OLIVER EZEQUIEL RIVAS PAREDES'
  },
  remitenteCargo: {
    type: String,
    default: 'Director General de la Oficina de Atención al Ciudadano'
  },
  asunto: {
    type: String,
    default: 'APROBACIÓN DE AYUDA ECONÓMICA PARA SUFRAGAR MONTO DE ESTUDIO MÉDICO ESPECIAL (TEST DE STAMEY), PARA EL CIUDADANO JOSÉ GONZALO RAMÓN SÁNCHEZ CHÁVEZ.'
  },
  fecha: {
    type: String,
    default: '13 de junio de 2025'
  }
});


const parseSpanishDate = (dateString: string): string => {
  const parts = dateString.split(' de ');
  if (parts.length !== 3) return new Date().toISOString().slice(0, 10);

  const day = parts[0].padStart(2, '0');
  const year = parts[2];
  const monthMap: { [key: string]: string } = {
    'enero': '01', 'febrero': '02', 'marzo': '03', 'abril': '04', 'mayo': '05', 'junio': '06',
    'julio': '07', 'agosto': '08', 'septiembre': '09', 'octubre': '10', 'noviembre': '11', 'diciembre': '12'
  };
  const month = monthMap[parts[1].toLowerCase()];

  return `${year}-${month}-${day}`;
};


const fechaSeleccionada = ref(parseSpanishDate(props.fecha));


const fechaFormateada = computed(() => {
  if (!fechaSeleccionada.value) return '';
  const [year, month, day] = fechaSeleccionada.value.split('-');
  const date = new Date(Number(year), Number(month) - 1, Number(day));
  
  return date.toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    timeZone: 'UTC' 
  });
});


const generarPDF = () => {
  const doc = new jsPDF({ orientation: 'p', unit: 'mm', format: 'letter' });
  const marginX = 15;
  const marginY = 15;
  const pageWidth = doc.internal.pageSize.getWidth();
  const contentWidth = pageWidth - marginX * 2;
  const azulOscuro = [0, 51, 102];
  const borderStartY = marginY - 5;
  let y = marginY + 5;

  try {
    doc.addImage('/escudo.png', 'PNG', 20, 17, 30, 27);
  } catch(e) {
    console.error("No se pudo cargar la imagen del escudo. Asegúrate que 'escudo.png' esté en la carpeta /public.", e);
  }
  y += 5;
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(10);
  doc.text('REPÚBLICA BOLIVARIANA DE VENEZUELA', pageWidth / 2, y, { align: 'center' });
  y += 5;
  doc.text('MINISTERIO DEL PODER POPULAR DE ECONOMÍA Y FINANZAS', pageWidth / 2, y, { align: 'center' });
  y += 10;
  doc.setFontSize(12);
  const puntoDeCuentaText = 'PUNTO DE CUENTA Nº';
  const numeroPuntoText = ` ${props.numeroPunto}`;
  const puntoWidth = doc.getTextWidth(puntoDeCuentaText);
  const startX = (pageWidth - (puntoWidth + doc.getTextWidth(numeroPuntoText))) / 2;
  doc.setTextColor(0, 0, 0);
  doc.text(puntoDeCuentaText, startX, y);
  doc.setTextColor(0, 0, 0);
  doc.text(numeroPuntoText, startX + puntoWidth, y);
  doc.setTextColor(0, 0, 0);
  y += 10;

  // Cuadro de Contenido Interno (sin cambios)
  const tableStartY = y;
  const labelWidth = 35;
  const textStartX = marginX + labelWidth;
  const textWidth = contentWidth - labelWidth;
  const remitenteHeight = 25;
  doc.setFontSize(14);
  doc.setFont('helvetica', 'bold');
  const asuntoLines = doc.splitTextToSize(props.asunto.toUpperCase(), textWidth - 5);
  const asuntoHeight = asuntoLines.length * 7.5;
  const contenidoAsuntoHeight = Math.max(40, asuntoHeight + 15);
  const totalBoxHeight = remitenteHeight + contenidoAsuntoHeight;
  doc.setLineWidth(0.3);
  doc.setDrawColor(azulOscuro[0], azulOscuro[1], azulOscuro[2]);
  doc.rect(marginX, tableStartY, contentWidth, totalBoxHeight);
  doc.line(textStartX, tableStartY, textStartX, tableStartY + totalBoxHeight);
  doc.line(marginX, tableStartY + remitenteHeight, marginX + contentWidth, tableStartY + remitenteHeight);
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(11);
  doc.text('Remitente:', marginX + 5, tableStartY + 13);
  doc.setFontSize(12);
  doc.text(props.remitenteNombre.toUpperCase(), textStartX + 3, tableStartY + 10);
  doc.setFont('helvetica', 'normal');
  doc.setFontSize(11);
  doc.text(props.remitenteCargo, textStartX + 3, tableStartY + 16);
  const asuntoY = tableStartY + remitenteHeight + 13;
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(11);
  doc.text('ASUNTO:', marginX + 5, asuntoY);
  doc.setFontSize(14);
  doc.text(asuntoLines, textStartX + 3, asuntoY, { lineHeightFactor: 1.8 });
  
  // --- Fecha personalizada ---
  // AHORA USA LA PROPIEDAD COMPUTADA
  const fechaY = tableStartY + totalBoxHeight + 10;
  doc.setFontSize(10);
  doc.setFont('helvetica', 'normal');
  doc.text(fechaFormateada.value, pageWidth - marginX, fechaY, { align: 'right' });

  // Borde exterior
  const borderPaddingBottom = 5;
  const borderTotalHeight = (fechaY + borderPaddingBottom) - borderStartY;
  doc.setLineWidth(1.5);
  doc.setDrawColor(azulOscuro[0], azulOscuro[1], azulOscuro[2]);
  doc.roundedRect(marginX - 5, borderStartY, contentWidth + 10, borderTotalHeight, 5, 5, 'S');

  doc.save(`ficha_oficio_${props.numeroPunto.replace('/', '-')}.pdf`);
};
</script>

<template>
  <div class="bg-gray-200 min-h-screen p-8 flex flex-col items-center gap-8">
    
    <div class="ficha-preview bg-white shadow-lg">
        <div class="inner-border">
            <header class="ficha-header">
                <img src="/escudo.png" alt="Escudo" class="escudo">
                <div class="ministerio-info">
                <p>REPÚBLICA BOLIVARIANA DE VENEZUELA</p>
                <p>MINISTERIO DEL PODER POPULAR DE ECONOMÍA Y FINANZAS</p>
                </div>
            </header>
            <div class="punto-cuenta-title">
                PUNTO DE CUENTA N<span class="text-black-600">º {{ numeroPunto }}</span>
            </div>
            <main class="ficha-main-content">
                <div class="remitente-section">
                <div class="label">Remitente:</div>
                <div class="content">
                    <p class="font-bold text-sm">{{ remitenteNombre.toUpperCase() }}</p>
                    <p class="text-xs">{{ remitenteCargo }}</p>
                </div>
                </div>
                <div class="asunto-section">
                <div class="label">ASUNTO:</div>
                <div class="content">
                    <p class="font-bold text-base">{{ asunto.toUpperCase() }}</p>
                </div>
                </div>
            </main>
            <footer class="ficha-footer">
                <p>{{ fechaFormateada }}</p>
            </footer>
        </div>
    </div>

    <div class="controles-pdf bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
      <h3 class="text-xl font-bold mb-4 text-center">Generar Ficha</h3>
      <div class="mb-4">
        <label for="fecha" class="block text-sm font-medium text-gray-700">Seleccionar Fecha:</label>
  
        <input 
          type="date" 
          id="fecha" 
          v-model="fechaSeleccionada" 
          class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        >
      </div>
      <button @click="generarPDF" class="w-full bg-blue-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors">
        Generar PDF
      </button>
    </div>

  </div>
</template>

<style scoped>
.ficha-preview {
  width: 216mm;
  height: auto; 
  padding: 10mm;
  background-color: white;
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
  font-family: 'Arial', sans-serif;
  box-sizing: border-box;
}

.inner-border {
  border: 3px solid #003366; 
  border-radius: 20px;
  width: 100%;
  height: 100%;
  padding: 10mm;
  display: flex;
  flex-direction: column;
  box-sizing: border-box;
}

.ficha-header {
  text-align: center;
  margin-bottom: 20px;
  position: relative;
}

.escudo {
  position: absolute;
  left: 0;
  top: -20px;
  width: 100px; 
  height: 90px;
}


.ministerio-info {
  font-weight: bold;
  font-size: 10pt;
  line-height: 1.4;
}

.punto-cuenta-title {
  text-align: center;
  font-weight: bold;
  font-size: 12pt;
  margin-bottom: 15px;
}

.ficha-main-content {
  border: 1px solid #003366;
  display: flex;
  flex-direction: column;
}

.remitente-section, .asunto-section {
  display: flex;
  font-size: 11pt;
}

.remitente-section {
  border-bottom: 1px solid #003366;
}

.label {
  padding: 8px 12px;
  width: 100px;
  flex-shrink: 0;
  display: flex;
  align-items: start;
  font-weight: bold;
  font-size: 11pt;
}

.content {
  padding: 8px 12px;
  border-left: 1px solid #003366;
  flex-grow: 1;
}
.content p.font-bold {
    font-size: 12pt;
}
.content .text-xs {
    font-size: 11pt;
}
.asunto-section .content p {
    font-size: 14pt;
    line-height: 2;
    letter-spacing: 0.5px;
}

.ficha-footer {
  margin-top: 15px;
  padding-top: 10px;
  text-align: right;
  font-size: 10pt;
}
</style>