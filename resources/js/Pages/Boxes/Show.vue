<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue'; // Asegúrate de tener este componente
import InputLabel from '@/Components/InputLabel.vue'; // Asegúrate de tener este componente
import PrimaryButton from '@/Components/PrimaryButton.vue'; // Asegúrate de tener este componente
import TextInput from '@/Components/TextInput.vue'; // Asegúrate de tener este componente

import { Head, Link, usePage, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    box: Object,
    tiposLavado: Array,
    vehiculos: Array, // Lista de vehículos existentes
    adminId: Number, // El ID del administrador logueado
    user: Object, // Para AppLayout
});

// Mensajes flash
const page = usePage();

// Formulario para iniciar servicio
const startServiceForm = useForm({
    box_id: props.box.id,
    vehiculo_patente: '',
    vehiculo_marca: '',
    vehiculo_modelo: '',
    tipo_lavado_id: '',
    administrador_id: props.adminId,
});

// Formulario para finalizar servicio (no necesita muchos campos aquí, solo el envío)
const finishServiceForm = useForm({});

// Referencia para alternar entre seleccionar vehículo existente o crear uno nuevo
const newVehicle = ref(false); // true para nuevo vehículo, false para seleccionar existente

// Watcher para limpiar campos de vehículo si se cambia entre existente/nuevo
watch(newVehicle, (newValue) => {
    if (!newValue) { // Si vuelve a seleccionar existente
        startServiceForm.vehiculo_marca = '';
        startServiceForm.vehiculo_modelo = '';
    } else { // Si va a crear uno nuevo, limpia la patente por si acaso
         startServiceForm.vehiculo_patente = ''; // Limpiar si se selecciona "nuevo" después de haber escrito una patente
    }
});


const submitStartService = () => {
    // Si no es un nuevo vehículo, aseguramos que marca y modelo sean nulos para la validación
    if (!newVehicle.value) {
        startServiceForm.vehiculo_marca = null;
        startServiceForm.vehiculo_modelo = null;
    }

    startServiceForm.post(route('servicios.iniciar'), {
        onSuccess: () => {
            // Limpiar el formulario después de un inicio exitoso
            startServiceForm.reset('vehiculo_patente', 'vehiculo_marca', 'vehiculo_modelo', 'tipo_lavado_id');
            newVehicle.value = false; // Volver al estado por defecto
        },
        onError: (errors) => {
            console.error('Error al iniciar servicio:', errors);
            // Los errores de validación se manejarán automáticamente por InputError
        }
    });
};

const submitFinishService = () => {
    if (confirm('¿Estás seguro de que quieres finalizar este servicio?')) {
        finishServiceForm.put(route('servicios.finalizar', props.box.servicio_en_curso.id), {
            onSuccess: () => {
                console.log('Servicio finalizado!');
            },
            onError: (errors) => {
                console.error('Error al finalizar servicio:', errors);
            }
        });
    }
};

const submitCancelService = () => {
    if (confirm('¿Estás seguro de que quieres cancelar este servicio? El box volverá a estar activo.')) {
        finishServiceForm.post(route('servicios.cancelar', props.box.servicio_en_curso.id), { // Usamos post para cancelar
            onSuccess: () => {
                console.log('Servicio cancelado!');
            },
            onError: (errors) => {
                console.error('Error al cancelar servicio:', errors);
            }
        });
    }
};
</script>

