<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Head } from '@inertiajs/vue3';
import { ref, watch, onMounted, computed } from 'vue';
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
const modeloSeleccionadoObjeto = ref(null); // Para guardar el objeto del modelo seleccionado con sus años

// Watcher para la marca seleccionada: carga los modelos
watch(marcaSeleccionada, async (newMarcaId) => {
    modelosDisponibles.value = [];
    form.modelo_id = '';
    form.tipo_vehiculo_id = ''; // Limpiar tipo si cambia la marca
    modeloSeleccionadoObjeto.value = null; // Limpiar objeto modelo
    form.anio = ''; // Limpiar año al cambiar la marca
    form.patente = ''; // Limpiar patente al cambiar la marca

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

// Watcher para el modelo seleccionado: asigna el tipo de vehículo y el objeto modelo completo
watch(() => form.modelo_id, (newModeloId) => {
    if (newModeloId) {
        const selectedModel = modelosDisponibles.value.find(modelo => modelo.id === newModeloId);
        if (selectedModel) {
            form.tipo_vehiculo_id = selectedModel.tipo_vehiculo_id;
            modeloSeleccionadoObjeto.value = selectedModel; // Guarda el objeto modelo completo
            form.anio = ''; // Limpiar el año si el modelo cambia
            form.patente = ''; // Limpiar la patente si el modelo cambia
        } else {
            form.tipo_vehiculo_id = '';
            modeloSeleccionadoObjeto.value = null;
            form.anio = '';
            form.patente = '';
        }
    } else {
        form.tipo_vehiculo_id = '';
        modeloSeleccionadoObjeto.value = null;
        form.anio = '';
        form.patente = '';
    }
});

// Watcher para el año: limpiar patente si cambia el año (para re-evaluar la longitud)
watch(() => form.anio, (newAnio, oldAnio) => {
    // Only clear if the previous anio was valid and the new one might change the length rule
    if (newAnio && oldAnio && (
        (newAnio < 2016 && oldAnio >= 2016) ||
        (newAnio >= 2016 && oldAnio < 2016)
    )) {
        // Only clear if it's an auto/camioneta where the rule changes
        const selectedTipoVehiculo = props.tiposVehiculo.find(t => t.id === form.tipo_vehiculo_id);
        if (selectedTipoVehiculo && (selectedTipoVehiculo.nombre === 'Auto' || selectedTipoVehiculo.nombre === 'Camioneta')) {
            form.patente = '';
        }
    } else if (!newAnio) { // If year is cleared
        form.patente = '';
    }
}, { immediate: false }); // Do not run immediately on component mount

// onMounted para precargar la marca, el modelo y el tipo al cargar el componente
onMounted(() => {
    if (props.vehiculo.modelo) { // Asegúrate de que el vehículo tenga su relación 'modelo' cargada
        marcaSeleccionada.value = props.vehiculo.modelo.marca_id;
        modelosDisponibles.value = props.modelosMarcaActual;
        // Encuentra el objeto modelo completo del vehículo actual
        const initialSelectedModel = props.modelosMarcaActual.find(modelo => modelo.id === props.vehiculo.modelo_id);
        if (initialSelectedModel) {
            modeloSeleccionadoObjeto.value = initialSelectedModel;
        }
    }
});

// Propiedad computada para determinar el año mínimo permitido
const minAnioPermitido = computed(() => {
    return modeloSeleccionadoObjeto.value ? modeloSeleccionadoObjeto.value.anio_inicio : null;
});

// Propiedad computada para determinar el año máximo permitido
const maxAnioPermitido = computed(() => {
    const currentYear = new Date().getFullYear();
    if (modeloSeleccionadoObjeto.value) {
        return modeloSeleccionadoObjeto.value.anio_fin || currentYear;
    }
    return null;
});

// Propiedad computada para la longitud máxima de la patente
const maxPatenteLength = computed(() => {
    const selectedTipoVehiculo = props.tiposVehiculo.find(t => t.id === form.tipo_vehiculo_id);

    if (!selectedTipoVehiculo || !form.anio) {
        return null; // No restriction until type and year are selected
    }

    const tipoNombre = selectedTipoVehiculo.nombre;
    const anio = form.anio;

    if (tipoNombre === 'Auto' || tipoNombre === 'Camioneta') {
        // Logic for cars and trucks based on year
        if (anio < 2016) { // Old or intermediate format
            return 7; // e.g., AAA NNN or ABC 123 (including space)
        } else { // Mercosur format
            return 9; // e.g., AA NNN LL or AB 123 CD (including spaces)
        }
    } else if (tipoNombre === 'Moto') {
        // Fixed length for motorcycles (most common formats)
        return 7; // e.g., 123 ABC or A 123 BC (including space)
    }

    return null; // Default, no restriction
});

// Opcional: Propiedad computada para mostrar el nombre del tipo de vehículo seleccionado automáticamente
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
</script>

<template>
    <AppLayout title="Editar Vehículo" :user="user">
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
                            <label for="anio" class="block text-gray-700 text-sm font-bold mb-2">Año:</label>
                            <input
                                id="anio"
                                type="number"
                                v-model="form.anio"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                :min="minAnioPermitido"
                                :max="maxAnioPermitido"
                                :disabled="!modeloSeleccionadoObjeto"
                                required
                            />
                            <div v-if="form.errors.anio" class="text-red-500 text-xs mt-1">
                                {{ form.errors.anio }}
                                <span v-if="modeloSeleccionadoObjeto && form.anio && (form.anio < minAnioPermitido || form.anio > maxAnioPermitido)">
                                    (Debe estar entre {{ minAnioPermitido }} y {{ maxAnioPermitido }})
                                </span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="patente" class="block text-gray-700 text-sm font-bold mb-2">Patente:</label>
                            <input
                                id="patente"
                                type="text"
                                v-model="form.patente"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                :maxlength="maxPatenteLength"
                                :disabled="!form.anio || !form.tipo_vehiculo_id"
                                required
                            />
                            <div v-if="form.errors.patente" class="text-red-500 text-xs mt-1">
                                {{ form.errors.patente }}
                                <span v-if="form.patente && maxPatenteLength && form.patente.length > maxPatenteLength">
                                    (Máximo {{ maxPatenteLength }} caracteres para este año y tipo de vehículo)
                                </span>
                            </div>
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

                        <div class="flex items-center justify-end mt-4">
                            <button
                                type="submit"
                                :disabled="form.processing || !form.modelo_id || !form.tipo_vehiculo_id || !form.anio || !form.patente"
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