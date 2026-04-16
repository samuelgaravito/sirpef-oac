<script setup lang="ts">
import Controls from '@/components/sirpef/form/Controls.vue';
import { onMounted, ref } from 'vue';
import Select from '@/components/Votos/Select.vue';
import Http from '@/utils/Http';

const props = defineProps<{
    FormEvent: (event: Event, eventos: any[]) => void,
    step: number,
    values: any[]
}>()


const eventos = ref([] as any)

const selected_eventos = ref(props.values)

const Geteventos = async () => {
    const response = await Http.get("api/registro/evento")

   const Map_Respo = response.data.map(e => {
        return {
            id: e.id,
            nombre: e.titulo,
        }
    })

    eventos.value = Map_Respo
}

const placeItem = (id: number) => {
    const index = eventos.value.findIndex(e => e.id == id);
    if (index !== -1) {
        const removedValue = eventos.value.splice(index, 1)[0];
        selected_eventos.value.push(removedValue);

        const childs = findOficinas(eventos.value, id)

        if (childs.length > 0) selected_eventos.value.push(...childs);
    
    }
}

function findOficinas(eventos, id, oficinas = []) {
  const filteredeventos = eventos.filter(e => e.menu_id == id);
  oficinas.push(...filteredeventos);
  for (const e of filteredeventos) {
    const newOficinas = eventos.filter(oficina => oficina.menu_id == e.id);
    if (newOficinas.length > 0) {
      findOficinas(eventos, e.id, oficinas);
    }
  }
  return oficinas;
}

const removeItem = (id: number) => {
    const index = selected_eventos.value.findIndex(e => e.id == id);
  if (index !== -1) {
    const removedValue = selected_eventos.value.splice(index, 1)[0];
    eventos.value.push(removedValue);

    removeOficinas(selected_eventos.value, id);
  }
}

function removeOficinas(eventos, id) {
  const filteredeventos = eventos.filter(e => e.menu_id == id);
  for (const e of filteredeventos) {
    eventos.splice(eventos.indexOf(e), 1);
    removeOficinas(eventos, e.id);
  }
}


onMounted(async () => {
    await Geteventos()
    selected_eventos.value.forEach(element => {
        const index = eventos.value.findIndex(e => e.id == element.id);
        if (index !== -1) {
            eventos.value.splice(index, 1)[0];
        }
    });
})

</script>

<template>
    <form @submit.prevent="(e: FormDataEvent) => FormEvent(e, selected_eventos)">
      <Select :width="'w-full mx-auto my-4'" :Unidades_Ascritas="eventos" :SelectValue="placeItem" :ActiveUnid="'Selecciona los participantes'"/>
    
      <input class="inputLabel" name="eventos" placeholder="Eventos"/>
      <div class="h-[150px] border flex flex-wrap gap-4 p-2 overflow-auto mb-10">
          <article v-if="selected_eventos.length > 0" :title="oficina.nombre" class="flex min-w-[160px] h-[50px] border rounded-2xl overflow-hidden text-ellipsis gap-5 px-4" v-for="oficina in selected_eventos" >
            <p class="capitalize self-center">{{oficina.nombre}}</p>
            <input name="eventos" class="inputToDelete" type="button" value="x" title="eliminar" :id="oficina.id" @click="() => removeItem(oficina.id)">
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