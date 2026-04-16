import { getDay } from "@/utils/GetDay";
import Http from "@/utils/Http";
import { onMounted, ref } from "vue";

export default () => {
    const Coordenadas = ref([])
    const FechaOn = ref(getDay())
    const FechaHas = ref(getDay())
    const SectionFloat = ref(false)
    const Unidades_Ascritas = ref([]);


    const DataFiltered = ref({
        data: [],
        texto: "",
        total: -1,
        to: -1
      })

    const Top_Estados = ref({
        mayor: [],
        menor: [],
        todos: []
      });

    const GetCoordenadas = async () => {
        Coordenadas.value = []
        const response = await Http.get(`/api/registro/conteoRegistrosPorEstado/${FechaOn.value}/${FechaHas.value}`);
        console.log(response)
        Coordenadas.value = response.data
      }

    const GetTop5Estados = async () => {
        try {
          const response = await Http.get(`/api/registro/porcentaje-participacion-estado/${FechaOn.value}/${FechaHas.value}`);
          const info = response.data;
          const dataCopiaMayor = [...info];
          const dataCopiaMenor = [...info];
    
          const topMayor = dataCopiaMayor.sort((a, b) => b.porcentaje_participacion - a.porcentaje_participacion).splice(0, 5);
          const topMenor = dataCopiaMenor.sort((a, b) => a.porcentaje_participacion - b.porcentaje_participacion).splice(0, 5);
    
    
          Top_Estados.value = {
             mayor: topMayor,
            menor: topMenor,
            todos: info
          }
        } catch (error) {
          console.log(error)
        }
      }
    

      const FilterEstados = async (texto: string, total: number, estado: string) => {
        let estad_status
    
          /*
          2 = total participantes
          3 = sin participacion
        */
    
        if (texto == "Si participaron" || texto == "No participaron") {
          estad_status = texto == "Si participaron" ? 1 : 0
        }
        else{
          estad_status = texto ? 2 : 3
        }
        
        const estado_id = Top_Estados.value.todos.filter(e => e.name == estado)[0].id
        const {data} = await Http.get(`/api/registro/get_per_x_estado/${estado_id}/${estad_status}/${FechaOn.value}/${FechaHas.value}`)
        
        const dataMaped = data.map(e => {
          if(e.descripcion) {
            return {
              ...e,
            }
          } else {
            return {
              ...e,
              descripcion: e.voto == false ? 'Rechazado' : 'Pendiente'
            }
          }
         })
        return dataMaped

      }
    

      const loadData = async (texto: string, total: number, to?: number, unid?: string, uni_or_est?: string) => {
        SectionFloat.value = true
        let data = []

        data = await FilterEstados(texto, DataFiltered.value.total ? DataFiltered.value.total : total, unid)

      
        DataFiltered.value = {
            data,
            texto: texto ? texto : unid,
            total : data.length,
            to
          }

      
      }

      const hidden = (e?: PointerEvent) => {
        const target = e.target as HTMLElement
        if (e && target.tagName === "SECTION") {
          SectionFloat.value = false
          DataFiltered.value = {
            data: [],
            texto: "",
            total: -1,
            to: -1
          }
        } else {
          SectionFloat.value = true
        }
}


const LoadALl = () => {
  GetCoordenadas()
  GetTop5Estados()
  GetUnidAsc()
}

onMounted(() => {
  LoadALl()
})


  const PostUnidAsc = async (id: Number) => {
    const response = await Http.post("/api/registro/obtener-unidad-adscrita", {
      unidad_adscrita_id: id
    });
    LoadALl()
  }


  const SetDates = (e: any) => {
    const form = new FormData(e.currentTarget as HTMLFormElement)
    FechaOn.value = form.get("Desde") as string
    FechaHas.value = form.get("Hasta") as string
    LoadALl()
  }


  const GetUnidAsc = async () => {
    Unidades_Ascritas.value = []
    const response = await Http.get("/api/registro/unidades-adscritas");
    Unidades_Ascritas.value = response.data.unidad_adscrita
  }


    return {
        FechaOn,
        FechaHas,
        Coordenadas,
        Top_Estados,
        SectionFloat,
        DataFiltered,
        Unidades_Ascritas,
        hidden,
        loadData,
        SetDates,
        GetUnidAsc,
        PostUnidAsc,
        GetCoordenadas
    }
}