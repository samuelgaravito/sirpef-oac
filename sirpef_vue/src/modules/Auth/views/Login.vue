<script setup lang="ts">  
  import Welcome from "@/components/sirpef/welcome.vue"
  import FormLogin from "../components/FormLogin.vue";
  import { useLogin } from '../composables/useLogin'  
  import Banner from "@/components/Votos/Banner.vue"
  import {onMounted} from "vue"

  const { login, sending, error } = useLogin()


onMounted(() => {
  login()
})

</script>

<template>
  <section class="bg-[#041E42] h-[100vh] grid items-center justify-center">
    <div class="text-white text-center scale-[1.5]">
      <div class="three-body">
        <div class="three-body__dot"></div>
        <div class="three-body__dot"></div>
        <div class="three-body__dot"></div>
      </div>
      <p class="font-semibold mt-5">Iniciando sesión</p>
    </div>
  </section>
</template>


<style scoped>
.three-body {
 --uib-size: 35px;
 --uib-speed: 0.8s;
 --uib-color: #FFF;
 position: relative;
 display: inline-block;
 height: var(--uib-size);
 width: var(--uib-size);
 animation: spin78236 calc(var(--uib-speed) * 2.5) infinite linear;
}

.three-body__dot {
 position: absolute;
 height: 100%;
 width: 30%;
}

.three-body__dot:after {
 content: '';
 position: absolute;
 height: 0%;
 width: 100%;
 padding-bottom: 100%;
 background-color: var(--uib-color);
 border-radius: 50%;
}

.three-body__dot:nth-child(1) {
 bottom: 5%;
 left: 0;
 transform: rotate(60deg);
 transform-origin: 50% 85%;
}

.three-body__dot:nth-child(1)::after {
 bottom: 0;
 left: 0;
 animation: wobble1 var(--uib-speed) infinite ease-in-out;
 animation-delay: calc(var(--uib-speed) * -0.3);
}

.three-body__dot:nth-child(2) {
 bottom: 5%;
 right: 0;
 transform: rotate(-60deg);
 transform-origin: 50% 85%;
}

.three-body__dot:nth-child(2)::after {
 bottom: 0;
 left: 0;
 animation: wobble1 var(--uib-speed) infinite
    calc(var(--uib-speed) * -0.15) ease-in-out;
}

.three-body__dot:nth-child(3) {
 bottom: -5%;
 left: 0;
 transform: translateX(116.666%);
}

.three-body__dot:nth-child(3)::after {
 top: 0;
 left: 0;
 animation: wobble2 var(--uib-speed) infinite ease-in-out;
}

@keyframes spin78236 {
 0% {
  transform: rotate(0deg);
 }

 100% {
  transform: rotate(360deg);
 }
}

@keyframes wobble1 {
 0%,
  100% {
  transform: translateY(0%) scale(1);
  opacity: 1;
 }

 50% {
  transform: translateY(-66%) scale(0.65);
  opacity: 0.8;
 }
}

@keyframes wobble2 {
 0%,
  100% {
  transform: translateY(0%) scale(1);
  opacity: 1;
 }

 50% {
  transform: translateY(66%) scale(0.65);
  opacity: 0.8;
 }
}

</style>