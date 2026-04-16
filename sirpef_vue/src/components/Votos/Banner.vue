<script setup lang="ts"> 

const props = defineProps<{
    text: string
}>()
</script>

<template>
    <!--section id="banner" class="relative">
        <article class="grid bg-[#010c41] p-30 h-96 relative z-10 rounded-r-full">
          <h2 class="text-white text-5xl font-bold te">{{ text }}</h2>
        </article>
          <img src="@/assets/banner.png" alt="">
    </section-->
</template>

<style scoped>

/* pasarlo a tailwind */

#contenedor #banner {
  margin-bottom: 8vh;
  z-index: 0;
}


#banner{
  position: fixed;
  width: 100%;
  bottom: 0%;
  overflow: hidden;
} 

#banner article{
  width: 65%;
  place-items: center;
}

#banner article h2{
  width: 80%;
  font-size: 4dvb;
}

#banner img{
  position: absolute;
  right: 20px;
  top:47%;
  width: 42%;
  transform: translateY(-42%);
}

@media (max-width: 900px) {
  
  #containerLogin{
  margin-top: 0vh;
  transform: translateY(-0%);
}
  #banner{
    position: relative;
    overflow: auto;
    width: 100%;
    overflow: hidden;
  }

  #banner article{
  width: 100%;
  border-radius: 0px 0px;
}

#banner article h2{
    font-size:20px;
  transform: translateY(-100%);
  width: 80%;
  text-align: center;
  }

 #banner img{
  position: absolute;
  left: 0;
  top:60%;
  width: 90%;
  transform: translateY(-30%);
  z-index:10;
} 
}

</style>