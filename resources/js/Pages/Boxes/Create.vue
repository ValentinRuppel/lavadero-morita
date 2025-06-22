<template>
  <div>
    <h1>Crear nuevo Box</h1>
    <form @submit.prevent="submit">
      <div>
        <label>Número del box:</label>
        <input v-model="form.numero" />
        <div v-if="form.errors.numero">{{ form.errors.numero }}</div>
      </div>
      <button type="submit">Guardar</button>
    </form>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
  numero: ''
});

function submit() {
  form.post('/boxes');
}
</script>