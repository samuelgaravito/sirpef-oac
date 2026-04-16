<script lang="ts" setup>

const props = defineProps<{
    enlaces: any[],
}>()
</script>

<template>
       <article class="relative w-full md:w-[320px] rounded-3xl shadow-lg h-[280px] overflow-hidden bg-white border mt-10 md:mt-0 cardlink"
            v-for="enlace in enlaces">
            <router-link class="border-gray-200" :to="enlace.path">
                <div class="px-10 pb-10 mt-10 h-16 mx-auto ">
                    <font-awesome-icon class="rounded-xl h-12 block text-[#4E85C8] w-full" :icon="enlace.icon" />
                </div>
                <div class="p-5">
                    <h5 class="mb-2 text-xl font-bold text-gray-900 text-center capitalize">{{ enlace.title }}</h5>
                    <p class="mb-3 font-normal text-gray-500 text-center ">{{ enlace.descripcion || enlace.title}}</p>
                </div>
            </router-link>

        </article>
</template>

<style scope>
.cardlink:hover {
    transform: scale(1.09);
    transition: all .2s ease-in-out;
}

.cardlink > div {
    border-radius: 0% 0% 100% 100%;
}


.cardlink:hover {
    transform: scale(1.09);
    transition: all .2s ease-in-out;
}

.cardlink >div {
    border-radius: 0% 0% 100% 100%;
}


.cardlink:hover {
    filter: blur(0px);
    transform: scale(1.1);
    opacity: 1;
}
</style>