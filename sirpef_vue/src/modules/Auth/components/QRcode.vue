<script setup lang="ts">
import QRCodeVue3 from "qrcode-vue3";
import { useAuthStore } from '../stores';
import { onMounted, ref } from 'vue';

const store = useAuthStore()
const value = ref('')
const user  = ref({} as any)
const url = location.host


const loadUser = async () => {
    const resultUser = await store.getAuthUser()
    user.value = resultUser
    value.value = `http://${url}/oneVote/${resultUser.cedula}`
}

onMounted(() => loadUser())


</script>



<template>
        <div class="bg-white max-w-[400px] rounded-2xl shadow-lg mx-auto overflow-hidden">
           <article class="p-5 h-[500px] grid items-center">
               <div class="md:h-[300px] grid place-items-center">
                    <QRCodeVue3
                    :value="value"
                    :qrOptions="{ typeNumber: '0', mode: 'Byte', errorCorrectionLevel: 'Q' }"
                    :imageOptions="{ hideBackgroundDots: true, imageSize: 0.4, margin: 0 }"
                    :dotsOptions="{ type: 'square', color: '#000' }"
                    :cornersSquareOptions="{ type: 'square', color: '#000' }"
                    v-if="value"
                    />

                    <p class="text-3xl" v-else>Sin eventos</p>
               </div>

                <p class="mt-4 font-bold text-center">{{ user.name }}</p>

                <div class="mt-5 text-center bg-[#041E42] rounded-xl p-3 text-white whitespace-nowrap">
                    <p><strong>Cédula:</strong> {{ user.cedula }}</p>
                </div>

           </article>
        </div>
</template>