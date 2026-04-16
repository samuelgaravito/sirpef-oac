<script setup lang="ts">
import { computed } from "vue"
import { useRouter } from "vue-router"


const defaultLayout = "default"
const { currentRoute } = useRouter()
const layout = computed(
  () => `${currentRoute.value.meta.layout || defaultLayout}-layout`
)

</script>

<template>
    <component :is="layout">
  <div id="background">
    <div>
      <router-view v-slot="{Component}">
        <component :is="Component" :key="$route.path"></component>
      </router-view>
    </div>
  </div>
  </component>
</template>

<style lang="css" scoped>

#background{
  width: 100%;
  min-height: 100vh;
  background-color: #F4F5FA;
   /*background-color: #F4F5FA;
  background-image:url("@/assets/fondo.png");
  min-height: 90vh;
  margin:0 auto;
  padding-top: 5vh;*/
}


  .fade-enter-active,
  .fade-leave-active{
    transition: opacity 0.2s;
  }

  .fade-enter-from,
  .fade-leave-to{
    opacity:0;
  }

</style>
