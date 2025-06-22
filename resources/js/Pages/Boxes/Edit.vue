
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    box: Object,
});

const form = useForm({
    _method: 'put',
    nombre_box: props.box.nombre_box,
    descripcion: props.box.descripcion,
    estado: props.box.estado,
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

<template>
    <Head :title="'Editar Box: ' + props.box.nombre_box" />

    <AppLayout title="Listado de Vehículos" :user="user">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Box: {{ props.box.nombre_box }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="max-w-lg mx-auto">
                            <div>
                                <InputLabel for="nombre_box" value="Nombre del Box" />
                                <TextInput
                                    id="nombre_box"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.nombre_box"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.nombre_box" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="descripcion" value="Descripción (Opcional)" />
                                <textarea
                                    id="descripcion"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                    v-model="form.descripcion"
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.descripcion" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="estado" value="Estado del Box" />
                                <select
                                    id="estado"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                    v-model="form.estado"
                                >
                                    <option value="activo">Activo</option>
                                    <option value="mantenimiento">Mantenimiento</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.estado" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <button
                                    type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                >
                                    {{ form.processing ? 'Actualizando...' : 'Actualizar Vehículo' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>