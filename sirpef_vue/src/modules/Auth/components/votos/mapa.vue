<script setup lang="ts">
import { onMounted, ref, watch, onUnmounted } from 'vue';
import L from 'leaflet';
import type {Coordenadas} from "@/types/votos/Coordenadas"

const map = ref()

const props = defineProps<{
  Coordenadas: Coordenadas[],
  loadData: Function,
  id_Html: string 
}>()



onUnmounted(() => {
  delete (document as any).LoadDataMapa;
});


onMounted(() => {
  (document as any).LoadDataMapa = (texto: string, total: number, to: number, unidad: string) => props.loadData(texto, total, to, unidad);

  map.value = L.map(props.id_Html).setView([10.4982047, -66.8870176], 10);
  map.value.setMinZoom(6)
  map.value.scrollWheelZoom.disable()


  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  }).addTo( map.value);
  /*map.setMinZoom(13)*/
  
 /* map.setMaxBounds([
      [10.5254, -66.9498], // Coordenadas of Caracas
      [10.4625, -66.8358]  // Coordenadas del suroeste
  ])*/


  /*const customMarker = new L.Icon({
  iconUrl: 'https://cdn.pixabay.com/photo/2014/04/03/10/03/google-309740_1280.png',
  iconSize: [28, 45], // tamaño de la imagen
});*/


  

  /*const marker = L.marker([10.4982047, -66.8870176]/*, {icon: customMarker}).addTo(map);
  const marker2 = L.marker([10.5029299,-66.904541]).addTo(map);

  marker.bindPopup("<h3>casa<h3>")
  marker2.bindPopup("<h3>aaaa<h3>")*/
});

watch(() => props.Coordenadas.length, (newLength, oldLength) => {
  map.value.eachLayer((layer) => {
      if (layer instanceof L.Marker) {
        layer.remove();
      }
    });
    if (newLength > 0) {
    props.Coordenadas.forEach(element => {

      if (element.coordenadas[0] == null || element.coordenadas[1] == null) return

    const marker = L.marker(element.coordenadas).addTo( map.value);
      marker.bindPopup(`<h3 class="bold text-center">${element.nombre_estado}</h3> <br> 
           <div class="w-[300px] relative">
            <div class="bg-[#F0F0F0] w-full grid grid-cols-6 gap-10 text-center items-center my-3 border mx-auto justify-beeteen rounded-md overflow-hidden"">
            <h1 onclick="LoadDataMapa('Total participación', ${element.total_registros}, 2, '${element.nombre_estado}')" style="background-color: rgb(49, 169, 217);" class="w-16 p-5 text-white font-bold col-start-1 col-end-2 hover:cursor-pointer hover:text-xl transition-all"">${element.total_registros}</h1>
            <p class="text-sm w-max col-start-3 col-end-5 text-gray-600 capitalize text-center">Total participación</p>
          </div>


          <div class="w-full relative">
            <div class="bg-[#F0F0F0] w-full grid grid-cols-6 gap-10 text-center items-center my-3 border mx-auto justify-beeteen rounded-md overflow-hidden"">
            <h1 onclick="LoadDataMapa('Si participaron', ${element.votos_verdaderos}, 2, '${element.nombre_estado}')"  style="background-color: rgb(200, 0, 54);" class="w-16 p-5 text-white font-bold col-start-1 col-end-2 hover:cursor-pointer hover:text-xl transition-all"">${element.votos_verdaderos}</h1>
            <p class="text-sm w-max col-start-3 col-end-5 text-gray-600 capitalize text-center">Si participaron</p>
          </div>

          <div class="w-full relative">
            <div class="bg-[#F0F0F0] w-full grid grid-cols-6 gap-10 text-center items-center my-3 border mx-auto justify-beeteen rounded-md overflow-hidden"">
            <h1 onclick="LoadDataMapa('No participaron', ${element.votos_falsos}, 2, '${element.nombre_estado}')" style="background-color: rgb(26, 73, 104);" class="w-16 p-5 text-white font-bold col-start-1 col-end-2 hover:cursor-pointer hover:text-xl transition-all"">${element.votos_falsos}</h1>
            <p class="text-sm w-max col-start-3 col-end-5 text-gray-600 capitalize text-center">No participaron</p>
          </div>
      `)
      
      /*marker.on('mouseover', () => {
            marker.openPopup();
          });

          marker.on('mouseout', () => {
            marker.closePopup();
          });*/
    });
    } 
  });
</script>

<template>
  <div class="map" :id="id_Html" @focusout="map.scrollWheelZoom.disable()" @focusin="map.scrollWheelZoom.enable()"></div>
</template>


<style scoped>

.map{
  width: 100%;
  height: 100%;
  margin: 0 auto;
  z-index: 0;
  border-radius: 30px;
}

</style>