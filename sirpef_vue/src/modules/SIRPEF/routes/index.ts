import auth from "@/middleware/auth"
import admin from "@/middleware/admin"
import whitEvent from "@/middleware/OnlyWithEvents"

export default [
  {
    path: "/oneVote/:ci?",
    name: "oneVote",
    meta: { middleware: [whitEvent, auth] },
    component: () => import("../views/Participacion/OneVote.vue").then(m => m.default)
  },

  {
    path: "/masive/:ci?",
    name: "masive",
    meta: { middleware: [whitEvent, auth] },
    component: () => import("../views/Participacion/MasiveLoad.vue").then(m => m.default)
  },

  {
    path: "/authpart/:ci?",
    name: "authpart",
    meta: { middleware: [whitEvent, auth] },
    component: () => import("../views/Participacion/autorizados.vue").then(m => m.default)
  },

  {
    path: "/loadforcsv",
    name: "loadforcsv",
    meta: { middleware: [whitEvent, auth] },
    component: () => import("../views/Participacion//MasiveCSV.vue").then(m => m.default)
  },
  {
    path: "/auditoria",
    name: "auditoria",
    meta: { middleware: [auth, admin] },
    component: () => import("../views/auditoria/index.vue").then(m => m.default)
  },

  {
    path: "/personal",
    name: "personal",
    meta: { middleware: [auth]},
    component: () => import("../../Personal/views/index.vue").then(m => m.default)
  },

  // eventos

  {
    path: "/events",
    name: "events",
    meta: { middleware: [auth] },
    component: () => import("../views/eventos/index.vue").then(m => m.default)
  },

]
