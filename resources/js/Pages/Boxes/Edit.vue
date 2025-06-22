<template>
  <div>
    <h1>Editar Box</h1>
    <form @submit.prevent="submit">
      <div>
        <label>Número del box:</label>
        <input v-model="form.numero" />
        <div v-if="form.errors.numero">{{ form.errors.numero }}</div>
      </div>
      <button
          type="submit"
          class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
      >
          {{ form.processing ? 'Actualizando...' : 'Actualizar Vehículo' }}
      </button>
    </form>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({ box: Object });

const form = useForm({
  _method: 'put',
  numero: props.box.numero
});

const submit = () => {
  form.post(route('boxes.update', props.box.id), {
  onSuccess: () => {
    // redirigir o mensaje
  },
  onError: (errors) => {
    console.error('Errores de validación:', errors)
  }
});
};
</script>
