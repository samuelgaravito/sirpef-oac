<script setup lang="ts">
import Controls from '@/components/sirpef/form/Controls.vue';
import { useAuthStore } from '@/modules/Auth/stores';
import useGeo from '@/modules/Personal/composables/useGeo';
import { onMounted} from 'vue';

const {
  estados,
  municipios,
  parroquias,
  Unidades,
  paises,
  getEstados,
  getMunicipios,
  getParroquias,
  getUnidadesPublica,
  getPaises
} = useGeo()

const store = useAuthStore()


const props = defineProps<{
    emitForm: (event: Event) => void,
    step: number,
    values: any,
}>()

onMounted(() => {
  getEstados()
  getUnidadesPublica()
  getPaises()
  getMunicipios(props.values.estado_id)
  getParroquias(props.values.municipio_id)
})

</script>


<template>
    <form @submit.prevent="emitForm">
        <div class="lg:grid-cols-2 lg:grid my-5 gap-x-10">
            <div>
                <label class="block my-2">Oficina</label>
                <select name="ministerio_id" v-model="values.ministerio_id" :disabled="store.isAdmin ? false : true">
                    <option :value="null">No seleccionado</option>
                    <option v-for="unidad in Unidades" :value="unidad.id">{{ unidad.nombre }}</option>
                </select>
            </div>
            <div>
                <label class="block my-2">Pais</label>
                <select name="pais_id" v-model="values.pais_id" required>
                    <option value="">No seleccionado</option>
                    <option v-for="pais in paises" :value="pais.id">{{ pais.pais }}</option>
                </select>
            </div>
        </div>


        <div class="lg:grid-cols-2 lg:grid my-5 gap-x-10" v-if="values.pais_id == '170'">
            <div>
                <label class="block my-2">Estado</label>
                <select name="estado_id" v-model="values.estado_id" @change="() => getMunicipios(values.estado_id)" required>
                    <option :value="null">No seleccionado</option>
                    <option v-for="estado in estados" :value="estado.id">{{ estado.estado }}</option>
                </select>
            </div>
            <div>
                <label class="block my-2">Municipio</label>
                <select name="municipio_id" v-model="values.municipio_id" @change="() => getParroquias(values.municipio_id)" required>
                    <option :value="null">No seleccionado</option>
                    <option v-for="muni in municipios" :value="muni.id">{{ muni.municipio }}</option>
                </select>
            </div>
        </div>

        <div class="lg:grid-cols-2 lg:grid my-5 gap-x-10" v-if="values.pais_id == '170'">
            <div>
                <label class="block my-2">Parroquia</label>
                <select name="parroquia_id" v-model="values.parroquia_id" required>
                    <option :value="null">No seleccionado</option>
                    <option v-for="parroquia in parroquias" :value="parroquia.id">{{ parroquia.parroquias }}</option>
                </select>
            </div>
        </div>

        <div class="lg:grid-cols-2 lg:grid my-5 gap-x-10">
            <div>
                <label class="block my-2">Correo</label>
                <input type="email" v-model="values.correo_electronico" placeholder="email" required/>
            </div>

            <div>
                <label class="block my-2">Fecha de nacimiento</label>
                <input type="date" v-model="values.fecha_nacimiento" placeholder="fecha_nacimiento" required/>
            </div>
        </div>


        <label class="block my-2">Dirección</label>
        <textarea name="direccion" class="resize-none h-[100px] mb-10" placeholder="Ingrese el direccion" v-model="values.direccion" required></textarea>


        
        <Controls :step="step"/>
    </form>
</template>