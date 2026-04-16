<script setup lang="ts">
import Controls from '@/components/sirpef/form/Controls.vue';
import { onMounted, ref } from 'vue';
import Select from '@/components/Votos/Select.vue';
import Http from '@/utils/Http';

const props = defineProps<{
    FormEvent: (event: Event, ministerios: any[]) => void,
    step: number,
    values: any[]
}>()


const Ministerios = ref([] as any)

const selected_ministerios = ref(props.values)

const GetMinisterios = async () => {
    const response = await Http.get("api/registro/get-ministerios")
    Ministerios.value = response.data
}

const placeItem = (id: number) => {
    const index = Ministerios.value.findIndex(e => e.id == id);
    if (index !== -1) {
        const removedValue = Ministerios.value.splice(index, 1)[0];
        selected_ministerios.value.push(removedValue);

        const childs = findOficinas(Ministerios.value, id)

        if (childs.length > 0) selected_ministerios.value.push(...childs);
    
    }
}

function findOficinas(ministerios, id, oficinas = []) {
  const filteredMinisterios = ministerios.filter(e => e.ministerio_padre_id == id);
  oficinas.push(...filteredMinisterios);
  for (const e of filteredMinisterios) {
    const newOficinas = ministerios.filter(oficina => oficina.ministerio_padre_id == e.id);
    if (newOficinas.length > 0) {
      findOficinas(ministerios, e.id, oficinas);
      removeOficinas(ministerios, e.id)
    }
  }
  return oficinas;
}

const removeItem = (id: number) => {
    const index = selected_ministerios.value.findIndex(e => e.id == id);
    if (index !== -1) {
        const removedValue = selected_ministerios.value.splice(index, 1)[0];
        Ministerios.value.push(removedValue);
        removeOficinasFromSelected(selected_ministerios.value, id);
    }
}


function removeOficinasFromSelected(ministerios, id) {
    const filteredMinisterios = ministerios.filter(e => e.ministerio_padre_id == id);
    for (const e of filteredMinisterios) {
        const index = ministerios.findIndex(item => item.id == e.id);
        if (index !== -1) {
            Ministerios.value.push(ministerios[index]);
            ministerios.splice(index, 1);
        }
        removeOficinasFromSelected(ministerios, e.id);
    }
}
function removeOficinas(ministerios, id) {
  const filteredMinisterios = ministerios.filter(e => e.ministerio_padre_id == id);
  for (const e of filteredMinisterios) {
    ministerios.splice(ministerios.indexOf(e), 1);
    removeOficinas(ministerios, e.id);
  }
}


onMounted(async () => {
    await GetMinisterios()
    selected_ministerios.value.forEach(element => {
        const index = Ministerios.value.findIndex(e => e.id == element.id);
        if (index !== -1) {
            Ministerios.value.splice(index, 1)[0];
        }
    });
})

</script>

<template>
    <form @submit.prevent="(e: FormDataEvent) => FormEvent(e, selected_ministerios)">
      <Select :width="'w-full mx-auto my-4'" :Unidades_Ascritas="Ministerios.sort((a, b) => a.id - b.id)" :SelectValue="placeItem" :ActiveUnid="'Selecciona los participantes'"/>
    
      <input class="inputLabel" name="oficinas" placeholder="Oficinas"/>
      <div class="h-[150px] border flex flex-wrap gap-4 p-2 overflow-auto mb-10">
          <article v-if="selected_ministerios.length > 0" :title="oficina.nombre" class="flex min-w-[160px] h-[50px] border rounded-2xl overflow-hidden text-ellipsis gap-5 px-4" v-for="oficina in selected_ministerios" >
            <p class="capitalize self-center">{{oficina.nombre}}</p>
            <input name="oficinas" class="inputToDelete" type="button" value="x" title="eliminar" :id="oficina.id" @click="() => removeItem(oficina.id)">
          </article>
          <p class="text-gray-500 capitalize text-center w-full mt-[10%]" v-else>Sin selección</p>
      </div>

      <Controls :step="step"/>
    </form>
</template>

<style scoped>
    .inputToDelete {
        @apply bg-red-500 text-white rounded-full w-[30px] h-[30px] block cursor-pointer self-center;
    }

    .inputLabel {
        @apply pointer-events-none mb-5;
    }
</style>