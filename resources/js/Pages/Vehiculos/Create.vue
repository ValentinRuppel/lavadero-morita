<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    vehiculos: Object,
    user: Object, // <-- ¡¡Añade esta línea!!
    errors: Object, // Para acceder a los errores de validación de Inertia
});

const form = useForm({
    marca: '',
    modelo: '',
    anio: '',
    patente: '',
    color: '',
    tipo_vehiculo_id: '',
    user_id: '',
});

const submit = () => {
    form.post(route('vehiculos.store'), {
        onFinish: () => form.reset(), // Resetea todo el formulario en finish
        onError: () => {
            // Inertia automáticamente maneja los errores y los hace disponibles en props.errors
            // Aquí puedes hacer focus en el primer campo con error, si lo deseas
            if (form.errors.marca) document.getElementById('marca').focus();
            // ... para otros campos
        },
    });
};
</script>

<template>
    <Head title="Crear Vehículo" />

    <AppLayout :user="user">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Crear Nuevo Vehículo</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6">Datos del Vehículo</h3>

                        <div v-if="Object.keys(form.errors).length > 0" class="mb-4">
                            <div class="font-medium text-red-600">¡Ups! Algo salió mal.</div>
                            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                                <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
                            </ul>
                        </div>

                        <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="marca" value="Marca" />
                                <TextInput
                                    id="marca"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.marca"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.marca" />
                            </div>

                            <div>
                                <InputLabel for="modelo" value="Modelo" />
                                <TextInput
                                    id="modelo"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.modelo"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.modelo" />
                            </div>

                            <div>
                                <InputLabel for="anio" value="Año" />
                                <TextInput
                                    id="anio"
                                    type="number"
                                    class="mt-1 block w-full"
                                    v-model="form.anio"
                                    min="1900"
                                    :max="new Date().getFullYear() + 1"
                                />
                                <InputError class="mt-2" :message="form.errors.anio" />
                            </div>

                            <div>
                                <InputLabel for="patente" value="Patente" />
                                <TextInput
                                    id="patente"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.patente"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.patente" />
                            </div>

                            <div>
                                <InputLabel for="color" value="Color" />
                                <TextInput
                                    id="color"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.color"
                                />
                                <InputError class="mt-2" :message="form.errors.color" />
                            </div>

                            <div>
                                <InputLabel for="tipo_vehiculo_id" value="Tipo de Vehículo" />
                                <select
                                    id="tipo_vehiculo_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    v-model="form.tipo_vehiculo_id"
                                    required
                                >
                                    <option value="">Seleccione un Tipo</option>
                                    <option v-for="tipo in tiposVehiculo" :key="tipo.id" :value="tipo.id">
                                        {{ tipo.nombre }} (${{ tipo.precio }})
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.tipo_vehiculo_id" />
                            </div>

                            <div>
                                <InputLabel for="user_id" value="Cliente" />
                                <select
                                    id="user_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    v-model="form.user_id"
                                    required
                                >
                                    <option value="">Seleccione un Cliente</option>
                                    <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">
                                        {{ cliente.name }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.user_id" />
                            </div>

                            <div class="col-span-full flex items-center justify-end mt-4">
                                <Link :href="route('vehiculos.index')" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                    Cancelar
                                </Link>
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Guardar Vehículo
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>