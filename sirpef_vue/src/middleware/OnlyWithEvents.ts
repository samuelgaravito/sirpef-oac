import { useEventsName } from "@/stores/nameEvent";
import { alerta } from "@/utils/alert";
import { useRouter } from "vue-router";

export default async function whitEvent({to, next}) {
    
    return next()
    const event = useEventsName()
    if(!event.id) {
        const router = useRouter()
        router.push('events')
        return alerta("Info", "No has elegido un evento", "info")
    }
    next()
  }