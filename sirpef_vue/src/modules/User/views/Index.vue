<script setup lang="ts">
// @ts-nocheck
import useIndex from "../composables/useIndex";
import AppPaginationB from "@/components/AppPaginationB.vue";
import Welcome from '@/components/sirpef/welcome.vue';
import AppBtn from "@/components/AppBtn.vue"
import AvatarIcon from "@/icons/AvatarIcon.vue"
import FormUser from "../components/FormUser.vue";
import { ref } from "vue";

const {
  errors,
  data,
  deleteRow,
  setSearch,
  setSort,
  loadUsers
} = useIndex()

const id_user_edit = ref('0')

</script>

<template>
  <Welcome title="Gestión de Usuarios" subtitle="Gestione los uduarios del sistema SIRPEF">
    <FormUser :id_user_edit="id_user_edit" />
  </Welcome>


  <div class="w-11/12 mx-auto">

    <div class="overflow-hidden panel mt-6 ">

      <div class="flex w-full rounded justify-between items-center my-6">
        <form class="flex w-full rounded" @submit.prevent="setSearch">
          <input class="miinput" type="text" name="search" v-model="data.search" placeholder="Buscar..." />
        </form>

        <div class="flex space-x-2">
          <AppLink class="btn btn-primary" to="/users/create">
            <span>Crear</span>
          </AppLink>
        </div>
      </div>

      <div class="table-data__wrapper">
        <table class="table-data">
          <thead>
            <tr class="">
              <th class="">
                Avatar
              </th>
              <th class="">
                <AppLink to="#" @click.prevent="setSort('name')">Nombre</AppLink>
              </th>
              <th class="">
                <AppLink to="#" @click.prevent="setSort('email')">Correo</AppLink>
              </th>
              <th class="">
                <AppLink to="#" @click.prevent="setSort('role')">Role</AppLink>
              </th>
              <th class="">Acción</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in data.rows" :key="row.id" class="">
              <td>
                <div class="inline-flex items-center space-x-2">
                  <img v-if="row.avatar" :src="row.avatar" class="w-10 h-10 rounded-full" alt="" />
                  <AvatarIcon class="w-10 h-10 text-gray-400 rounded-full" v-else />
                </div>
              </td>
              <td class="">
                {{ row.name }}
              </td>
              <td class="">
                {{ row.email }}
              </td>
              <td class="">
                {{ row.role_id }}
              </td>
              <td class="">
                <div class="flex items-center space-x-1">
                  <AppBtn class="btn btn-primary btn-xs" @click="id_user_edit = row.id">
                    Editar
                  </AppBtn>
                  <AppBtn @click="deleteRow(row.id)" class="btn btn-danger btn-xs">
                    Eliminar
                  </AppBtn>
                </div>
              </td>
            </tr>
            <tr v-if="data.rows.length === 0">
              <td class="" colspan="4">Usuarios no encontrados.</td>
            </tr>
          </tbody>
        </table>
      </div>
      <span v-if="Object.keys(errors).length > 0" class="text-red-500">{{ errors }}</span>
      <AppPaginationB v-if="data.links" :links="data.links" />
    </div>
  </div>
</template>