<template>
    <Head :title="'Detalle del Box: ' + props.box.nombre_box" />

    <AppLayout :user="user">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detalle del Box: {{ props.box.nombre_box }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="successMessage" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p class="font-bold">Éxito</p>
                    <p>{{ successMessage }}</p>
                </div>
                <div v-if="errorMessage" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p class="font-bold">Error</p>
                    <p>{{ errorMessage }}</p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-2xl font-bold mb-4">{{ props.box.nombre_box }}</h3>
                        <p class="text-gray-700 mb-2">Descripción: {{ props.box.descripcion || 'Sin descripción' }}</p>
                        <div class="mb-4">
                            <span :class="{
                                'px-3 py-1 rounded-full text-sm font-semibold inline-block': true,
                                'bg-green-100 text-green-800': props.box.estado === 'activo',
                                'bg-red-100 text-red-800': props.box.estado === 'ocupado',
                                'bg-yellow-100 text-yellow-800': props.box.estado === 'mantenimiento',
                            }">
                                Estado: {{ props.box.estado.charAt(0).toUpperCase() + props.box.estado.slice(1) }}
                            </span>
                        </div>

                        <div v-if="props.box.servicio_en_curso && props.box.estado === 'ocupado'" class="mt-6 p-4 bg-indigo-100 rounded-lg border border-indigo-300">
                            <h4 class="text-lg font-semibold text-indigo-800 mb-2">Servicio en Curso:</h4>
                            <p class="text-indigo-700">Vehículo: {{ props.box.servicio_en_curso.vehiculo?.marca }} {{ props.box.servicio_en_curso.vehiculo?.modelo }} ({{ props.box.servicio_en_curso.vehiculo?.patente }})</p>
                            <p class="text-indigo-700">Tipo de Lavado: {{ props.box.servicio_en_curso.tipo_lavado?.nombre_lavado }}</p>
                            <p class="text-indigo-700">Precio Base: ${{ props.box.servicio_en_curso.tipo_lavado?.precio }}</p>
                            <p class="text-indigo-700">Inició: {{ new Date(props.box.servicio_en_curso.fecha_hora_inicio).toLocaleString() }}</p>
                            <p class="text-indigo-700">Asignado por: {{ props.box.servicio_en_curso.administrador?.name }}</p>
                            
                            <div class="mt-4 flex space-x-2">
                                <PrimaryButton @click="submitFinishService" :disabled="finishServiceForm.processing">
                                    Finalizar Servicio
                                </PrimaryButton>
                                <PrimaryButton @click="submitCancelService" class="bg-gray-500 hover:bg-gray-700" :disabled="finishServiceForm.processing">
                                    Cancelar Servicio
                                </PrimaryButton>
                            </div>
                        </div>
                        
                        <div v-else-if="props.box.estado === 'activo'" class="mt-6 p-4 bg-green-50 rounded-lg border border-green-200">
                            <h4 class="text-lg font-semibold text-green-800 mb-4">Iniciar Nuevo Servicio:</h4>
                            <form @submit.prevent="submitStartService" class="space-y-4">
                                <div class="flex items-center space-x-4 mb-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" v-model="newVehicle" :value="false" class="form-radio text-indigo-600">
                                        <span class="ml-2 text-gray-700">Seleccionar Vehículo Existente</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" v-model="newVehicle" :value="true" class="form-radio text-indigo-600">
                                        <span class="ml-2 text-gray-700">Registrar Nuevo Vehículo</span>
                                    </label>
                                </div>

                                <div v-if="!newVehicle">
                                    <InputLabel for="vehiculo_patente_select" value="Patente del Vehículo Existente" />
                                    <select 
                                        id="vehiculo_patente_select"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        v-model="startServiceForm.vehiculo_patente"
                                        required
                                    >
                                        <option value="">Seleccione una patente</option>
                                        <option v-for="vehiculo in props.vehiculos" :key="vehiculo.id" :value="vehiculo.patente">
                                            {{ vehiculo.display }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="startServiceForm.errors.vehiculo_patente" />
                                </div>

                                <div v-else>
                                    <div>
                                        <InputLabel for="vehiculo_patente_new" value="Patente del Nuevo Vehículo" />
                                        <TextInput
                                            id="vehiculo_patente_new"
                                            type="text"
                                            class="mt-1 block w-full uppercase"
                                            v-model="startServiceForm.vehiculo_patente"
                                            required
                                        />
                                        <InputError class="mt-2" :message="startServiceForm.errors.vehiculo_patente" />
                                    </div>
                                    <div class="mt-4">
                                        <InputLabel for="vehiculo_marca" value="Marca del Vehículo" />
                                        <TextInput
                                            id="vehiculo_marca"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="startServiceForm.vehiculo_marca"
                                            required
                                        />
                                        <InputError class="mt-2" :message="startServiceForm.errors.vehiculo_marca" />
                                    </div>
                                    <div class="mt-4">
                                        <InputLabel for="vehiculo_modelo" value="Modelo del Vehículo" />
                                        <TextInput
                                            id="vehiculo_modelo"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="startServiceForm.vehiculo_modelo"
                                            required
                                        />
                                        <InputError class="mt-2" :message="startServiceForm.errors.vehiculo_modelo" />
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <InputLabel for="tipo_lavado_id" value="Tipo de Lavado" />
                                    <select 
                                        id="tipo_lavado_id"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        v-model="startServiceForm.tipo_lavado_id"
                                        required
                                    >
                                        <option value="">Seleccione un tipo de lavado</option>
                                        <option v-for="tipo in props.tiposLavado" :key="tipo.id" :value="tipo.id">
                                            {{ tipo.nombre_lavado }} (${{ tipo.precio }})
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="startServiceForm.errors.tipo_lavado_id" />
                                </div>

                                <div class="flex items-center justify-end mt-4">
                                    <PrimaryButton :class="{ 'opacity-25': startServiceForm.processing }" :disabled="startServiceForm.processing">
                                        Iniciar Servicio
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>

                        <div v-else-if="props.box.estado === 'mantenimiento'" class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200 text-yellow-800">
                            <h4 class="text-lg font-semibold mb-2">Box en Mantenimiento</h4>
                            <p>Este box no está disponible actualmente para servicios.</p>
                        </div>

                        <div class="mt-6">
                            <Link :href="route('boxes.index')" class="text-blue-600 hover:text-blue-900 mr-4">Volver a la lista de Boxes</Link>
                            <Link :href="route('boxes.edit', props.box.id)" class="text-indigo-600 hover:text-indigo-900" 
                                :class="{'text-gray-400 cursor-not-allowed': props.box.estado === 'ocupado'}"
                                :disabled="props.box.estado === 'ocupado'">
                                Editar Box
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>