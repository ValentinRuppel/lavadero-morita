<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Head } from '@inertiajs/vue3';
import { ref, watch, onMounted, computed } from 'vue'; // Importa 'computed'
import axios from 'axios';

const props = defineProps({
    user: Object,
    vehiculo: Object, // El vehículo que se está editando
    tiposVehiculo: Array,
    marcas: Array,
    modelosMarcaActual: Array, // Modelos de la marca del vehículo que se está editando
});

const form = useForm({
    _method: 'PUT',
    modelo_id: props.vehiculo.modelo_id,
    patente: props.vehiculo.patente,
    anio: props.vehiculo.anio,
    tipo_vehiculo_id: props.vehiculo.tipo_vehiculo_id, // precargar con el tipo_vehiculo_id del vehículo
});

const modelosDisponibles = ref([]);
const marcaSeleccionada = ref('');
const modeloSeleccionadoObjeto = ref(null); // Para guardar el objeto del modelo seleccionado

// Watcher para la marca seleccionada: carga los modelos
watch(marcaSeleccionada, async (newMarcaId) => {
    modelosDisponibles.value = [];
    form.modelo_id = '';
    form.tipo_vehiculo_id = ''; // Limpiar tipo si cambia la marca
    modeloSeleccionadoObjeto.value = null; // Limpiar objeto modelo

    if (newMarcaId) {
        try {
            const response = await axios.get(route('vehiculos.modelosByMarca'), {
                params: {
                    marca_id: newMarcaId
                }
            });
            modelosDisponibles.value = response.data;
        } catch (error) {
            console.error('Error al cargar modelos:', error);
        }
    }
});

// Watcher para el modelo seleccionado: asigna el tipo de vehículo
watch(() => form.modelo_id, (newModeloId) => {
    if (newModeloId) {
        const selectedModel = modelosDisponibles.value.find(modelo => modelo.id === newModeloId);
        if (selectedModel && selectedModel.tipo_vehiculo_id) {
            form.tipo_vehiculo_id = selectedModel.tipo_vehiculo_id;
            modeloSeleccionadoObjeto.value = selectedModel;
        } else {
            form.tipo_vehiculo_id = '';
            modeloSeleccionadoObjeto.value = null;
        }
    } else {
        form.tipo_vehiculo_id = '';
        modeloSeleccionadoObjeto.value = null;
    }
});

// onMounted para precargar la marca, el modelo y el tipo al cargar el componente
onMounted(() => {
    if (props.vehiculo.modelo) { // Asegúrate de que el vehículo tenga su relación 'modelo' cargada
        marcaSeleccionada.value = props.vehiculo.modelo.marca_id;
        // Cuando marcaSeleccionada.value cambia, el watcher de marca hará la llamada y poblará modelosDisponibles.
        // Después de que modelosDisponibles se haya poblado, el watcher de form.modelo_id debería configurar el tipo.

        // Dado que el watcher de marcaSeleccionada es async, podemos inicializar modelosDisponibles
        // con los modelosMarcaActual para que el dropdown de modelos tenga opciones desde el inicio.
        modelosDisponibles.value = props.modelosMarcaActual;
        
        // El form.modelo_id ya está precargado desde props.vehiculo.modelo_id.
        // El watcher de form.modelo_id se ejecutará automáticamente y establecerá form.tipo_vehiculo_id.
    }
});

const tipoVehiculoAutoSeleccionadoNombre = computed(() => {
    if (form.tipo_vehiculo_id) {
        const tipo = props.tiposVehiculo.find(t => t.id === form.tipo_vehiculo_id);
        return tipo ? `${tipo.nombre} ($${tipo.precio})` : 'Cargando...';
    }
    return 'Selecciona un modelo para auto-seleccionar';
});


const submit = () => {
    form.post(route('vehiculos.update', props.vehiculo.id), {
        onSuccess: () => {
            // No resetear en edición, usualmente
        },
        onError: (errors) => {
            console.error('Error al actualizar el vehículo:', errors);
        }
    });
};

const getCurrentYear = () => {
    return new Date().getFullYear();
};
</script>

<template>
    <AppLayout title="Editar Vehículo">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar Vehículo
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit">
                        <div v-if="$page.props.flash && $page.props.flash.success" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p class="font-bold">Éxito</p>
                            <p>{{ $page.props.flash.success }}</p>
                        </div>
                        <div v-if="$page.props.flash && $page.props.flash.error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p class="font-bold">Error</p>
                            <p>{{ $page.props.flash.error }}</p>
                        </div>

                        <div class="mb-4">
                            <label for="marca_seleccionada" class="block text-gray-700 text-sm font-bold mb-2">Marca:</label>
                            <select
                                id="marca_seleccionada"
                                v-model="marcaSeleccionada"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required
                            >
                                <option value="" disabled>Selecciona una marca</option>
                                <option v-for="marca in props.marcas" :key="marca.id" :value="marca.id">
                                    {{ marca.nombre }}
                                </option>
                            </select>
                            <div v-if="form.errors.marca_seleccionada" class="text-red-500 text-xs mt-1">{{ form.errors.marca_seleccionada }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="modelo_id" class="block text-gray-700 text-sm font-bold mb-2">Modelo:</label>
                            <select
                                id="modelo_id"
                                v-model="form.modelo_id"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                :disabled="!marcaSeleccionada || modelosDisponibles.length === 0"
                                required
                            >
                                <option value="" disabled>Selecciona un modelo</option>
                                <option v-if="!marcaSeleccionada" value="" disabled>Selecciona una marca primero</option>
                                <option v-if="marcaSeleccionada && modelosDisponibles.length === 0" value="" disabled>No hay modelos para esta marca</option>
                                <option v-for="modelo in modelosDisponibles" :key="modelo.id" :value="modelo.id">
                                    {{ modelo.nombre }}
                                </option>
                            </select>
                            <div v-if="form.errors.modelo_id" class="text-red-500 text-xs mt-1">{{ form.errors.modelo_id }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="patente" class="block text-gray-700 text-sm font-bold mb-2">Patente:</label>
                            <input
                                id="patente"
                                type="text"
                                v-model="form.patente"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required
                            />
                            <div v-if="form.errors.patente" class="text-red-500 text-xs mt-1">{{ form.errors.patente }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="anio" class="block text-gray-700 text-sm font-bold mb-2">Año:</label>
                            <input
                                id="anio"
                                type="number"
                                v-model="form.anio"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                :max="getCurrentYear()"
                                min="1900"
                                required
                            />
                            <div v-if="form.errors.anio" class="text-red-500 text-xs mt-1">{{ form.errors.anio }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="tipo_vehiculo_id" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Vehículo:</label>
                            <input type="hidden" name="tipo_vehiculo_id" v-model="form.tipo_vehiculo_id" />
                            <input
                                type="text"
                                :value="tipoVehiculoAutoSeleccionadoNombre"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline bg-gray-100 cursor-not-allowed"
                                readonly
                                :disabled="!form.tipo_vehiculo_id"
                            />
                            <div v-if="form.errors.tipo_vehiculo_id" class="text-red-500 text-xs mt-1">{{ form.errors.tipo_vehiculo_id }}</div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Cliente:</label>
                            <p class="py-2 px-3 text-gray-700">{{ vehiculo.cliente_nombre }}</p>
                        </div>


                        <div class="flex items-center justify-end mt-4">
                            <button
                                type="submit"
                                :disabled="form.processing || !form.modelo_id || !form.tipo_vehiculo_id"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                            >
                                {{ form.processing ? 'Actualizando...' : 'Actualizar Vehículo' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>