import { reactive, onMounted } from "vue"
import { onBeforeRouteUpdate } from "vue-router"
import useTableGrid from "@/composables/useTableGrid"
import useHttp from "@/composables/useHttp"
import EmployesServices from "../services/EmployesService"
import Swal from "sweetalert2"
import { alerta } from "@/utils/alert"
import Http from "@/utils/Http"

type Params =  string | string[][] | Record<string, string> | URLSearchParams | undefined

export default () => {
  const data = reactive({
    rows: [],
    links: [],
    page: "1",
    search: "",
    sort: "",
    direction: ""
  })

  const {  
    errors,
    getError     
  } = useHttp()

  const {
    route,
    router,
    setSearch,
    setSort, 
  } = useTableGrid(data, "/personal")

  const getEmployes = (routeQuery: string) => {  
    
    return EmployesServices.getEmployes(routeQuery)
      .then((response) => {
        data.rows = response.data.rows.data
        data.links = response.data.rows.links
        data.search = response.data.search
        data.sort = response.data.sort
        data.direction = response.data.direction   
      })
      .catch((error) => {
        console.log(error)
      })
  }

  const confirmacion = (id: number, text: string, to: string) => {
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
        to == "D" ? DeleteVoto(id) : ChanguePart(id)
      }    
    });
  }

  const ChanguePart = async (id: Number) => {
    try {
      const response = await Http.put(`/api/registro/${id}`, {});
      alerta('Enviado', response.data.message, 'success')
      router.push( { path: '/personal' } )        
    } catch (error) {
      console.log(error)
    }
  }

  const DeleteVoto = (rowId?: number) => {
    if (rowId === undefined)
      return
    return EmployesServices.deleteVoto(rowId)
        .then((response) => {
          alerta('Enviado', response.data.msg, 'success')
          errors.value = {}
          router.push( { path: '/personal' } )        
        })
        .catch((err) => {                
          console.log( err.response.data )
          errors.value = getError(err)
    })
  }



  onBeforeRouteUpdate(async (to, from) => {     
    if (to.query !== from.query) {        
      await getEmployes(
        new URLSearchParams(to.query as Params).toString()
      )
    }
  })

  onMounted(() => {
    getEmployes(
      new URLSearchParams(route.query as Params).toString()
    )
  })

  return {
    errors,
    data,
    router,
    confirmacion,
    setSearch,
    setSort
  }
}

