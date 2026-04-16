<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useAuthStore } from "@/modules/Auth/stores"
import Logout from "@/modules/Auth/components/Logout.vue"
import { useNotifications } from "@/stores/notifications"
import { alerta } from "@/utils/alert";

import { getAuthMenu } from "@/modules/Auth/services"
import type { Menu } from "@/types/Menu"
import { useEventsName } from "@/stores/nameEvent";

const dropdownOpen = ref(false)
const NotidownOpen = ref(false)
const store = computed(() => useAuthStore())

const NameEvent = useEventsName()

const UseNoti: any = useNotifications();


const LogoHeader = ref("SIRPEF")

const LoadNoti = () => {
  UseNoti.resetCount()
  NotidownOpen.value = !NotidownOpen.value
}

const menus = ref<Menu[]>([])

const displayNav = ref(false)

onMounted(async () => {
  const response = await getAuthMenu()
  menus.value = response.data.sort((a, b) => a.id - b.id)
  LoadInterval()

  await NameEvent.GetUserInfo()
})

const interval = ref(null)
const seeBtnBio = ref(null)

const LoadInterval = () => {
  interval.value = setInterval(() => {
    if (!NameEvent.name) return
    LogoHeader.value = LogoHeader.value == 'SIRPEF' ? NameEvent.name : 'SIRPEF'
  }, 3000);

}

onUnmounted(() => {
  clearInterval(interval.value);
  checkSiperfBio()
})


const checkSiperfBio = async () => {
  const urlBiometric = 'http://localhost:5000/formregis'
  try {
    await fetch(urlBiometric)
    seeBtnBio.value = true
  } catch (error) {
    seeBtnBio.value = false
  }
}

const openwindow = async () => {
  const urlBiometric = 'http://localhost:5000/formregis'
  window.open(urlBiometric, '_blank', 'width=500,height=500,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no');
}


const SVA_URL = import.meta.env.VITE_SVA_URL

</script>


