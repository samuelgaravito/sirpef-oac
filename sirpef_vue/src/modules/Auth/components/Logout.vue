<script setup lang="ts">
import { computed } from "vue"
import { useAuthStore } from '@/modules/Auth/stores'
import LogoutIcon from "@/icons/LogoutIcon.vue";

const authStore = computed(() => useAuthStore())
const logout = () => authStore.value.logout()
</script>

<template>
  <button
    type="button"
    @click="logout"
    class="inline-flex items-center space-x-2 px-4 py-2 text-sm hover:bg-gray-400 w-full"
  >
    <span class="inline">Salir de SIRPEF</span>
    <LogoutIcon class="w-6 h-6 text-white" />
  </button>
</template>

