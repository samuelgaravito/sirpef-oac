import auth from "@/middleware/auth"
import admin from "@/middleware/admin"

export default [{
    path: "/solicitudes",
    name: "solicitudes",
    meta: { middleware: [auth, admin] },
    component: () => import("@/modules/Autorizados/views/solicitudes.vue").then(m => m.default),
}
]
