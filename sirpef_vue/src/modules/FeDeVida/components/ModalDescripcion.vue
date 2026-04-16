<script lang="ts" setup>
const props = defineProps<{
    descripcion: string
}>()

const $emit = defineEmits(['close'])
const hidden = (e?: any) => {
    const target = e.target as HTMLElement
    if (e && target.tagName === "SECTION") {
        $emit('close')
    }
}

</script>

<template>
    <section
        class="fixed inset-0 flex justify-center bg-black bg-opacity-30 overflow-hidden items-center z-[200] cursor-pointer md:rounded-2xl"
        @click="hidden">
        <article
            class="relative shadow-xl fadeout bg-white flex items-center justify-center cursor-default w-[90%] min-h-[300px] p-5 py-16 text-justify rounded-3xl "
            @click.prevent>
            <p class="absolute right-5 top-5 text-black md:text-white font-semibold text-xl z-[300] cursor-pointer transition-all md:top-4 hover:scale-105"
                @click="$emit('close')">X</p>

            <p>{{ descripcion }}</p>
        </article>
    </section>
</template>
