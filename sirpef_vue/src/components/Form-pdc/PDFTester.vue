<script setup lang="ts">
import { reactive, ref, watch, onMounted, onUnmounted } from 'vue';
import generatePuntoDeCuentaPDF from '@/modules/FeDeVida/utils/pdf-punto';

// Definición de interfaces para una tipificación estricta de los datos del formulario
interface Firma {
  nombre: string;
  cargo: string;
  resolucion1: string;
  resolucion2?: string;
}

interface PuntoDeCuentaData {
  [key: string]: any; // Permite el acceso dinámico a las propiedades en el v-for
  puntoNro: string;
  nroPaginas: string;
  fechaDocumento: string;
  presentadoA_nombre: string;
  presentadoA_cargo: string;
  presentadoPor_nombre: string;
  presentadoPor_cargo: string;
  asunto: string;
  decision: 'APROBADO' | 'NEGADO' | 'VISTO' | 'DIFERIDO' | 'OTRO' | '';
  otrasInstrucciones: string;
  anexos: 'Si' | 'No' | '';
  firmaIzquierda: Firma;
  firmaDerecha: Firma;
  exposicionDeMotivosCompleta: string;
  propuestaCompleta: string;
}

// Datos iniciales para el formulario, usando el texto largo para un test más realista
const today = new Date().toISOString().slice(0, 10);
const form = reactive<PuntoDeCuentaData>({
  puntoNro: '454/2024',
  nroPaginas: '1/1',
  fechaDocumento: today,
  presentadoA_nombre: 'ANMY IVONETT PÉREZ GONZÁLEZ',
  presentadoA_cargo: 'Directora General de la Consultoría Jurídica',
  presentadoPor_nombre: 'OLIVER EZEQUIEL RIVAS PAREDES',
  presentadoPor_cargo: 'Director General (E) de la Oficina de Atención al Ciudadano',
  asunto: 'APROBACIÓN DE AYUDA ECONÓMICA PARA INTERVENCIÓN QUIRÚRGICA.',
  decision: 'APROBADO',
  otrasInstrucciones: 'Se instruye a la Oficina de Presupuesto tramitar lo conducente para el pago correspondiente.',
  anexos: 'Si',
  firmaIzquierda: {
    nombre: 'OLIVER EZEQUIEL RIVAS PAREDES',
    cargo: 'Director General (E) de la Oficina de Atención al Ciudadano',
    resolucion1: 'Resolución No. 006-2024, publicada en la Gaceta Oficial de la República Bolivariana de Venezuela No. 42.958, de fecha 06 de septiembre de 2024. y actuando según delegación de firma en Resolución N° 010-2024 publicada en la Gaceta Oficial de la R.B.V N° 42.975 de fecha 30/09/202',
  },
  firmaDerecha: {
    nombre: 'ANMY IVONETT PÉREZ GONZÁLEZ',
    cargo: 'Directora General de la Consultora Jurídica',
    resolucion1: 'Resolución N° 004-2024 de fecha 02 de septiembre del 2024, publicada en Gaceta Oficial de la R.B.V N° 42.955 de fecha 03 de septiembre del 2024;',

  },
  exposicionDeMotivosCompleta: `De acuerdo a lo establecido en la Gaceta Oficial N° 38.750 del 20 de agosto del 2007 en Capitulo II de la sección I de las oficinas de atención al ciudadano, en su artículo 13 numerales 1,4 y 6, así como en su sección II de la tramitación de las denuncias, quejas, reclamos, sugerencias y peticiones en su artículo 16, se recibe en esta Dirección General la solicitud de ayuda económica de la ciudadana MAYÉRLING MILAGRO MICETT RODRÍGUEZ titular de la cédula de identidad N° V-19.753.269, para sufragar monto por adquisición de insumos médicos para intervención quirúrgica (cateterismo cardiaco), para su hija la infante GIANNA SOPHIA MONSALVE MICETT de 2 años de edad, por presentar diagnóstico Canal AV completo ratelli tipo A. Síndrome de Down, según informe médico anexo. El monto total asciende a la cantidad de BOLÍVARES CIENTO CINCUENTA Y CUATRO MIL QUINIENTOS CINCUENTA CON CERO CÉNTIMOS (Bs.154.550,00) según presupuesto N° 0000011825 de fecha 19/11/2024 la cual será cancelada a INVERSIONES MÉDICA A Y E, C.A, de la siguiente manera a través de la compensación del monto de BOLÍVARES TREINTA Y SEIS MIL QUINIENTOS VEINTE CON CERO CÉNTIMOS (Bs.36.520,00), a favor de este Órgano Ministerial según Nota de Crédito N° 000765 y mediante el pago a la referida sociedad mercantil por BOLÍVARES CIENTO DIECIOCHO MIL TREINTA CON CERO CÉNTIMOS (Bs.118.030,00), en vista de que la referida ciudadana antes identificada, manifiesta no contar con los recursos económicos para sufragar el monto de lo requerido. La paciente será intervenida quirúrgicamente en el Hospital Militar "Dr. Carlos Arvelo. Fundacardín, ubicado Av. San Martin. Parroquia San Juan Distrito Capital`,
  propuestaCompleta: `Muy respetuosamente, se somete a consideración, evaluación y posterior decisión de la ciudadana ANMY IVONETT PÉREZ GONZÁLEZ en su condición de Directora General de la Consultoría Jurídica del Ministerio del Poder Popular de Economía, Finanzas y Comercio Exterior, según Resolución N° 004-2024 de fecha 02 de septiembre del 2024, publicada en Gaceta Oficial Ordinaria de la República Bolivariana de Venezuela N° 42.955 de fecha 03 de septiembre del 2024; y actuando según delegación de firma como consta en la Resolución N° 010-2024 de fecha 30/09/2024; el otorgamiento de la ayuda económica solicitada, por un monto de BOLÍVARES CIENTO CINCUENTA Y CUATRO MIL QUINIENTOS CINCUENTA CON CERO CÉNTIMOS (Bs.154.550,00), el cual será cancelado a INVERSIONES MÉDICA A Y E, C.A. RIF: J-298641380 de la siguiente manera: 1: a través de la compensación del monto de BOLÍVARES TREINTA Y SEIS MIL QUINIENTOS VEINTE CON CERO CÉNTIMOS (Bs.36.520,00) a favor de este órgano Ministerial según nota de crédito N° 000765 y; 2: mediante el pago a la referida sociedad mercantil por BOLÍVARES CIENTO DIECIOCHO MIL TREINTA CON CERO CÉNTIMOS (Bs.118.030,00).`
});

