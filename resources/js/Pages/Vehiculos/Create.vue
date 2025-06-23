<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue'; // Asegúrate de importar 'watch' si lo necesitas

const props = defineProps({
    tiposVehiculos: Array,
    marcas: Array, // Asegúrate de que esto se pase
    clientes: Array, // Para el select de clientes
    selectedUserId: [Number, String, null], // Nuevo prop para el ID del cliente
    selectedUserName: [String, null], // Nuevo prop para el nombre del cliente (opcional)
    isAdminContext: Boolean, // Nuevo prop para saber si es un admin logueado
});

const form = useForm({
    user_id: props.selectedUserId || '', // Precarga el user_id si viene de props
    tipo_vehiculo_id: '',
    marca: '',
    modelo: '',
    patente: '',
    anio: '',
    // modelo_id: '', // Si lo usas
});

// Variable reactiva para el mensaje inicial
const initialMessage = ref('');

// Si el selectedUserId viene, muestra un mensaje
if (props.selectedUserId && props.selectedUserName) {
    initialMessage.value = `Registrando un nuevo vehículo para ${props.selectedUserName}. Por favor, complete los datos.`;
} else if (props.selectedUserId) {
    initialMessage.value = `Registrando un nuevo vehículo para un cliente. Por favor, complete los datos.`;
} else if (props.isAdminContext) {
    initialMessage.value = `Registre un nuevo vehículo y asócielo a un cliente.`;
} else {
    // Podrías tener una lógica diferente si el propio cliente puede acceder a esta página
    initialMessage.value = `Registre su nuevo vehículo.`;
}


const submit = () => {
    form.post(route('vehiculos.store'), {
        onSuccess: () => form.reset(),
    });
};

// ... (posibles watches para modelos si los tienes)

</script>

<template>
    <AppLayout title="Registrar Vehículo">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Registrar Nuevo Vehículo</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div v-if="initialMessage" class="mb-4 text-blue-600 font-semibold">
                        {{ initialMessage }}
                    </div>

                    <form @submit.prevent="submit">
                        <div class="mb-4" v-if="!form.user_id">
                            <InputLabel for="user_id" value="Asignar a Cliente" />
                            <select
                                id="user_id"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                v-model="form.user_id"
                                required
                            >
                                <option value="">Seleccione un cliente</option>
                                <option v-for="cliente in props.clientes" :key="cliente.id" :value="cliente.id">
                                    {{ cliente.name }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.user_id" />
                        </div>
                        <input type="hidden" v-model="form.user_id" />


                        <div class="mb-4">
                            <InputLabel for="tipo_vehiculo_id" value="Tipo de Vehículo" />
                            <select
                                id="tipo_vehiculo_id"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                v-model="form.tipo_vehiculo_id"
                                required
                            >
                                <option value="">Seleccione un tipo</option>
                                <option v-for="tipo in props.tiposVehiculos" :key="tipo.id" :value="tipo.id">
                                    {{ tipo.nombre }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.tipo_vehiculo_id" />
                        </div>

                        <div class="mb-4">
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

                        <div class="mb-4">
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

                        <div class="mb-4">
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

                        <div class="mb-4">
                            <InputLabel for="anio" value="Año (Opcional)" />
                            <TextInput
                                id="anio"
                                type="number"
                                class="mt-1 block w-full"
                                v-model="form.anio"
                            />
                            <InputError class="mt-2" :message="form.errors.anio" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Registrar Vehículo
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>