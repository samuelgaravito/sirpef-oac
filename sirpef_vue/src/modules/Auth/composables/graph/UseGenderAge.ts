import Http from "@/utils/Http";
import { onMounted, ref } from "vue";

export default (FechaOn: string, FechaHas: string) => {
    const PieSex = ref({} as any);
    const DonutSex = ref({} as any);
    const TableAges = ref({} as any);

    const GetSexAge = async () => {
        const response = await Http.get(`/api/registro/countSexAge/${FechaOn}/${FechaHas}`);
        TableAges.value = response.data
    }

  const GetSex = async () => {
    const response = await Http.get(`/api/registro/countSex/${FechaOn}/${FechaHas}`);
    const response2 = await Http.get(`/api/registro/countgenero`);

    PieSex.value = {
      data: response.data,
      active: PieSex.value.active || "F"
    }
    //[response.data.M.Si || 1, response.data.F.Si || 1],
    // [response.data.total_masculino, response2.data.total_femenino]
    DonutSex.value = {
      labels: ["Masculino", "Femenino"],
      datasets: [{
        label: 'Resultados de Participación',
        data: [response.data.M.Si || 0, response.data.F.Si || 0], 
        backgroundColor: ["#31A9D9", "#C40B0B"],
      }]
    }
    ChangueSexData(PieSex.value.active)
  }

  const ChangueSexData = (gender: string) => {
    const info = PieSex.value.data[gender]
    const stringLabel = gender == "F" ? "Femenino" : "Masculino"   
    const backgroundColor =  gender == "F" ? ["#A70505", "#C80036", "#C40B0B"] : ["#1c55ff", "#879ddd", "#0d47a1"]

    const data = {
        labels: ["Si Participaron", "No Participaron", `Participantes Faltantes`],
        datasets: [{
          label: 'Han participado un total',
          data: [info.Si, info.No, info.faltantes],
          backgroundColor
        }]
      }

      PieSex.value = {
        data: PieSex.value.data,
        active: gender,
        graph: data,
        backgroundColor
      }
  }


  onMounted(() => {
    GetSex()
    GetSexAge()
  })

    return {
        PieSex,
        ChangueSexData,
        DonutSex,
        TableAges,
        GetSex,
        GetSexAge
    }
}