<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch, onUnmounted } from 'vue';

const page = usePage();
const showError = ref(false);
const errorMessage = ref('');
watch(() => page.props.flash.error, (value) => {
    if (value) {
        errorMessage.value = value;
        showError.value = true;
        setTimeout(() => {
            showError.value = false;
            errorMessage.value = '';
        }, 5000);
    }
}, { immediate: true });

const props = defineProps({
    box: Object,
    tiposLavado: Array,
    clientes: Array,
    adminId: Number,
    user: Object,
});

const startServiceForm = useForm({
    box_id: props.box.id,
    user_id: '',
    vehiculo_id: '',
    tipo_lavado_id: '',
    administrador_id: props.adminId,
});
const finishServiceForm = useForm({});

const selectedClientId = ref('');
const emailInput = ref('');
const showClientSuggestions = ref(false);
const availableVehicles = ref([]);
const showStartServiceModal = ref(false);
const showPostServiceOptionsModal = ref(false);
const newClientVehicle = ref(false);

const filteredClients = computed(() => {
    if (!emailInput.value.trim()) {
        return [];
    }
    const query = emailInput.value.toLowerCase();
    return props.clientes.filter(client =>
        client.email.toLowerCase().includes(query) ||
        client.name.toLowerCase().includes(query)
    );
});

const selectClient = (client) => {
    selectedClientId.value = client.id;
    emailInput.value = client.email;
    startServiceForm.user_id = client.id;
    showClientSuggestions.value = false;
};

const checkClientVehiclesAndRedirect = async (clientId, clientName) => {
    showStartServiceModal.value = false;
    router.post(route('client.checkAndRedirect'), {
        user_id: clientId,
        user_name: clientName,
    }, {
        onSuccess: (page) => {
            console.log('Backend handled redirect or response');
        },
        onError: (errors) => {
            console.error('Error al verificar vehículos en el backend:', errors);
        },
        preserveState: false,
        preserveScroll: true,
    });
};

watch(selectedClientId, (newClientId) => {
    const client = props.clientes.find(c => c.id === newClientId);
    if (client) {
        availableVehicles.value = client.vehiculos;
        startServiceForm.vehiculo_id = '';
        if (availableVehicles.value.length === 0) {
            checkClientVehiclesAndRedirect(newClientId, client.name);
        }
    } else {
        availableVehicles.value = [];
        startServiceForm.vehiculo_id = '';
    }
    startServiceForm.user_id = newClientId;
});

const submitStartService = () => {
    if (newClientVehicle.value && selectedClientId.value) {
        startServiceForm.user_id = selectedClientId.value;
    } else if (!newClientVehicle.value && startServiceForm.vehiculo_id) {
        startServiceForm.user_id = null;
    }
    startServiceForm.post(route('servicios.iniciar'), {
        onSuccess: () => {
            showStartServiceModal.value = false;
        },
        onError: (errors) => {
            console.error('Error al iniciar servicio:', errors);
        },
        preserveScroll: true,
    });
};

const submitFinishService = () => {
    if (!props.box.servicio_en_curso || !props.box.servicio_en_curso.id) {
        console.error('No hay un servicio en curso para finalizar.');
        return;
    }
    const targetFinalizeUrl = route('servicios.finalizar', props.box.servicio_en_curso.id);
    console.log('submitFinishService: Intentando PUT a:', targetFinalizeUrl);
    finishServiceForm.put(targetFinalizeUrl, {
        onSuccess: () => {
            showPostServiceOptionsModal.value = false;
        },
        onError: (errors) => {
            console.error('Error al finalizar servicio:', errors);
        },
        preserveScroll: true,
    });
};

const submitCancelService = () => {
    if (confirm('¿Estás seguro de que quieres cancelar este servicio? El box volverá a estar activo.')) {
        finishServiceForm.post(route('servicios.cancelar', props.box.servicio_en_curso.id), {
            onSuccess: () => { },
            onError: (errors) => {
                console.error('Error al cancelar servicio:', errors);
            },
            preserveScroll: true,
        });
    }
};