// Refs para gestionar las URLs del PDF y evitar fugas de memoria
const pdfUrl = ref('');
const previousPdfUrl = ref('');

// Función que se encarga de generar el PDF y actualizar la URL
const regeneratePDF = () => {
  // Limpia la URL anterior de la memoria para evitar memory leaks
  if (previousPdfUrl.value) {
    URL.revokeObjectURL(previousPdfUrl.value);
  }

  // **MAPEO DE DATOS:** Transforma los datos del formulario a la estructura que espera la función `generatePuntoDeCuentaPDF`
  const pdfData = {
      numero_punto: form.puntoNro,
      fecha: form.fechaDocumento,
      presentado_a: form.presentadoA_nombre,
      cargo_a: form.presentadoA_cargo,
      presentado_por: form.presentadoPor_nombre,
      cargo_por: form.presentadoPor_cargo,
      asunto: form.asunto,
      exposicion_motivos: form.exposicionDeMotivosCompleta,
      propuesta: form.propuestaCompleta,
      decision: form.decision,
      otras_instrucciones: form.otrasInstrucciones,
      anexos: form.anexos === 'Si', // Convierte el string a booleano
      resolucion_1: form.firmaIzquierda.resolucion1,
      resolucion_2: `${form.firmaDerecha.resolucion1} ${form.firmaDerecha.resolucion2 || ''}`.trim(),
  };

  // Llama a la función de generación de PDF, solicitando una 'bloburl'
  const newUrl = generatePuntoDeCuentaPDF(pdfData, 'bloburl') as string;
  pdfUrl.value = newUrl;
  
  // Guarda la nueva URL para poder limpiarla en la próxima regeneración
  previousPdfUrl.value = newUrl;
};

// Observador que vigila cualquier cambio en el formulario y llama a la regeneración del PDF
watch(form, regeneratePDF, { deep: true });

// Genera el PDF inicial cuando el componente se monta por primera vez
onMounted(regeneratePDF);

// Hook del ciclo de vida que limpia la última URL cuando el componente se destruye
onUnmounted(() => {
  if (pdfUrl.value) {
    URL.revokeObjectURL(pdfUrl.value);
  }
});

