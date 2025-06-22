<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    nombre_box: '',
    descripcion: '',
});

const submit = () => {
    form.post(route('boxes.store'), {
        onFinish: () => form.reset('nombre_box', 'descripcion'), // Resetea el formulario después de enviar
    });
};
const props = defineProps({
    user:Object 
});
</script>

<template>
    <Head title="Crear Box" />
    <AppLayout title="Listado de Vehículos" :user="user">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Crear Nuevo Box</h2>
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

                            <div class="flex items-center justify-end mt-4">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Crear Box
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
      </AppLayout>
</template>