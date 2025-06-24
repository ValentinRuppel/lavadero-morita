<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    user: Object,
    tiposVehiculo: Array,
    marcas: Array,
});

const form = useForm({
    modelo_id: '',
    patente: '',
    anio: '',
    tipo_vehiculo_id: '',
});

const modelosDisponibles = ref([]);
const marcaSeleccionada = ref('');
const modeloSeleccionadoObjeto = ref(null);

watch(marcaSeleccionada, async (newMarcaId) => {
    modelosDisponibles.value = [];
    form.modelo_id = '';
    form.tipo_vehiculo_id = '';
    modeloSeleccionadoObjeto.value = null;
    form.anio = '';
    form.patente = '';

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

watch(() => form.modelo_id, (newModeloId) => {
    if (newModeloId) {
        const selectedModel = modelosDisponibles.value.find(modelo => modelo.id === newModeloId);
        if (selectedModel) {
            form.tipo_vehiculo_id = selectedModel.tipo_vehiculo_id;
            modeloSeleccionadoObjeto.value = selectedModel;
            form.anio = '';
            form.patente = '';
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

watch(() => form.anio, (newAnio, oldAnio) => {
    if (newAnio && oldAnio && (
        (newAnio < 2016 && oldAnio >= 2016) ||
        (newAnio >= 2016 && oldAnio < 2016)
    )) {
        const selectedTipoVehiculo = props.tiposVehiculo.find(t => t.id === form.tipo_vehiculo_id);
        if (selectedTipoVehiculo && (selectedTipoVehiculo.nombre === 'Auto' || selectedTipoVehiculo.nombre === 'Camioneta')) {
            form.patente = '';
        }
    } else if (!newAnio) {
        form.patente = '';
    }
}, { immediate: false });

const minAnioPermitido = computed(() => {
    return modeloSeleccionadoObjeto.value ? modeloSeleccionadoObjeto.value.anio_inicio : null;
});

const maxAnioPermitido = computed(() => {
    const currentYear = new Date().getFullYear();
    if (modeloSeleccionadoObjeto.value) {
        return modeloSeleccionadoObjeto.value.anio_fin || currentYear;
    }
    return null;
});

const maxPatenteLength = computed(() => {
    const selectedTipoVehiculo = props.tiposVehiculo.find(t => t.id === form.tipo_vehiculo_id);

    if (!selectedTipoVehiculo || !form.anio) {
        return null;
    }

    const tipoNombre = selectedTipoVehiculo.nombre;
    const anio = form.anio;

    if (tipoNombre === 'Auto' || tipoNombre === 'Camioneta') {
        if (anio < 2016) {
            return 7;
        } else {
            return 9;
        }
    } else if (tipoNombre === 'Moto') {
        return 7;
    }

    return null;
});

const tipoVehiculoAutoSeleccionadoNombre = computed(() => {
    if (form.tipo_vehiculo_id) {
        const tipo = props.tiposVehiculo.find(t => t.id === form.tipo_vehiculo_id);
        return tipo ? `${tipo.nombre} ($${tipo.precio})` : 'Cargando...';
    }
    return 'Selecciona un modelo para auto-seleccionar';
});

const submit = () => {
    form.post(route('vehiculos.store'), {
        onSuccess: () => {
            form.reset();
            marcaSeleccionada.value = '';
            modelosDisponibles.value = [];
            modeloSeleccionadoObjeto.value = null;
        },
        onError: (errors) => {
            console.error('Error al registrar el vehículo:', errors);
        }
    });
};
</script>

<template>
    <AppLayout title="Registrar Vehículo" :user="user">
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Tarjeta principal con glassmorphism -->
                <div class="bg-white/15 backdrop-blur-md rounded-2xl shadow-2xl border border-white/20 p-8 relative overflow-hidden">
                    <!-- Partículas internas flotantes -->
                    <div class="absolute top-4 right-4 w-2 h-2 bg-pink-300/40 rounded-full animate-pulse"></div>
                    <div class="absolute top-8 left-8 w-1 h-1 bg-violet-400/50 rounded-full animate-bounce" style="animation-delay: 0.5s"></div>
                    <div class="absolute bottom-6 right-12 w-3 h-3 bg-purple-200/30 rounded-full animate-pulse" style="animation-delay: 1s"></div>
                    <div class="absolute bottom-12 left-6 w-2 h-2 bg-fuchsia-300/35 rounded-full animate-bounce" style="animation-delay: 1.5s"></div>

                    <div class="text-center mb-8 relative">
                        <h2 class="text-3xl font-bold bg-gradient-to-r from-purple-200 to-pink-200 bg-clip-text text-transparent mb-4 pt-6">
                            Registrar Nuevo Vehículo
                        </h2>

                        <!-- Línea decorativa con gradiente -->
                        <div class="flex justify-center items-center space-x-2 mb-4">
                            <div class="w-8 h-0.5 bg-gradient-to-r from-transparent to-purple-400/50"></div>
                            <div class="w-3 h-3 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full animate-pulse"></div>
                            <div class="w-12 h-0.5 bg-gradient-to-r from-purple-400/50 to-pink-400/50"></div>
                            <div class="w-3 h-3 bg-gradient-to-r from-pink-400 to-violet-400 rounded-full animate-pulse" style="animation-delay: 0.5s"></div>
                            <div class="w-8 h-0.5 bg-gradient-to-r from-pink-400/50 to-transparent"></div>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Flash messages -->
                        <div v-if="$page.props.flash && $page.props.flash.success" class="bg-green-500/20 border border-green-400/30 text-green-100 p-4 rounded-lg backdrop-blur-sm">
                            <p class="font-bold">Éxito</p>
                            <p>{{ $page.props.flash.success }}</p>
                        </div>
                        <div v-if="$page.props.flash && $page.props.flash.error" class="bg-red-500/20 border border-red-400/30 text-red-100 p-4 rounded-lg backdrop-blur-sm">
                            <p class="font-bold">Error</p>
                            <p>{{ $page.props.flash.error }}</p>
                        </div>

                        <!-- Marca -->
                        <div class="group relative">
                            <label for="marca_seleccionada" class="block text-purple-100 text-sm font-bold mb-2">Marca:</label>
                            <select
                                id="marca_seleccionada"
                                v-model="marcaSeleccionada"
                                class="w-full py-2 px-3 bg-white/10 border border-purple-300/30 text-purple-100 rounded-lg focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/50 backdrop-blur-sm transition-all duration-300"
                                required
                            >
                                <option value="" disabled class="bg-purple-900">Selecciona una marca</option>
                                <option v-for="marca in props.marcas" :key="marca.id" :value="marca.id" class="bg-purple-900">
                                    {{ marca.nombre }}
                                </option>
                            </select>
                            <div v-if="form.errors.marca_seleccionada" class="text-red-400 text-xs mt-1">{{ form.errors.marca_seleccionada }}</div>
                        </div>

                        <!-- Modelo -->
                        <div class="group relative">
                            <label for="modelo_id" class="block text-purple-100 text-sm font-bold mb-2">Modelo:</label>
                            <select
                                id="modelo_id"
                                v-model="form.modelo_id"
                                class="w-full py-2 px-3 bg-white/10 border border-purple-300/30 text-purple-100 rounded-lg focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/50 backdrop-blur-sm transition-all duration-300"
                                :disabled="!marcaSeleccionada || modelosDisponibles.length === 0"
                                required
                            >
                                <option value="" disabled class="bg-purple-900">Selecciona un modelo</option>
                                <option v-if="!marcaSeleccionada" value="" disabled class="bg-purple-900">Selecciona una marca primero</option>
                                <option v-if="marcaSeleccionada && modelosDisponibles.length === 0" value="" disabled class="bg-purple-900">No hay modelos para esta marca</option>
                                <option v-for="modelo in modelosDisponibles" :key="modelo.id" :value="modelo.id" class="bg-purple-900">
                                    {{ modelo.nombre }}
                                </option>
                            </select>
                            <div v-if="form.errors.modelo_id" class="text-red-400 text-xs mt-1">{{ form.errors.modelo_id }}</div>
                        </div>

                        <!-- Año -->
                        <div class="group relative">
                            <label for="anio" class="block text-purple-100 text-sm font-bold mb-2">Año:</label>
                            <input
                                id="anio"
                                type="number"
                                v-model="form.anio"
                                class="w-full py-2 px-3 bg-white/10 border border-purple-300/30 text-purple-100 rounded-lg focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/50 backdrop-blur-sm transition-all duration-300"
                                :min="minAnioPermitido"
                                :max="maxAnioPermitido"
                                :disabled="!modeloSeleccionadoObjeto"
                                required
                            />
                            <div v-if="form.errors.anio" class="text-red-400 text-xs mt-1">
                                {{ form.errors.anio }}
                                <span v-if="modeloSeleccionadoObjeto && form.anio && (form.anio < minAnioPermitido || form.anio > maxAnioPermitido)">
                                    (Debe estar entre {{ minAnioPermitido }} y {{ maxAnioPermitido }})
                                </span>
                            </div>
                        </div>

                        <!-- Patente -->
                        <div class="group relative">
                            <label for="patente" class="block text-purple-100 text-sm font-bold mb-2">Patente:</label>
                            <input
                                id="patente"
                                type="text"
                                v-model="form.patente"
                                class="w-full py-2 px-3 bg-white/10 border border-purple-300/30 text-purple-100 rounded-lg focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/50 backdrop-blur-sm transition-all duration-300"
                                :maxlength="maxPatenteLength"
                                :disabled="!form.anio || !form.tipo_vehiculo_id"
                                required
                            />
                            <div v-if="form.errors.patente" class="text-red-400 text-xs mt-1">
                                {{ form.errors.patente }}
                                <span v-if="form.patente && maxPatenteLength && form.patente.length > maxPatenteLength">
                                    (Máximo {{ maxPatenteLength }} caracteres para este año y tipo de vehículo)
                                </span>
                            </div>
                        </div>

                        <!-- Tipo de Vehículo -->
                        <div class="group relative">
                            <label for="tipo_vehiculo_id" class="block text-purple-100 text-sm font-bold mb-2">Tipo de Vehículo:</label>
                            <input type="hidden" name="tipo_vehiculo_id" v-model="form.tipo_vehiculo_id" />
                            <input
                                type="text"
                                :value="tipoVehiculoAutoSeleccionadoNombre"
                                class="w-full py-2 px-3 bg-white/5 border border-purple-300/20 text-purple-100 rounded-lg backdrop-blur-sm cursor-not-allowed"
                                readonly
                                :disabled="!form.tipo_vehiculo_id"
                            />
                            <div v-if="form.errors.tipo_vehiculo_id" class="text-red-400 text-xs mt-1">{{ form.errors.tipo_vehiculo_id }}</div>
                        </div>

                        <!-- Botones -->
                        <div class="flex items-center justify-between mt-6">
                            <Link
                                :href="route('vehiculos.index')"
                                class="group relative bg-purple-500/20 hover:bg-purple-500/30 text-purple-100 px-4 py-2 rounded-full transition-all duration-300 transform hover:scale-105 border border-purple-400/20 backdrop-blur-sm"
                            >
                                <span class="relative z-10 flex items-center gap-2">
                                    ⬅ Volver
                                </span>
                                <div class="absolute inset-0 bg-gradient-to-r from-purple-400/20 to-pink-400/20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </Link>

                            <button
                                type="submit"
                                :disabled="form.processing || !form.modelo_id || !form.tipo_vehiculo_id || !form.anio || !form.patente"
                                class="group relative bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-bold py-2 px-6 rounded-full transition-all duration-300 transform hover:scale-105 border border-white/20 backdrop-blur-sm"
                            >
                                <span class="relative z-10">
                                    {{ form.processing ? 'Registrando...' : 'Registrar Vehículo' }}
                                </span>
                            </button>
                        </div>
                    </form>

                    <!-- Decoración inferior con puntos animados -->
                    <div class="flex justify-center items-center space-x-3 opacity-60 mt-8">
                        <div class="w-2 h-2 bg-purple-400/60 rounded-full animate-pulse"></div>
                        <div class="w-1 h-1 bg-pink-400/50 rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
                        <div class="w-3 h-3 bg-violet-400/40 rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
                        <div class="w-1 h-1 bg-fuchsia-400/60 rounded-full animate-pulse" style="animation-delay: 0.6s"></div>
                        <div class="w-2 h-2 bg-purple-300/50 rounded-full animate-pulse" style="animation-delay: 0.8s"></div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

/* Efectos para los inputs */
.group:hover select,
.group:hover input:not([disabled]) {
    transform: scale(1.01);
    transition: transform 0.3s ease-in-out;
}

/* Scroll suave */
html {
    scroll-behavior: smooth;
}
</style>
