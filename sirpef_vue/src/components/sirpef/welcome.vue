<script setup lang="ts">
import { useRoute } from 'vue-router';


const welcome = defineProps<{
    title: string,
    subtitle: string,
    img?: string
}>()

const route = useRoute()

const classImg = () => {
    console.log(route.fullPath)
    if(route.fullPath == '/fedevida/form') return 'w-[80%]'
    return ''
}

</script>

<template>
    <section id="welcomeComp" class="bg-[#041E42] h-auto lg:h-[70vh] grid grid-cols-1 md:grid-cols-2 place-items-center px-[0vw] md:px-[5vw] lg:px-[10vw]">
        
        <div v-if="welcome.img" class="col-span-1 md:col-start-2 md:col-end-3 slide-in-right my-10">
            <img id="bannerIMG" alt="image_not_found" :class="classImg()" class="col-span-1 md:col-start-2 md:col-end-3 mx-auto" :src="`/screens/${welcome.img}`"/>
        </div>

         <div id="componet" v-else class="relative col-span-1 md:col-start-2 md:col-end-3 slide-in-right w-full my-10">
            <slot></slot>
        </div>

        <div id="textBanner" class="md:col-start-1 md:col-end-2 md:row-start-1 w-[80%] slide-in-left my-10" 
        :class="welcome.img ? '' : 'row-start-2'"
        
        >
            <p class="bg-[#9ca9bb4b] text-white font-bold w-auto md:w-[20%] p-2 rounded-md text-center">SIRPEF</p>
            <h1 class="text-5xl font-bold text-white mt-10">{{welcome.title}}</h1>
            <p class="text-2xl text-white mt-5 text-left">{{welcome.subtitle}}</p>
        </div>
    </section>
</template>

<style scope>

 #welcomeComp{
    background-position: right bottom 0px;
    background-image: url(/Bg-welcome.svg) !important;
    background-repeat: no-repeat;
    background-size: cover;
 }

 #bannerIMG{
    transform: perspective(1600px) rotateY(-25deg) translateX(-30px);
 }

 @media (max-width: 640px) {

    #componet{
        grid-row: 2/3;
    }

    #textBanner{
        grid-row: 1/2;
    }

    #bannerIMG{
    transform: perspective(1600px) rotateY(-25deg) translateX(-10px);
 }
 }

</style>