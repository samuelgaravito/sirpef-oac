import type { UserData } from "@/types/votos/voteOne"
import {ref } from "vue"
import Http from "@/utils/Http";
import {alerta} from "../../../utils/alert"

export default () => {

    const popup = ref<HTMLElement>(null)
    const result = ref({} as UserData)
    const popupStatus = ref(0)
    const Loading = ref(false)
    
    const GetUser = async (cedula: string) => {
        result.value = {} as UserData
        try {
            const res = await Http.get(`/api/registro/fedevida/search/${cedula}`);
            result.value = res.data
            return true
        } catch (error) {
            if (error.response.status != 200) {
                alerta("error", error.response.data.msg, "info")
            }
            return false
        }
    }

    const hidden = (e: PointerEvent) => {
        const target = e.target as HTMLElement
        if (e && target.tagName === "SECTION") {
            popupStatus.value = 0
        } else {
            if(target.tagName === "BUTTON") {
                const button = e.target as HTMLButtonElement
                if(button.name == "si") popupStatus.value = 1
            }         
            popup.value.style.display = "grid"
        }
    }


    return {
        result,
        popup,
        popupStatus,
        Loading,
        GetUser,
        hidden,
        alerta,
    }
}