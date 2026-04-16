import { alerta } from '@/utils/alert';
import convertDateISO from '@/utils/convertDateISO';
import Http from '@/utils/Http';
import jsPDF from 'jspdf';
import QRCode from 'qrcode';

export const generatePDF = async (cedula: string) => {
    const url = location.host
    try {
        const res = await Http.get(`/api/findByCedula/fedevida/search/${cedula}`);
        const data = res.data[0];
        if(data.voto == false) return alerta('info', 'No ha participado', 'error')
        const doc = new jsPDF();
        let telefono2 = data.telefono_2;
        // Generar el código QR
        const qrCodeDataUrl = await QRCode.toDataURL(`http://${url}/fedevida/checkpdf/${cedula}`);
        const tipo_empleado: string = data.tipo_empleado || 'Sin información'
        const lugar = data.pais_id == 170 ? data.localizacion : data.nombrepais
        const fechaConvertida = convertDateISO(data.fecha_registro);

        doc.addImage("/bannerMPPEF.jpg", 'JPG', 10, 5, 170, 15);
        doc.setFont("arial", "bold");

        doc.setFontSize(14);
        doc.text('COMPROBANTE FE DE VIDA AÑO 2025', 60, 35);
        doc.setFontSize(10);

        doc.rect(5, 40, 200, 160); // x - y - ancho - largo

        // subtitulos
        doc.text('FECHA:', 10, 50);
        doc.text('TIPO:', 100, 50);
        //doc.text('JUBILADO:', 100, 50);
        //doc.text('PENSIONADO:', 130, 50);
        //doc.text('SOBREVIVIENTE:', 165, 50);
        doc.text('CEDULA DE IDENTIDAD:', 10, 60);
        doc.text('FECHA DE NACIMIENTO:', 100, 60);
        doc.text('NOMBRES Y APELLIDOS:', 10, 70);

        doc.text('DIRECCIÓN:', 10, 80);
        doc.text('CORREO:', 100, 80);
        doc.text('ESTADO DE PROCEDENCIA:', 10, 100);
        doc.text('TELÉFONO 1:', 10, 110);
        doc.text('TELÉFONO 2:', 100, 110);
        doc.text('ORGANISMO QUE OTORGÓ EL BENEFICIO:', 10, 120);
        

        if(data.tipo_empleado_id > 2) {
            doc.text('CAUSANTE DE LA PENSIÓN:', 10, 140);
            
            if(tipo_empleado.includes(' SOBREVIVIENTE ')) {
                /*doc.text('PARENTESCO CON EL CAUSANTE:', 10, 170);

                doc.text('PENSIÓN COMPARTIDA: SI  ', 10, 180);
                doc.text('NO', 70, 180);
                doc.text('TIPO:', 120, 180);
                doc.text('NOMBRE(S):', 10, 190);
                doc.text('APELLIDO(S):', 100, 190);     */   
            }
        }

        
        doc.rect(30, 250, 55, 0); // x - y - ancho - largo
        doc.text('SELLO', 50, 255);
        doc.addImage("/sello.jpeg", 'JPEG', 30, 200, 55, 45);

        doc.addImage(qrCodeDataUrl, 'PNG', 130, 210, 60, 60); // Ajusta la posición y el tamaño según sea necesario

        doc.setTextColor(255, 0, 0); 
        doc.text('VALIDA TU REGISTRO', 142, 270);
        doc.setTextColor(0, 0, 0);
         
        doc.text('fedevidafinanzas2025@gmail.com', 80, 290);
        doc.text('Para información adicional llamar a los siguientes números telefónicos: 0212-8025166/5193/4947/5113/5139', 20, 280);

        // valores
        doc.setFont("arial", "normal");

        doc.text(fechaConvertida, 25, 50);
        doc.text(tipo_empleado, 115, 50);
        doc.text(data.cedula.toString() || 'Sin información', 60, 60);
        doc.text(data.fecha_nacimiento || 'Sin información', 145, 60);
        doc.text(data.nombre_completo || 'Sin información', 55, 70);
        doc.text(data.correo_electronico || 'Sin información', 120, 80);
        doc.text(data.direccion || 'Sin información', 10, 90);
        doc.text(lugar || 'Sin información', 60, 100);
        doc.text(data.telefono || 'Sin información', 35, 110);
        doc.text(telefono2 != null ? telefono2 : 'Sin información', 130, 110);
        doc.text(data.ministerio.nombre || 'Sin información', 10, 130);
        
        console.log(data)
        if(data.tipo_empleado_id > 2) {
            doc.text(data.causa_pension || 'Sin información', 10, 150);
        }

        // Agregar el código QR

        // En lugar de doc.save, usa doc.output
        const pdfOutput = doc.output('blob');
        const pdfUrl = URL.createObjectURL(pdfOutput);

        // Abre el PDF en una nueva pestaña
        window.open(pdfUrl);
    } catch (error) {
        console.log(error)
        let message = 'ocurrio un error al generar'
        //if(error.response.data) message = error.response.data.msg
        alerta('info', message, 'info')
    }
}