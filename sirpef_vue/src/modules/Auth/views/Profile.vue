<script setup lang="ts">
import { useAuthStore } from "../stores"
import ProfileUser from "../components/ProfileUser.vue"
import Welcome from '@/components/sirpef/welcome.vue';
import ProfileUpdatePassword from "../components/ProfileUpdatePassword.vue"
import ProfileVerifyEmail from "../components/ProfileVerifyEmail.vue"
import ProfileFileUpload from "../components/ProfileFileUpload.vue"
import AppSectionBorder from "@/components/AppSectionBorder.vue"
import AppSectionInfo from "@/components/AppSectionInfo.vue"

const store = useAuthStore()

const updateUser = () => {
  store.getAuthUser()
}

</script>

<template>
  <Welcome
    title="Perfil"
    subtitle="Ajuste y configure las opciones de su perfil"
    img="perfil.jpeg"
  />
  <div class="p-0 xl:px-0">
    
      <div class="max-w-7xl mx-auto py-5 sm:px-5 lg:px-5">
        <!--h1 class="font-bold text-xl mb-4">Mi Perfil</h1-->
      
        <div class="md:grid md:grid-cols-3 md:gap-6">      
          <AppSectionInfo 
            title="Información de su cuenta"
            description="Actualice la información de perfil y la dirección de correo electrónico de su cuenta."
          />
          <ProfileUser class="p-5 bg-base-200 border rounded shadow col-span-2" />
          <AppSectionBorder />                 
        </div>

        <div class="md:grid md:grid-cols-3 md:gap-6">
          <AppSectionInfo
            title="Actualizar contraseña"
            description="Asegúrese de que su cuenta esté usando una contraseña larga y aleatoria para mantenerse seguro."
          />
          <ProfileUpdatePassword class="p-5 bg-base-200 border rounded shadow col-span-2" />
          <AppSectionBorder />
        </div>
      </div>
    </div>
</template>
