<script setup lang="ts">
import Header from "./Header.vue"
import Sidebar from "./Sidebar.vue"
import PageFoot from "./PageFoot.vue"
import useWebsockets from "@/composables/votos/useWebsockets"
import { onMounted, onUnmounted } from "vue"

const { ConnectSocket, DisconnectSocket, GetUser } = useWebsockets()


onMounted(async () => {
  await GetUser()
  ConnectSocket()
})
onUnmounted(() => {
  DisconnectSocket()
})
</script>

<template>
  <Header />
  <div class="section pt-[10vh]">
    <div id="Containerdelmain" class="flex-1 flex flex-col">
      <main id="ContainerMain" class="flex-1 overflow-y-auto">
        <div class="">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<style>
#ContainerMain{
  overflow-x: hidden;
}

#Containerdelmain{
  overflow: hidden;
}
</style>
