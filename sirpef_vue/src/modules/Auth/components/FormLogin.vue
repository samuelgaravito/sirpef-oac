<script setup lang="ts">
  import { ref } from "vue"  
  
  defineProps({
    error: [Object, String],
    sending: Boolean
  })  
  const emit = defineEmits(['submit'])  
  const email = ref(null)  
  const password = ref(null)  
  const submit = async () => {
    emit('submit', {
      email: email.value,
      password: password.value
    })
  }
</script>

<template>
  <form @submit.prevent="submit" class="grid grid-cols-3 gap-5">
    <div>
      <p>Correo</p>
      <input
      type="email"
      label="Correo Electrónico"
      name="email"
      v-model="email"
      autocomplete="email"
      placeholder="Ingrese su correo"      
      class="mb-2 my-3"
      data-testid="email-input"      
    />
    </div>
    <div>
      <p>Contraseña</p>
      <input
      type="password"
      label="Contraseña"
      name="password"
      placeholder="Ingrese su contraseña"
      v-model="password"
      class="mb-4 my-3"
      data-testid="password-input"
    />    

    </div>

    <input
        type="submit"
        :text="sending ? 'Iniciando sesión...' : 'Iniciar sesión'"
        :isDisabled='sending'
        class="text-gray-700 w-3/4 mb-4 my-8 bg-[#ECA008] rounded-md px-3 py-2 col-start-3 col-end-4 font-bold hover:bg-[#010c41] hover:w-4/5 text-white mx-auto cursor-pointer transition-all"
        value="Iniciar sesión"
        />
    <AppFlashMessage :error='error' />
  </form>
</template>


<style scoped>

  input{
    border-radius: 20px;
    padding: 15px;
  }

  @media (max-width: 900px) {
    form{
      display: block;
    }

    input[type="submit"]{
      margin: 30px auto;
      display: block;
    }
  }
</style>