</script>

<template>
  <div class="pdf-tester-layout">
    <!-- Columna Izquierda: Formulario de Controles -->
    <div class="form-column">
      <h1 class="tester-title">Banco de Pruebas de PDF</h1>
      <form class="space-y-4">
        <!-- Itera sobre las propiedades del objeto 'form' para crear los campos dinámicamente -->
        <div v-for="(value, key) in form" :key="key">
          <label :for="key" class="label-style">{{ key.replace(/([A-Z])/g, ' $1') }}</label>
          
          <!-- Renderiza un input de texto para campos de tipo string simples -->
          <input v-if="typeof value === 'string' && !key.toLowerCase().includes('completa') && key !== 'anexos' && key !== 'decision'" type="text" :id="key" v-model="form[key]" class="input-style" />
          
           <!-- Renderiza un select para la decisión -->
          <select v-if="key === 'decision'" :id="key" v-model="form[key]" class="input-style">
            <option>APROBADO</option>
            <option>NEGADO</option>
            <option>VISTO</option>
            <option>DIFERIDO</option>
            <option>OTRO</option>
          </select>

          <!-- Renderiza un select para los anexos -->
          <select v-if="key === 'anexos'" :id="key" v-model="form[key]" class="input-style">
            <option>Si</option>
            <option>No</option>
          </select>
          
          <!-- Renderiza un textarea para campos de texto largos -->
          <textarea v-if="key.toLowerCase().includes('completa')" :id="key" v-model="form[key]" rows="6" class="input-style"></textarea>
          
          <!-- Renderiza un sub-formulario para campos de tipo objeto (las firmas) -->
          <div v-if="typeof value === 'object' && value !== null" class="nested-form">
            <div v-for="(subValue, subKey) in value" :key="subKey">
              <label :for="`${key}-${subKey}`" class="label-style nested-label">{{ subKey }}</label>
              <textarea :id="`${key}-${subKey}`" v-model="form[key][subKey]" rows="4" class="input-style"></textarea>
            </div>
          </div>
        </div>
      </form>
    </div>

    <!-- Columna Derecha: Previsualizador de PDF -->
    <div class="pdf-column">
      <!-- El iframe se recrea cada vez que 'pdfUrl' cambia gracias a :key -->
      <iframe v-if="pdfUrl" :key="pdfUrl" :src="pdfUrl" class="pdf-iframe"></iframe>
      <div v-else class="loading-placeholder">Generando PDF...</div>
    </div>
  </div>
</template>

<style scoped>
.pdf-tester-layout {
  display: grid;
  grid-template-columns: 40% 60%;
  height: 100vh;
  width: 100%;
  gap: 1rem;
  background-color: #f0f2f5;
  padding: 1rem;
  box-sizing: border-box;
}

.form-column, .pdf-column {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  overflow: hidden;
}

.form-column {
  overflow-y: auto;
  padding: 1.5rem;
}

.tester-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1a202c;
  margin-bottom: 1.5rem;
  border-bottom: 2px solid #e5e7eb;
  padding-bottom: 0.75rem;
}

.label-style {
  display: block;
  font-weight: 600;
  font-size: 0.9rem;
  color: #4a5568;
  margin-bottom: 0.3rem;
  text-transform: capitalize;
}

.nested-label {
    color: #2b6cb0;
    padding-left: 1rem;
    font-size: 0.85rem;
}

.input-style {
  width: 100%;
  padding: 0.6rem 0.8rem;
  border: 1px solid #cbd5e0;
  border-radius: 6px;
  font-size: 1rem;
  transition: border-color 0.2s, box-shadow 0.2s;
  box-sizing: border-box;
  resize: vertical;
  background-color: #fff; /* Asegura fondo blanco para selects */
}

.input-style:focus {
  outline: none;
  border-color: #3182ce;
  box-shadow: 0 0 0 2px rgba(49, 130, 206, 0.2);
}

.nested-form {
    border-left: 3px solid #e2e8f0;
    padding-left: 1rem;
    margin-top: 0.75rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.space-y-4 > * + * {
    margin-top: 1.5rem;
}

.pdf-column {
  display: flex;
  flex-direction: column;
}

.pdf-iframe {
  flex-grow: 1;
  width: 100%;
  height: 100%;
  border: none;
}

.loading-placeholder {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
  font-size: 1.2rem;
  color: #718096;
}
</style>