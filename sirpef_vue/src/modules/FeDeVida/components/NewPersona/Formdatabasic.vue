<script setup lang="ts">
import Controls from '@/components/sirpef/form/Controls.vue';
import { getTypeCase } from '../../services';
import { onMounted, ref, watch} from 'vue';

const types = ref([])
const type = ref('0')

const props = defineProps<{
  emitForm: (event: Event) => void,
  step: number,
  values: any,
}>()

const getTypeCases = async () => {
  const response = await getTypeCase()
  types.value = response.data


}

onMounted(() => {
  getTypeCases()
})


watch(() => props.values, () => {

  if (props.values.tipo_caso_id && types.value.length > 0) {
    const father = types.value.find(e => e.id == props.values.tipo_caso_id)
    type.value = father ? father.tipo_caso_padre_id : '0'
  }

}, {deep: true})

</script>


<template>
  <form @submit.prevent="emitForm">
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 mt-3">
      <input name="cedula" class="w-full bg-gray-100 text-gray-900 mt-2 p-3 rounded-lg" maxlength="8" type="text"
        placeholder="Cédula*" v-model="values.cedula" required />
      <input name="nombre_completo" class="w-full bg-gray-100 text-gray-900 mt-2 p-3 rounded-lg" type="text"
        placeholder="Nombre Completo*" v-model="values.nombre_completo" required />

      <input name="fecha_nacimiento" class="w-full bg-gray-100 text-gray-900 mt-2 p-3 rounded-lg" type="date"
        placeholder="Nombre Completo*" v-model="values.fecha_nacimiento" required />

      <input name="telefono" class="w-full bg-gray-100 text-gray-900 mt-2 p-3 rounded-lg" type="text" maxlength="11"
        placeholder="Teléfono*" v-model="values.telefono" required />

      <div class="col-span-2">
        <label class="block">Tipo de caso</label>
        <select name="tipo_caso_id" v-model="type" required>
          <option :value="'0'">Sin selección</option>
          <option :value="type.id" v-for="type in types.filter(e => e.tipo_caso_padre_id == null)">{{ type.tipo }}
          </option>
        </select>

        <div class="mt-5 capitalize" v-if="type != '0'">
          <label class="block">Tipo de de sub-caso</label>
          <select name="tipo_caso_id" v-model="values.tipo_caso_id" required>
            <option :value="''">Sin selección</option>
            <option :value="type.id" v-for="type in types.filter(e => e.tipo_caso_padre_id == type)" class="capitalize">{{ type.tipo }}
            </option>
          </select>
        </div>

        <label class="block my-5">Descripción del caso</label>
        <textarea class="resize-none" name="descripcion" v-model="values.descripcion"></textarea>
      </div>
    </div>
    <Controls :step="step" />
  </form>
</template>