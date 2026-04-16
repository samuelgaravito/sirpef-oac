import { onMounted, ref } from 'vue';
import { jsPDF } from "jspdf";
import domtoimage from 'dom-to-image-more';
import { getDay } from '@/utils/GetDay';

export default (props: any) => {

    const fecha_desde = ref(getDay())
    const fecha_hasta = ref(getDay())
    const selectedUnid = ref('ninguna')

    const Listsee = ref(false)



    const SeeFilter = (e: MouseEvent) => {
        const element = e.target as HTMLButtonElement
        if (element.id != "btn_filter") return
        Listsee.value = !Listsee.value
    }

    const GetUnidAsc = () => {
        let actives = props.Unidades_Ascritas.find(element => element.active === 0)
        selectedUnid.value = actives ? actives.nombre : 'Ninguna'
    }

    const print = async () => {
        const node = document.getElementById('Estadisticas');
        node.classList.add("ExportTemp")


        const unid = selectedUnid.value.includes('Todos') || selectedUnid.value.includes('Todas ')
            ? selectedUnid.value : selectedUnid.value.split("-").length > 0
                ? selectedUnid.value.split("-")[0] : "Resumen";


        setTimeout(async () => {
            const dataUrl: string = await domtoimage
                .toPng(node, {
                    width: 1494,
                    height: 664 + 50,
                    quality: 1,
                })

            const doc = new jsPDF({
                orientation: 'l',
                format: [250, 440]
            });

            doc.addFont('Helvetica-Bold.ttf', 'helvetica', 'bold');

            doc.text(`Fecha: ${getDay()}`, 25, 15);
            doc.text(`Oficina: ${selectedUnid.value}`, 80, 15, { maxWidth: 360 });

            //doc.addImage("./bannerMPPEF.png", 'PNG', 60, 0, 300, 25);

            doc.addImage(dataUrl, 'PNG', 25, 25, 400, 195);

            doc.save(`${unid}export.pdf`);
            //node.classList.remove("ExportTemp")
            location.reload()
        }, 600);


    };


    return {
        GetUnidAsc,
        print,
        SeeFilter,
        selectedUnid,
        fecha_desde,
        fecha_hasta,
        Listsee,
    }

}