<template>
  <header
    class="fixed z-[100] bg-[#041E42] w-full h-[10vh] border-solid border-b-[2px] border-[#0A264D] flex py-3 min-[957px]:justify-around justify-between max-[957px]:px-5 items-center"
    v-if="store.authUser">

    <div>
      <router-link
        class="grid text-white font-bold text-3xl gap-2 items-center w-[150px] overflow-hidden whitespace-nowrap"
        :to="{ name: 'dashboard' }">
        <p :class="LogoHeader == 'SIRPEF' ? 'slide-in-left' : 'text-[14px] min-w-[200px] max-w-[300px] slide-right'">{{
          LogoHeader }}</p>
      </router-link>
    </div>



    <div :class="displayNav ? 'block' : 'hidden'"
      class="fixed inset-0 top-[0vh] max-[957px]:bg-white z-[100] min-[957px]:static max-[957px]:slide-in-left min-[957px]:block">

      <button class="visible md:hidden w-[30px] h-10 scale-[2] mx-auto mr-[20px] block my-5"
        @click="displayNav = false">
        <font-awesome-icon icon="fa-solid fa-xmark" class="" />
      </button>

      <nav class="flex gap-12 relative max-[957px]:w-full max-[957px]:block">
        <button
          class="min-[957px]:h-[10vh] a_header max-[957px]:w-[80%] max-[957px]:mx-auto max-[957px]:block max-[957px]:p-5 max-[957px]:border-b"
          v-for="menu in menus">
          <div class="flex items-center gap-3 min-[957px]:text-white mx-auto">
            {{ menu.title }}
            <svg class="max-[957px]:fill-gray-300 fill-white" xmlns="http://www.w3.org/2000/svg" width="12" height="7"
              viewBox="0 0 12 7">
              <path d="M2.1 0 .8 1.325 6 6.5l5.2-5.175L9.9 0 6 3.9 2.1 0"></path>
            </svg>
          </div>

          <article
            class="min-[957px]:absolute bg-white w-96 top-[101%] min-[957px]:p-10 py-6 hidden z-[56] text-left min-[957px]:drop-shadow">
            <p class="text-[#B4B5BB] my-2">{{ menu.title }}</p>
            <hr>
            <ul class="grid mt-2 items-center gap-3  text-base w-full text-[#5b5c5f]">
              <li class="w-full my-[-5px]" v-for="submenu in menu.children_menus.sort((a, b) => b.id - a.id)"
                v-if="menu.children_menus.length > 0">
                <router-link class="hover:bg-[#E5EDF7] p-1 w-full block h-full capitalize" :to="{ name: submenu.path }"
                  @click="displayNav = false">{{ submenu.title }}</router-link>
              </li>
              <li v-else>
                <router-link class="hover:bg-[#E5EDF7] p-1 w-full block capitalize" :to="{ name: menu.path }"
                  @click="displayNav = false">{{ menu.title }}</router-link>
              </li>
            </ul>
          </article>
        </button>
      </nav>
    </div>

    <article class="text-white">
      <div class="flex items-center">
        <div class="relative flex gap-5">
          <button @click="LoadNoti" class="relative z-[56] block scale-135 h-8 w-10 rounded-full focus:outline-none"
            v-if="store.isAdmin">
            <div class="absolute top-0 left-[85%] text-sm">{{ UseNoti.count }}</div>
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
              class="w-full absolute inset-0 h-3/4 top-2" viewBox="0 0 52 59">
              <title>Group</title>
              <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" stroke-linecap="round"
                stroke-linejoin="round">
                <g id="Group" transform="translate(2.000000, 2.000000)" stroke="#FFFFFF" stroke-width="3">
                  <path
                    d="M23.6274,5 C19.384,5 15.3143,6.6857 12.3137,9.6863 L11.6863,10.3137 C8.6857,13.3143 7,17.384 7,21.6274 L7,24.5656 C7,29.9665 4.8545,35.1461 1.0355,38.9651 C0.3725,39.6281 0,40.5273 0,41.4649 C0,43.4173 1.5828,45.0001 3.5352,45.0001 L44.4648,45.0001 C46.4172,45.0001 48,43.4173 48,41.4649 C48,40.5273 47.6275,39.6281 46.9645,38.9651 C43.1455,35.1461 41,29.9665 41,24.5656 L41,21.6274 C41,17.384 39.3143,13.3143 36.3137,10.3137 L35.6863,9.6863 C32.6857,6.6857 28.616,5 24.3726,5 L23.6274,5 Z"
                    id="Path" />
                  <path
                    d="M17.7549,45 C17.3124,45.9211 17.0718,46.9434 17.0718,48 C17.0718,50.4752 18.3923,52.7624 20.5359,54 C22.6795,55.2376 25.3205,55.2376 27.4641,54 C29.6077,52.7624 30.9282,50.4752 30.9282,48 C30.9282,46.9434 30.6876,45.9211 30.245,45"
                    id="Path" />
                  <path d="M26,5.0829 L26,2 C26,0.8954 25.1046,0 24,0 C22.8954,0 22,0.8954 22,2 L22,5.0829" id="Path" />
                  <path
                    d="M47.9999,21.0001 C47.9999,17.8484 47.3791,14.7275 46.173,11.8157 C44.9669,8.9039 43.1991,6.2581 40.9705,4.0295"
                    id="Path" />
                  <path
                    d="M0,21.0001 C0,17.8484 0.6208,14.7275 1.8269,11.8157 C3.033,8.9039 4.8008,6.2581 7.0294,4.0295"
                    id="Path" />
                </g>
              </g>
            </svg>
          </button>

          <div v-if="NotidownOpen == true"
            class="absolute overflow-auto right-0 md:right-8 top-full z-[56] mt-3 w-screen max-w-md rounded-3xl bg-white shadow-lg ring-1 ring-gray-900/5 h-80">
            <div class="p-4" v-if="UseNoti.notifications.length > 0">
              <div v-for="noti in UseNoti.notifications"
                class="group relative flex items-center gap-x-6 rounded-lg p-4 text-sm leading-6">
                <div class="flex-auto">
                  <div class="block font-semibold text-gray-900">
                    Notificacion:
                    <span class="absolute inset-0"></span>
                  </div>
                  <p class="mt-1 text-gray-600">{{ noti }}</p>
                </div>
                <div class="flex h-11 w-11 flex-none items-center justify-center rounded-lg bg-gray-50">
                  <button type="button" @click="() => UseNoti.removeItem(noti)"
                    class="z-20 text-black hover:text-[#ECA008] cursor-pointer">
                    <font-awesome-icon icon="fa-solid fa-trash" />
                  </button>
                </div>
              </div>
            </div>

              <article class="text-center p-8 px-14 mx-auto text-gray-500 h-full grid place-content-center" v-else>
                <div class="flex justify-center mb-4">
                  <font-awesome-icon icon="bell-slash" class="text-6xl"/>
                </div>
                <p class="text-lg font-semibold">No hay notificaciones</p>
                <p class="text-gray-500 mt-2">Parece que no tienes ninguna notificación en este momento. Vuelve más
                  tarde para comprobar si hay actualizaciones.</p>
              </article>
          </div>

          <button class="hidden min-[957px]:block w-8 h-10 scale-150" title="Añadir nueva huella" @click="openwindow"
            v-if="store.authUser.isAdmin && seeBtnBio">
            <font-awesome-icon icon="fingerprint" />
            <p class="absolute bottom-0 right-0">+</p>
          </button>

          <button @click="dropdownOpen = !dropdownOpen"
            class="relative z-[100] block h-8 w-8 rounded-full overflow-hidden focus:outline-none">
            <img v-if="store.authUser && store.authUser.avatar" :src="store.authUser.avatar"
              class="w-10 h-10 rounded-full" alt="" />
            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
              class="w-8 h-10 rounded-full">
              <path
                d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z">
              </path>
            </svg>
          </button>



          <div v-show="dropdownOpen" @click="dropdownOpen = false"
            class="absolute right-0 mt-10 py-2 w-48 bg-white text-black rounded-md shadow-xl z-20 p-3">

            <p class="text-[#B4B5BB] font-bold">Cuenta</p>
            <a :href="`${SVA_URL}/profile`" class="block px-4 py-2 text-sm hover:bg-gray-400">
              {{ store.authUser ? store.authUser.name : 'Profile' }}
            </a>


            <Logout />
          </div>

          <button class="max-[957px]:block hidden w-8 h-10 scale-150" @click="displayNav = true">
            <font-awesome-icon icon="fa-solid fa-bars" />
          </button>
        </div>


      </div>
    </article>
  </header>
</template>

<style>
.a_header:hover {
  color: #4E85C8;
  border-bottom: #4E85C8 solid;
  transition: .2s ease;
}

.a_header:hover>article {
  display: block;
}

.slide-right {
  animation: slideInRight 4s ease-out;
}

@keyframes slideInRight {
  0% {
    transform: translateX(100%);
  }

  100% {
    transform: translateX(-100%);
  }
}
</style>