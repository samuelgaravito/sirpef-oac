<script lang="ts" setup>


const props = defineProps<{
    title: string,
    value: string,
    color: string,
}>()


const checkIcon = () => {
    if(props.title.toLowerCase() == 'casos cerrados') return "person-circle-check"
    else if(props.title.toLowerCase() == 'casos remitidos a otro') return "person-circle-check"
    else if(props.title.toLowerCase() == 'casos faltantes') return "person-circle-check"
    else if(props.title.toLowerCase() == 'casos con resultado directo') return "person-circle-check"
    else if(props.title.toLowerCase() == 'casos orientados') return 'person-circle-xmark'
    else if(props.title.toLowerCase() == 'casos en trámite') return 'person-circle-xmark'
    else if(props.title.toLowerCase() == 'casos resultado directo') return 'person-circle-exclamation'
    else if(props.title.toLowerCase() == 'casos remitidos otro') return 'person-circle-exclamation'
}

</script>

<template>
    <div class=" items-center justify-center bg-white border rounded-xl overflow-hidden shadow hover:scale-[1.05] transition-all cursor-pointer">
        <div  class="p-4 flex items-center w-[100%] h-[50%]" :style="{
            'background-color': color
          }">
          <font-awesome-icon :icon="checkIcon()" class="block scale-[1.4] mx-auto text-[#FFFF] h-[60px]" />
        </div>
        <div class="px-4 text-gray-700 h-[50%] grid place-items-center py-5 text-center w-[100%] capitalize">
            <h3 class="text-xl font-bold tracking-wider">{{ title }}</h3>
            <p class="text-3xl mt-2">{{value}}</p>
        </div>
    </div>
</template>