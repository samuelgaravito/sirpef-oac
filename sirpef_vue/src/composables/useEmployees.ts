import Swal from "sweetalert2";
import Http from "@/utils/Http/index"
import { alerta } from "@/utils/alert"
import {ref} from "vue"
import { GetIcons, MaleOrFemale } from "@/utils/icons";

export  default () => {

  const employees = ref([])

  const confirm = (id: number, LoadALl: Function, text: string, to: string) => {
    Swal.fire({
      title: "¿Estas seguro?",
      html: text,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#010c41",
      cancelButtonColor: "#ECA008",
      confirmButtonText: "Confirmar",
      cancelButtonText: "Cancelar"
    }).then((result) => {
      if (result.isConfirmed) {
        to == "D" ? DeleteVoto(id, LoadALl) : ChanguePart(id, LoadALl)
      }    
    });
  }


  const GetEmployes = async () => {
    const data = await Http.get(`/api/registro`);
    const response = data.data
  }


    const DeleteVoto = async (id: number, LoadALl : Function) => {
      try {
        const response = await Http.delete(`/api/registro/delete-vote/${id}`);
        alerta('Enviado', response.data.msg, 'success')
        LoadALl()
      } catch (error) {
        console.log(error)
      }
    }
  
    const ChanguePart = async (id: Number, LoadALl : Function) => {
      try {
        const response = await Http.put(`/api/registro/${id}`, {});
        alerta('Enviado', response.data.message, 'success')
        LoadALl()
      } catch (error) {
        console.log(error)
      }
    }

    return {
        ChanguePart,
        confirm,
        GetEmployes,
        employees
    }
}