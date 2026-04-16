<script lang="ts" setup>

    const props = defineProps<{
        title: string,
    }>()

</script>

<template>
    <article id="bannerTitle" class="bg-[#041E42] text-white p-12 text-center capitalize">
        <p class="font-bold text-4xl">{{title}}</p>
    </article>
</template>

<style scope>

#bannerTitle{
    height: 140px !important;
    background-position: right bottom 0px;
}
</style>