watch(newClientVehicle, (newValue) => {
    if (!newValue) {
        startServiceForm.vehiculo_patente_nuevo = '';
        startServiceForm.vehiculo_marca_nuevo = '';
        startServiceForm.vehiculo_modelo_nuevo = '';
        startServiceForm.vehiculo_anio_nuevo = '';
    } else {
        startServiceForm.vehiculo_id = '';
    }
    startServiceForm.tipo_lavado_id = '';
});

watch(() => startServiceForm.vehiculo_id, () => {
    startServiceForm.tipo_lavado_id = '';
});

const selectedVehiculoTipoId = computed(() => {
    if (newClientVehicle.value) {
        return null;
    } else if (startServiceForm.vehiculo_id) {
        const selectedVehicle = availableVehicles.value.find(v => v.id === startServiceForm.vehiculo_id);
        return selectedVehicle ? selectedVehicle.tipo_vehiculo_id : null;
    }
    return null;
});

const filteredTiposLavado = computed(() => {
    return props.tiposLavado;
});

const estimatedPrice = computed(() => {
    let basePrice = 0;
    let tipoVehiculoPrice = 0;
    const selectedTipoLavado = props.tiposLavado.find(
        t => t.id === startServiceForm.tipo_lavado_id
    );
    if (selectedTipoLavado) {
        basePrice = parseFloat(selectedTipoLavado.precio);
    }
    if (!newClientVehicle.value && startServiceForm.vehiculo_id) {
        const selectedVehicle = availableVehicles.value.find(v => v.id === startServiceForm.vehiculo_id);
        if (selectedVehicle && selectedVehicle.tipo_vehiculo_precio !== undefined) {
            tipoVehiculoPrice = parseFloat(selectedVehicle.tipo_vehiculo_precio);
        } else if (selectedVehicle && selectedVehicle.tipo_vehiculo && selectedVehicle.tipo_vehiculo.precio !== undefined) {
            tipoVehiculoPrice = parseFloat(selectedVehicle.tipo_vehiculo.precio);
        }
    }
    return basePrice + tipoVehiculoPrice;
});

const estimatedDuration = computed(() => {
    const selectedTipoLavado = props.tiposLavado.find(
        t => t.id === startServiceForm.tipo_lavado_id
    );
    return selectedTipoLavado ? parseInt(selectedTipoLavado.duracion_estimada) : 0;
});

const remainingTime = ref(0);
let timerInterval = null;
const serviceStartTime = ref(null);

const startTimer = (durationInSeconds) => {
    remainingTime.value = durationInSeconds;
    serviceStartTime.value = Date.now();
    if (timerInterval) {
        clearInterval(timerInterval);
    }
    timerInterval = setInterval(() => {
        if (remainingTime.value > 0) {
            remainingTime.value--;
        } else {
            clearInterval(timerInterval);
            timerInterval = null;
            if (props.box.estado === 'ocupado' && props.box.servicio_en_curso && props.box.servicio_en_curso.estado_servicio === 'en_curso') {
                showPostServiceOptionsModal.value = true;
            }
        }
    }, 1000);
};

const formatTime = (seconds) => {
    const minutes = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
};

watch(() => props.box.servicio_en_curso, (newService) => {
    if (newService && newService.tipo_lavado && newService.estado_servicio === 'en_curso') {
        const elapsedSeconds = Math.floor((Date.now() - new Date(newService.fecha_hora_inicio).getTime()) / 1000);
        const estimatedTotalSeconds = newService.tipo_lavado.duracion_estimada * 60;
        const remaining = estimatedTotalSeconds - elapsedSeconds;
        if (remaining > 0) {
            startTimer(remaining);
        } else {
            remainingTime.value = 0;
            if (props.box.estado === 'ocupado' && newService.estado_servicio === 'en_curso') {
                showPostServiceOptionsModal.value = true;
            }
        }
    } else {
        clearInterval(timerInterval);
        timerInterval = null;
        remainingTime.value = 0;
        showPostServiceOptionsModal.value = false;
    }
}, { immediate: true, deep: true });

