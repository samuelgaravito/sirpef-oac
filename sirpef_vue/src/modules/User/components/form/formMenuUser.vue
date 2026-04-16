<script setup lang="ts">
import Controls from '@/components/sirpef/form/Controls.vue';
import { onMounted, ref } from 'vue';
import Select from '@/components/Votos/Select.vue';
import Http from '@/utils/Http';

const props = defineProps<{
    FormEvent: (event: Event, menus: any[]) => void,
    step: number,
    values: any[]
}>()


const menus = ref([] as any)
const menus_to_see = ref([] as any)

const selected_menus = ref(props.values)

const Getmenus = async () => {
    const response = await Http.get("api/menus")
   const Map_Respo = response.data.data.map(e => {
        return {
            id: e.id,
            nombre: e.title,
            menu_id: e.menu_id
        }
    })

    menus_to_see.value = Map_Respo
    menus.value = Map_Respo

}


const placeItem = (id: number) => {
    const index = menus.value.findIndex(e => e.id == id);
    if (index !== -1) {
        const removedValue = menus.value.splice(index, 1)[0];
        selected_menus.value.push(removedValue);

        const childs = findOficinas(menus.value, id)

        if (childs.length > 0) selected_menus.value.push(...childs);
    
    }
}

function findOficinas(menus, id, oficinas = []) {
  const filteredmenus = menus.filter(e => e.menu_id == id);
  oficinas.push(...filteredmenus);
  for (const e of filteredmenus) {
    const newOficinas = menus.filter(oficina => oficina.menu_id == e.id);
    if (newOficinas.length > 0) {
      findOficinas(menus, e.id, oficinas);
    }
  }
  return oficinas;
}

const removeItem = (id: number) => {
    const index = selected_menus.value.findIndex(e => e.id == id);
  if (index !== -1) {
    const removedValue = selected_menus.value.splice(index, 1)[0];
    menus.value.push(removedValue);

    removeOficinas(selected_menus.value, id);
  }
}

function removeOficinas(menus, id) {
  const filteredmenus = menus.filter(e => e.menu_id == id);
  for (const e of filteredmenus) {
    menus.splice(menus.indexOf(e), 1);
    removeOficinas(menus, e.id);
  }
}


onMounted(async () => {
    await Getmenus()
    selected_menus.value.forEach(element => {
        const index = menus.value.findIndex(e => e.id == element.id);
        if (index !== -1) {
            menus.value.splice(index, 1)[0];
        }
    });
})

</script>

<template>
    <form @submit.prevent="(e: FormDataEvent) => FormEvent(e, selected_menus)">
      <Select :width="'w-full mx-auto my-4'" :Unidades_Ascritas="menus_to_see.filter(e => e.menu_id == null)" :SelectValue="placeItem" :ActiveUnid="'Selecciona los participantes'"/>
    
      <input class="inputLabel" name="menus" placeholder="Ventanas"/>
      <div class="h-[150px] border flex flex-wrap gap-4 p-2 overflow-auto mb-10">
          <article v-if="selected_menus.length > 0" :title="oficina.nombre" class="flex min-w-[160px] h-[50px] border rounded-2xl overflow-hidden text-ellipsis gap-5 px-4" v-for="oficina in selected_menus" >
            <p class="capitalize self-center">{{oficina.nombre}}</p>
            <input name="menus" class="inputToDelete" type="button" value="x" title="eliminar" :id="oficina.id" @click="() => removeItem(oficina.id)">
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