onUnmounted(() => {
    if (timerInterval) {
        clearInterval(timerInterval);
    }
});

const currentServiceId = computed(() => props.box.servicio_en_curso?.id || null);
const currentServiceTotal = computed(() => props.box.servicio_en_curso?.precio_total_servicio || 0);
</script>

<template>

    <Head :title="'Detalle del Box: ' + props.box.nombre_box" />

    <AppLayout :user="user">
        <div class="py-12">
            <div v-if="page.props.flash.error" class="bg-red-500 text-white p-3 rounded">
                {{ page.props.flash.error }}
            </div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Tarjeta principal con glassmorphism -->
                <div
                    class="bg-white/15 backdrop-blur-md rounded-2xl shadow-2xl border border-white/20 p-8 relative overflow-hidden">
                    <!-- Partículas internas flotantes -->
                    <div class="absolute top-4 right-4 w-2 h-2 bg-pink-300/40 rounded-full animate-pulse"></div>
                    <div class="absolute top-8 left-8 w-1 h-1 bg-violet-400/50 rounded-full animate-bounce"
                        style="animation-delay: 0.5s"></div>
                    <div class="absolute bottom-6 right-12 w-3 h-3 bg-purple-200/30 rounded-full animate-pulse"
                        style="animation-delay: 1s"></div>
                    <div class="absolute bottom-12 left-6 w-2 h-2 bg-fuchsia-300/35 rounded-full animate-bounce"
                        style="animation-delay: 1.5s"></div>

                    <!-- Título con decoración -->
                    <div class="text-center mb-8 relative">
                        <h2
                            class="text-3xl font-bold bg-gradient-to-r from-purple-200 to-pink-200 bg-clip-text text-transparent mb-4 pt-6">
                            Detalle del Box: {{ props.box.nombre_box }}
                        </h2>
                        <!-- Línea decorativa con gradiente -->
                        <div class="flex justify-center items-center space-x-2 mb-4">
                            <div class="w-8 h-0.5 bg-gradient-to-r from-transparent to-purple-400/50"></div>
                            <div
                                class="w-3 h-3 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full animate-pulse">
                            </div>
                            <div class="w-12 h-0.5 bg-gradient-to-r from-purple-400/50 to-pink-400/50"></div>
                            <div class="w-3 h-3 bg-gradient-to-r from-pink-400 to-violet-400 rounded-full animate-pulse"
                                style="animation-delay: 0.5s"></div>
                            <div class="w-8 h-0.5 bg-gradient-to-r from-pink-400/50 to-transparent"></div>
                        </div>
                    </div>

                    <!-- Información del box -->
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-purple-100 mb-2">{{ props.box.nombre_box }}</h3>
                        <p class="text-purple-200 text-sm mb-3">{{ props.box.descripcion || 'Sin descripción' }}</p>
                        <div class="mb-4">
                            <span :class="{
                                'px-3 py-1 rounded-full text-sm font-semibold inline-block border': true,
                                'bg-green-500/20 text-green-100 border-green-400/30': props.box.estado === 'activo',
                                'bg-red-500/20 text-red-100 border-red-400/30': props.box.estado === 'ocupado',
                                'bg-yellow-500/20 text-yellow-100 border-yellow-400/30': props.box.estado === 'mantenimiento',
                            }">
                                Estado: {{ props.box.estado.charAt(0).toUpperCase() + props.box.estado.slice(1) }}
                            </span>
                        </div>
                    </div>

                    <!-- Servicio en curso -->
                    <div v-if="props.box.servicio_en_curso && props.box.estado === 'ocupado'"
                        class="mt-6 p-4 bg-indigo-500/10 rounded-lg border border-indigo-400/20">
                        <h4 class="text-lg font-semibold text-indigo-100 mb-2">Servicio en Curso</h4>
                        <p class="text-indigo-200 text-sm">Vehículo: {{ props.box.servicio_en_curso.vehiculo?.patente }}
                            {{ props.box.servicio_en_curso.vehiculo?.marca }} {{
                                props.box.servicio_en_curso.vehiculo?.modelo }} {{
                                props.box.servicio_en_curso.vehiculo?.anio }}</p>
                        <p class="text-indigo-200 text-sm">Cliente: {{
                            props.box.servicio_en_curso.vehiculo?.cliente_nombre }}</p>
                        <p class="text-indigo-200 text-sm">Tipo de Lavado: {{
                            props.box.servicio_en_curso.tipo_lavado?.nombre_lavado }}</p>
                        <p class="text-indigo-200 text-sm">Precio Total: ${{
                            props.box.servicio_en_curso?.precio_total_servicio }}</p>
                        <p class="text-indigo-200 text-sm">Inició: {{ new
                            Date(props.box.servicio_en_curso.fecha_hora_inicio).toLocaleString() }}</p>
                        <p class="text-indigo-200 text-sm">Asignado por: {{
                            props.box.servicio_en_curso.administrador?.name }}</p>
                        <div class="mt-4 text-center">
                            <p class="text-xl font-bold text-indigo-100">Tiempo Restante: {{ formatTime(remainingTime)
                                }}</p>
                            <p v-if="remainingTime <= 0" class="text-red-100 font-semibold">¡Tiempo terminado!</p>
                        </div>
                        <div class="mt-4 flex justify-center space-x-2" v-if="remainingTime > 0">
                            <button @click="submitCancelService" :class="{
                                'group relative bg-red-500/20 hover:bg-red-500/30 text-red-100 hover:text-red-200 px-4 py-2 rounded-full transition-all duration-300 transform hover:scale-105 border border-red-400/20 backdrop-blur-sm': !finishServiceForm.processing,
                                'opacity-25 cursor-not-allowed px-4 py-2': finishServiceForm.processing
                            }" :disabled="finishServiceForm.processing">
                                <span class="relative z-10 flex items-center gap-2">❌ Cancelar Servicio</span>
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-red-400/20 to-pink-400/20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- SECCIÓN DE INICIAR NUEVO SERVICIO (BOX ACTIVO) -->
                    <div v-else-if="props.box.estado === 'activo'"
                        class="mt-6 p-4 bg-indigo-500/10 rounded-lg border border-indigo-400/20">
                        <h4 class="text-lg font-semibold text-indigo-100 mb-2">Iniciar Nuevo Servicio:</h4>
                        <form @submit.prevent="submitStartService" class="space-y-4">
                            <div class="relative">
                                <InputLabel for="email_input" value="Buscar Cliente por Email o Nombre"
                                    class="text-indigo-200 text-sm" />
                                <TextInput id="email_input" type="text"
                                    class="bg-white/10 border border-purple-300/20 text-purple-100 rounded-md shadow-sm mt-1 block w-full focus:ring-purple-500 focus:border-purple-500 backdrop-blur-sm"
                                    v-model="emailInput" @focus="showClientSuggestions = true"
                                    @blur="setTimeout(() => showClientSuggestions = false, 200)"
                                    placeholder="Escribe el email o nombre del cliente..." required />
                                <InputError class="mt-2" :message="startServiceForm.errors.user_id" />
                                <div v-if="showClientSuggestions && filteredClients.length > 0"
                                    class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg mt-1 max-h-60 overflow-y-auto">
                                    <div v-for="client in filteredClients" :key="client.id"
                                        @click="selectClient(client)"
                                        class="px-4 py-2 hover:bg-indigo-100 cursor-pointer text-gray-800">
                                        {{ client.name }} ({{ client.email }})
                                    </div>
                                </div>
                                <p v-if="emailInput && filteredClients.length === 0" class="text-sm text-red-600 mt-2">
                                    No se encontraron clientes con ese email o nombre.
                                </p>
                            </div>

                            <div v-if="selectedClientId && !newClientVehicle">
                                <InputLabel for="vehiculo_id" value="Seleccionar Vehículo"
                                    class="text-indigo-200 text-sm" />
                                <select id="vehiculo_id"
                                    class="bg-white/10 border border-purple-300/20 text-purple-100 rounded-md shadow-sm mt-1 block w-full focus:ring-purple-500 focus:border-purple-500 backdrop-blur-sm"
                                    v-model="startServiceForm.vehiculo_id" required
                                    :disabled="availableVehicles.length === 0">
                                    <option value="" class="bg-purple-800/50">Seleccione un vehículo</option>
                                    <option v-if="availableVehicles.length === 0" disabled>No hay vehículos para este
                                        cliente.</option>
                                    <option v-for="vehiculo in availableVehicles" :key="vehiculo.id"
                                        :value="vehiculo.id" class="bg-purple-800/50">
                                        {{ vehiculo.patente }} {{ vehiculo.modelo.marca.nombre }} {{
                                        vehiculo.modelo.nombre }} {{ vehiculo.anio }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="startServiceForm.errors.vehiculo_id" />
                            </div>

                            <div v-if="selectedClientId && newClientVehicle">
                                <div>
                                    <InputLabel for="vehiculo_patente_nuevo" value="Patente del Nuevo Vehículo" />
                                    <TextInput id="vehiculo_patente_nuevo" type="text"
                                        class="mt-1 block w-full uppercase"
                                        v-model="startServiceForm.vehiculo_patente_nuevo" required />
                                    <InputError class="mt-2"
                                        :message="startServiceForm.errors.vehiculo_patente_nuevo" />
                                </div>
                                <div class="mt-4">
                                    <InputLabel for="vehiculo_marca_nuevo" value="Marca del Vehículo" />
                                    <TextInput id="vehiculo_marca_nuevo" type="text" class="mt-1 block w-full"
                                        v-model="startServiceForm.vehiculo_marca_nuevo" required />
                                    <InputError class="mt-2" :message="startServiceForm.errors.vehiculo_marca_nuevo" />
                                </div>
                                <div class="mt-4">
                                    <InputLabel for="vehiculo_modelo_nuevo" value="Modelo del Vehículo" />
                                    <TextInput id="vehiculo_modelo_nuevo" type="text" class="mt-1 block w-full"
                                        v-model="startServiceForm.vehiculo_modelo_nuevo" required />
                                    <InputError class="mt-2" :message="startServiceForm.errors.vehiculo_modelo_nuevo" />
                                </div>
                                <div class="mt-4">
                                    <InputLabel for="vehiculo_anio_nuevo" value="Año del Vehículo" />
                                    <TextInput id="vehiculo_anio_nuevo" type="number" class="mt-1 block w-full"
                                        v-model="startServiceForm.vehiculo_anio_nuevo" min="1900"
                                        :max="new Date().getFullYear() + 1" required />
                                    <InputError class="mt-2" :message="startServiceForm.errors.vehiculo_anio_nuevo" />
                                </div>
                            </div>
                            <div class="mt-4">
                                <InputLabel for="tipo_lavado_id" value="Tipo de Lavado"
                                    class="text-indigo-200 text-sm" />
                                <select id="tipo_lavado_id"
                                    class="bg-white/10 border border-purple-300/20 text-purple-100 rounded-md shadow-sm mt-1 block w-full focus:ring-purple-500 focus:border-purple-500 backdrop-blur-sm"
                                    v-model="startServiceForm.tipo_lavado_id" required
                                    :disabled="!selectedClientId || (!startServiceForm.vehiculo_id && !newClientVehicle)">
                                    <option value="" class="bg-purple-800/50">Seleccione un tipo de lavado</option>
                                    <option v-for="tipo in filteredTiposLavado" :key="tipo.id" :value="tipo.id"
                                        class="bg-purple-800/50">
                                        {{ tipo.nombre_lavado }} (${{ tipo.precio }}) - {{ tipo.duracion_estimada }} min
                                    </option>
                                </select>
                                <InputError class="mt-2 text-red-100"
                                    :message="startServiceForm.errors.tipo_lavado_id" />
                            </div>
                            <div v-if="startServiceForm.tipo_lavado_id"
                                class="mt-4 p-3 bg-blue-500/10 border border-blue-400/20 rounded-md">
                                <p class="font-semibold text-blue-100">Costo Estimado: ${{ estimatedPrice.toFixed(2) }}
                                </p>
                                <p class="font-semibold text-blue-100">Duración Estimada: {{ estimatedDuration }}
                                    minutos</p>
                            </div>
                            <div class="flex justify-end mt-4">
                                <button type="button" @click="submitStartService"
                                    class="group relative bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-bold py-2 px-6 rounded-full transition-all duration-300 transform hover:scale-105 border border-white/20 backdrop-blur-sm"
                                    :disabled="startServiceForm.processing">
                                    <span class="relative z-10 flex items-center gap-2">➕ Iniciar Servicio</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Box en mantenimiento -->
                    <div v-else-if="props.box.estado === 'mantenimiento'"
                        class="mt-6 p-4 bg-yellow-500/10 rounded-lg border border-yellow-400/20 text-yellow-100">
                        <h4 class="text-lg font-semibold mb-2">Box en Mantenimiento</h4>
                        <p>Este box no está disponible actualmente para servicios.</p>
                    </div>

                    <!-- Navegación -->
                    <div class="flex justify-start mt-6 space-x-2">
                        <Link :href="route('boxes.index')"
                            class="group relative bg-purple-500/20 hover:bg-purple-500/30 text-purple-100 px-4 py-2 rounded-full transition-all duration-300 transform hover:scale-105 border border-purple-400/20 backdrop-blur-sm">
                        <span class="relative z-10 flex items-center gap-2">⬅ Volver a la lista de Boxes</span>
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-purple-400/20 to-pink-400/20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>
                        </Link>
                    </div>

                    <!-- Decoración inferior con puntos animados -->
                    <div class="flex justify-center items-center space-x-3 opacity-60 mt-8">
                        <div class="w-2 h-2 bg-purple-400/60 rounded-full animate-pulse"></div>
                        <div class="w-1 h-1 bg-pink-400/50 rounded-full animate-pulse" style="animation-delay: 0.2s">
                        </div>
                        <div class="w-3 h-3 bg-violet-400/40 rounded-full animate-pulse" style="animation-delay: 0.4s">
                        </div>
                        <div class="w-1 h-1 bg-fuchsia-400/60 rounded-full animate-pulse" style="animation-delay: 0.6s">
                        </div>
                        <div class="w-2 h-2 bg-purple-300/50 rounded-full animate-pulse" style="animation-delay: 0.8s">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de opciones al finalizar el servicio -->
        <div v-if="showPostServiceOptionsModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-sm">
            <div
                class="bg-white/15 backdrop-blur-md rounded-2xl shadow-2xl border border-white/20 p-6 w-full max-w-md relative">
                <div class="absolute top-2 right-2 w-2 h-2 bg-pink-300/40 rounded-full animate-pulse"></div>
                <h3 class="text-xl font-semibold text-purple-100 mb-4">Servicio Finalizado en Box {{ box.nombre_box }}
                </h3>
                <p class="text-purple-200 mb-4">El contador ha terminado. ¿Qué deseas hacer a continuación?</p>
                <div class="flex justify-end space-x-3">
                    <button @click="submitFinishService" :class="{
                        'group relative bg-green-500/20 hover:bg-green-500/30 text-green-100 hover:text-green-200 px-4 py-2 rounded-full transition-all duration-300 transform hover:scale-105 border border-green-400/20 backdrop-blur-sm': !finishServiceForm.processing,
                        'opacity-25 cursor-not-allowed px-4 py-2': finishServiceForm.processing
                    }" :disabled="finishServiceForm.processing">
                        <span class="relative z-10 flex items-center gap-2">✅ Finalizar Lavado</span>
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-green-400/20 to-cyan-400/20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
@keyframes float {

    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-10px);
    }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

@keyframes shimmer {
    0% {
        transform: translateX(-100%);
    }

    100% {
        transform: translateX(100%);
    }
}

/* Scroll suave */
html {
    scroll-behavior: smooth;
}
</style>
