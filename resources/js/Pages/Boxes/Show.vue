<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import DangerButton from '@/Components/DangerButton.vue';

// *** Importa 'router' en lugar de 'usePage' para la navegación ***
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch, onUnmounted } from 'vue';

const page = usePage()
const showError = ref(false)
const errorMessage = ref('')
// Observa cuando llega el mensaje flash
watch(() => page.props.flash.error, (value) => {
  if (value) {
    errorMessage.value = value
    showError.value = true

    // Ocultar después de 5 segundos
    setTimeout(() => {
      showError.value = false
      errorMessage.value = ''
    }, 5000)
  }
}, { immediate: true })
const props = defineProps({
    box: Object,
    tiposLavado: Array,
    clientes: Array,
    adminId: Number,
    user: Object,
});

// *** 'page' ya no es necesario si solo lo usabas para .visit() ***
// const page = usePage();

const startServiceForm = useForm({
    box_id: props.box.id,
    user_id: '',
    vehiculo_id: '',
    tipo_lavado_id: '',
    administrador_id: props.adminId,
});
const finishServiceForm = useForm({});

const selectedClientId = ref('');
const availableVehicles = ref([]);
const showStartServiceModal = ref(false); // Asumo que tienes esta variable para el modal
// ... otras variables y funciones que tengas

// Función para verificar vehículos en el backend
const checkClientVehiclesAndRedirect = async (clientId, clientName) => {
    // Cierra el modal *antes* de la petición al backend, para una mejor UX
    showStartServiceModal.value = false;

    // Haz una petición POST a una nueva ruta en el backend
    router.post(route('client.checkAndRedirect'), {
        user_id: clientId,
        user_name: clientName,
    }, {
        onSuccess: (page) => {
            // El backend ya manejó la redirección si no hay vehículos.
            // Si el backend no redirigió (es decir, el cliente sí tiene vehículos),
            // entonces esta función onSuccess no se ejecutará, o se ejecutará si el
            // backend retorna algo distinto a una redirección que sea capturado aquí.
            // En este caso, la redirección se manejará completamente en el backend.
            console.log('Backend handled redirect or response');
        },
        onError: (errors) => {
            console.error('Error al verificar vehículos en el backend:', errors);
            // Maneja errores si la petición al backend falla por alguna razón
        },
        preserveState: false, // Queremos que se reinicie el estado si hay redirección
        preserveScroll: true,
    });
};


watch(selectedClientId, (newClientId) => {
    const client = props.clientes.find(c => c.id === newClientId);
    if (client) {
        availableVehicles.value = client.vehiculos;
        startServiceForm.vehiculo_id = '';

        // Si el cliente no tiene vehículos, llama a la función que contacta al backend
        if (availableVehicles.value.length === 0) {
            checkClientVehiclesAndRedirect(newClientId, client.name);
        }
    } else {
        availableVehicles.value = [];
        startServiceForm.vehiculo_id = '';
    }
    startServiceForm.user_id = newClientId;
});

const showPostServiceOptionsModal = ref(false); // Modal para opciones al finalizar el servicio
const newClientVehicle = ref(false);
const submitStartService = () => {
    // Asegurarse de que el user_id esté seteado si es un nuevo vehículo para un cliente existente
    if (newClientVehicle.value && selectedClientId.value) {
        startServiceForm.user_id = selectedClientId.value;
    } else if (!newClientVehicle.value && startServiceForm.vehiculo_id) {
        // Si seleccionó un vehículo existente, el user_id del formulario debe ser null
        startServiceForm.user_id = null;
    }


    startServiceForm.post(route('servicios.iniciar'), {
        onSuccess: () => {
            showStartServiceModal.value = false;
            // Inertia se encarga de los flash messages si el controlador redirige con 'with()'
            // No necesitas asignar page.props.flash.success aquí.
        },
        onError: (errors) => {
            console.error('Error al iniciar servicio:', errors);
            // Los errores de validación se inyectarán automáticamente en startServiceForm.errors
        },
        preserveScroll: true, // Mantener el scroll al redirigir
    });
};

const submitFinishService = () => {
    if (!props.box.servicio_en_curso || !props.box.servicio_en_curso.id) {
        console.error('No hay un servicio en curso para finalizar.');
        return;
    }

    const targetFinalizeUrl = route('servicios.finalizar', props.box.servicio_en_curso.id);
    console.log('submitFinishService: Intentando PUT a:', targetFinalizeUrl); // <-- AÑADE ESTA LÍNEA

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

watch(newClientVehicle, (newValue) => {
    if (!newValue) { // Si deselecciona "Registrar Nuevo Vehículo"
        startServiceForm.vehiculo_patente_nuevo = '';
        startServiceForm.vehiculo_marca_nuevo = '';
        startServiceForm.vehiculo_modelo_nuevo = '';
        startServiceForm.vehiculo_anio_nuevo = '';
    } else { // Si selecciona "Registrar Nuevo Vehículo"
        startServiceForm.vehiculo_id = ''; // Limpia la selección de vehículo existente
    }
    startServiceForm.tipo_lavado_id = ''; // Limpia el tipo de lavado para forzar una nueva selección
});

watch(() => startServiceForm.vehiculo_id, () => {
    startServiceForm.tipo_lavado_id = ''; // Limpia el tipo de lavado si el vehículo cambia
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
        clearInterval(timerInterval); // Limpiar cualquier temporizador existente
    }

    timerInterval = setInterval(() => {
        if (remainingTime.value > 0) {
            remainingTime.value--;
        } else {
            clearInterval(timerInterval);
            timerInterval = null;
            // CUANDO EL CONTADOR LLEGA A CERO, MUESTRA EL MODAL DE OPCIONES
            // Solo si el box está ocupado y hay un servicio en curso (para evitar que aparezca si ya finalizó)
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

// ESTE WATCHER ES CRÍTICO: Asegura que el timer se inicie o se detenga correctamente
// cuando las propiedades del box o el servicio cambian (por ejemplo, después de una redirección)
watch(() => props.box.servicio_en_curso, (newService) => {
    if (newService && newService.tipo_lavado && newService.estado_servicio === 'en_curso') {
        const elapsedSeconds = Math.floor((Date.now() - new Date(newService.fecha_hora_inicio).getTime()) / 1000);
        const estimatedTotalSeconds = newService.tipo_lavado.duracion_estimada * 60;
        const remaining = estimatedTotalSeconds - elapsedSeconds;

        if (remaining > 0) {
            startTimer(remaining);
        } else {
            // Si el tiempo ya pasó al cargar, establece a 0 y abre el modal de opciones
            remainingTime.value = 0;
            if (props.box.estado === 'ocupado' && newService.estado_servicio === 'en_curso') {
                showPostServiceOptionsModal.value = true;
            }
        }
    } else {
        // Si no hay servicio en curso, o el servicio ya no está 'en_curso', limpia el timer
        clearInterval(timerInterval);
        timerInterval = null;
        remainingTime.value = 0;
        showPostServiceOptionsModal.value = false; // Asegura que el modal de opciones esté cerrado
    }
}, { immediate: true, deep: true }); // 'deep: true' es importante para detectar cambios dentro de servicio_en_curso

onUnmounted(() => {
    if (timerInterval) {
        clearInterval(timerInterval);
    }
});

const submitCancelService = () => {
    if (confirm('¿Estás seguro de que quieres cancelar este servicio? El box volverá a estar activo.')) {
        finishServiceForm.post(route('servicios.cancelar', props.box.servicio_en_curso.id), {
            onSuccess: () => {
                // Inertia se encargará de la redirección y el flash message
            },
            onError: (errors) => {
                console.error('Error al cancelar servicio:', errors);
            },
            preserveScroll: true,
        });
    }
};

const currentServiceId = computed(() => props.box.servicio_en_curso?.id || null);
const currentServiceTotal = computed(() => props.box.servicio_en_curso?.precio_total_servicio || 0);
</script>

<template>
    <Head :title="'Detalle del Box: ' + props.box.nombre_box" />

    <AppLayout :user="user">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detalle del Box: {{ props.box.nombre_box }}
            </h2>
        </template>

        <div class="py-12">
        <div v-if="page.props.flash.error" class="bg-red-500 text-white p-3 rounded">
            {{ page.props.flash.error }}
        </div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

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

                        <!-- SECCIÓN DE SERVICIO EN CURSO -->
                        <div v-if="props.box.servicio_en_curso && props.box.estado === 'ocupado'" class="mt-6 p-4 bg-indigo-100 rounded-lg border border-indigo-300">
                            <h4 class="text-lg font-semibold text-indigo-800 mb-2">Servicio en Curso:</h4>
                            <p class="text-indigo-700">Vehículo: {{ props.box.servicio_en_curso.vehiculo?.patente }} {{ props.box.servicio_en_curso.vehiculo?.marca }} {{ props.box.servicio_en_curso.vehiculo?.modelo }} {{ props.box.servicio_en_curso.vehiculo?.anio }}</p>
                            <p class="text-indigo-700">Cliente: {{ props.box.servicio_en_curso.vehiculo?.cliente_nombre }}</p>
                            <p class="text-indigo-700">Tipo de Lavado Actual: {{ props.box.servicio_en_curso.tipo_lavado?.nombre_lavado }}</p>
                            <p class="text-indigo-700">Precio Total del Servicio: ${{ props.box.servicio_en_curso?.precio_total_servicio }}</p>
                            <p class="text-indigo-700">Inició: {{ new Date(props.box.servicio_en_curso.fecha_hora_inicio).toLocaleString() }}</p>
                            <p class="text-indigo-700">Asignado por: {{ props.box.servicio_en_curso.administrador?.name }}</p>

                            <div class="mt-4 text-center">
                                <p class="text-xl font-bold text-indigo-900">Tiempo Restante: {{ formatTime(remainingTime) }}</p>
                                <p v-if="remainingTime <= 0" class="text-red-600 font-semibold">¡Tiempo terminado!</p>
                            </div>

                            <!-- Botones de Finalizar/Cancelar SOLO si el tiempo no ha terminado -->
                            <div class="mt-4 flex space-x-2" v-if="remainingTime > 0">
                                <DangerButton @click="submitCancelService" :disabled="finishServiceForm.processing">
                                    Cancelar Servicio
                                </DangerButton>
                            </div>
                        </div>

                        <!-- SECCIÓN DE INICIAR NUEVO SERVICIO (BOX ACTIVO) -->
                        <div v-else-if="props.box.estado === 'activo'" class="mt-6 p-4 bg-green-50 rounded-lg border border-green-200">
                            <h4 class="text-lg font-semibold text-green-800 mb-4">Iniciar Nuevo Servicio:</h4>
                            <form @submit.prevent="submitStartService" class="space-y-4">

                                <div>
                                    <InputLabel for="selected_client_id" value="Seleccionar Cliente" />
                                    <select
                                        id="selected_client_id"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        v-model="selectedClientId"
                                        @change="startServiceForm.user_id = selectedClientId"
                                        required
                                    >
                                        <option value="">Seleccione un cliente</option>
                                        <option v-for="cliente in props.clientes" :key="cliente.id" :value="cliente.id">
                                            {{ cliente.name }} ({{ cliente.email }})
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="startServiceForm.errors.user_id" />
                                </div>

                                <div v-if="selectedClientId && !newClientVehicle">
                                    <InputLabel for="vehiculo_id" value="Seleccionar Vehículo" />
                                    <select
                                        id="vehiculo_id"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        v-model="startServiceForm.vehiculo_id"
                                        required
                                        :disabled="availableVehicles.length === 0"
                                    >
                                        <option value="">Seleccione un vehículo</option>
                                        <option v-if="availableVehicles.length === 0" disabled>No hay vehículos para este cliente.</option>
                                        <option v-for="vehiculo in availableVehicles" :key="vehiculo.id" :value="vehiculo.id">
                                            {{ vehiculo.patente }} {{ vehiculo.modelo.marca.nombre }} {{ vehiculo.modelo.nombre }} {{ vehiculo.anio }}
                                        </option>
                                    </select>
                                </div>

                                <div v-if="selectedClientId && newClientVehicle">
                                    <div>
                                        <InputLabel for="vehiculo_patente_nuevo" value="Patente del Nuevo Vehículo" />
                                        <TextInput
                                            id="vehiculo_patente_nuevo"
                                            type="text"
                                            class="mt-1 block w-full uppercase"
                                            v-model="startServiceForm.vehiculo_patente_nuevo"
                                            required
                                        />
                                        <InputError class="mt-2" :message="startServiceForm.errors.vehiculo_patente_nuevo" />
                                    </div>
                                    <div class="mt-4">
                                        <InputLabel for="vehiculo_marca_nuevo" value="Marca del Vehículo" />
                                        <TextInput
                                            id="vehiculo_marca_nuevo"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="startServiceForm.vehiculo_marca_nuevo"
                                            required
                                        />
                                        <InputError class="mt-2" :message="startServiceForm.errors.vehiculo_marca_nuevo" />
                                    </div>
                                    <div class="mt-4">
                                        <InputLabel for="vehiculo_modelo_nuevo" value="Modelo del Vehículo" />
                                        <TextInput
                                            id="vehiculo_modelo_nuevo"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="startServiceForm.vehiculo_modelo_nuevo"
                                            required
                                        />
                                        <InputError class="mt-2" :message="startServiceForm.errors.vehiculo_modelo_nuevo" />
                                    </div>
                                    <div class="mt-4">
                                        <InputLabel for="vehiculo_anio_nuevo" value="Año del Vehículo" />
                                        <TextInput
                                            id="vehiculo_anio_nuevo"
                                            type="number"
                                            class="mt-1 block w-full"
                                            v-model="startServiceForm.vehiculo_anio_nuevo"
                                            min="1900" :max="new Date().getFullYear() + 1"
                                            required
                                        />
                                        <InputError class="mt-2" :message="startServiceForm.errors.vehiculo_anio_nuevo" />
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <InputLabel for="tipo_lavado_id" value="Tipo de Lavado" />
                                    <select
                                        id="tipo_lavado_id"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        v-model="startServiceForm.tipo_lavado_id"
                                        required
                                        :disabled="!selectedClientId || (!startServiceForm.vehiculo_id && !newClientVehicle)"
                                    >
                                        <option value="">Seleccione un tipo de lavado</option>
                                        <option v-for="tipo in filteredTiposLavado" :key="tipo.id" :value="tipo.id">
                                            {{ tipo.nombre_lavado }} (${{ tipo.precio }}) - {{ tipo.duracion_estimada }} min
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="startServiceForm.errors.tipo_lavado_id" />
                                </div>

                                <div v-if="startServiceForm.tipo_lavado_id" class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                                    <p class="font-semibold text-blue-800">Costo Estimado del Servicio: ${{ estimatedPrice.toFixed(2) }}</p>
                                    <p class="font-semibold text-blue-800">Duración Estimada: {{ estimatedDuration }} minutos</p>
                                </div>

                                <div class="flex items-center justify-end mt-4">
                                    <PrimaryButton :class="{ 'opacity-25': startServiceForm.processing }" :disabled="startServiceForm.processing || (!selectedClientId) || (!startServiceForm.vehiculo_id && !newClientVehicle) || !startServiceForm.tipo_lavado_id">
                                        Iniciar Servicio
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>

                        <!-- SECCIÓN DE BOX EN MANTENIMIENTO -->
                        <div v-else-if="props.box.estado === 'mantenimiento'" class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200 text-yellow-800">
                            <h4 class="text-lg font-semibold mb-2">Box en Mantenimiento</h4>
                            <p>Este box no está disponible actualmente para servicios.</p>
                        </div>

                        <!-- ENLACES DE NAVEGACIÓN -->
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

        <!-- MODAL DE OPCIONES AL FINALIZAR EL SERVICIO -->
        <div v-if="showPostServiceOptionsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50">
            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
                <h3 class="text-xl font-semibold mb-4">Servicio Finalizado en Box {{ box.nombre_box }}</h3>

                <p class="mb-4 text-gray-700">El contador ha terminado. ¿Qué deseas hacer a continuación?</p>

                <div class="flex items-center justify-end mt-6 space-x-3">
                    <PrimaryButton @click="submitFinishService" :class="{ 'opacity-25': finishServiceForm.processing }" :disabled="finishServiceForm.processing">
                        Finalizar Lavado
                    </PrimaryButton>
                </div>
            </div>
        </div>

        <!-- MODAL PARA INICIAR UN NUEVO SERVICIO (se abre con handleContinueWashing) -->
        <div v-if="showStartServiceModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50">
            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <h3 class="text-xl font-semibold mb-4">Iniciar Nuevo Servicio en Box {{ box.nombre_box }}</h3>
                <form @submit.prevent="submitStartService" class="space-y-4">
                    <div>
                        <InputLabel for="modal_selected_client_id" value="Seleccionar Cliente" />
                        <select
                            id="modal_selected_client_id"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                            v-model="selectedClientId"
                            @change="startServiceForm.user_id = selectedClientId"
                            required
                        >
                            <option value="">Seleccione un cliente</option>
                            <option v-for="cliente in props.clientes" :key="cliente.id" :value="cliente.id">
                                {{ cliente.name }} ({{ cliente.email }})
                            </option>
                        </select>
                        <InputError class="mt-2" :message="startServiceForm.errors.user_id" />
                    </div>

                    <div v-if="selectedClientId" class="flex items-center space-x-4 mt-4">
                        <label class="inline-flex items-center">
                            <input type="radio" v-model="newClientVehicle" :value="false" class="form-radio text-indigo-600">
                            <span class="ml-2 text-gray-700">Vehículo Existente del Cliente</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" v-model="newClientVehicle" :value="true" class="form-radio text-indigo-600">
                            <span class="ml-2 text-gray-700">Registrar Nuevo Vehículo para Cliente</span>
                        </label>
                    </div>

                    <div v-if="selectedClientId && !newClientVehicle">
                        <InputLabel for="modal_vehiculo_id" value="Seleccionar Vehículo" />
                        <select
                            id="modal_vehiculo_id"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                            v-model="startServiceForm.vehiculo_id"
                            required
                            :disabled="availableVehicles.length === 0"
                        >
                            <option value="">Seleccione un vehículo</option>
                            <option v-if="availableVehicles.length === 0" disabled>No hay vehículos para este cliente.</option>
                            <option v-for="vehiculo in availableVehicles" :key="vehiculo.id" :value="vehiculo.id">
                                {{ vehiculo.patente }} ({{ vehiculo.marca }} {{ vehiculo.modelo }} {{ vehiculo.anio }})
                            </option>
                        </select>
                        <InputError class="mt-2" :message="startServiceForm.errors.vehiculo_id" />
                    </div>

                    <div v-if="selectedClientId && newClientVehicle">
                        <div>
                            <InputLabel for="modal_vehiculo_patente_nuevo" value="Patente del Nuevo Vehículo" />
                            <TextInput
                                id="modal_vehiculo_patente_nuevo"
                                type="text"
                                class="mt-1 block w-full uppercase"
                                v-model="startServiceForm.vehiculo_patente_nuevo"
                                required
                            />
                            <InputError class="mt-2" :message="startServiceForm.errors.vehiculo_patente_nuevo" />
                        </div>
                        <div class="mt-4">
                            <InputLabel for="modal_vehiculo_marca_nuevo" value="Marca del Vehículo" />
                            <TextInput
                                id="modal_vehiculo_marca_nuevo"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="startServiceForm.vehiculo_marca_nuevo"
                                required
                            />
                            <InputError class="mt-2" :message="startServiceForm.errors.vehiculo_marca_nuevo" />
                        </div>
                        <div class="mt-4">
                            <InputLabel for="modal_vehiculo_modelo_nuevo" value="Modelo del Vehículo" />
                            <TextInput
                                id="modal_vehiculo_modelo_nuevo"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="startServiceForm.vehiculo_modelo_nuevo"
                                required
                            />
                            <InputError class="mt-2" :message="startServiceForm.errors.vehiculo_modelo_nuevo" />
                        </div>
                        <div class="mt-4">
                            <InputLabel for="modal_vehiculo_anio_nuevo" value="Año del Vehículo" />
                            <TextInput
                                id="modal_vehiculo_anio_nuevo"
                                type="number"
                                class="mt-1 block w-full"
                                v-model="startServiceForm.vehiculo_anio_nuevo"
                                min="1900" :max="new Date().getFullYear() + 1"
                                required
                            />
                            <InputError class="mt-2" :message="startServiceForm.errors.vehiculo_anio_nuevo" />
                        </div>
                    </div>

                    <div class="mt-4">
                        <InputLabel for="modal_tipo_lavado_id" value="Tipo de Lavado" />
                        <select
                            id="modal_tipo_lavado_id"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                            v-model="startServiceForm.tipo_lavado_id"
                            required
                            :disabled="!selectedClientId || (!startServiceForm.vehiculo_id && !newClientVehicle)"
                        >
                            <option value="">Seleccione un tipo de lavado</option>
                            <option v-for="tipo in filteredTiposLavado" :key="tipo.id" :value="tipo.id">
                                {{ tipo.nombre_lavado }} (${{ tipo.precio }}) - {{ tipo.duracion_estimada }} min
                            </option>
                        </select>
                        <InputError class="mt-2" :message="startServiceForm.errors.tipo_lavado_id" />
                    </div>

                    <div v-if="startServiceForm.tipo_lavado_id" class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                        <p class="font-semibold text-blue-800">Costo Estimado del Servicio: ${{ estimatedPrice.toFixed(2) }}</p>
                        <p class="font-semibold text-blue-800">Duración Estimada: {{ estimatedDuration }} minutos</p>
                    </div>

                    <div class="flex items-center justify-end mt-4 space-x-3">
                        <DangerButton type="button" @click="showStartServiceModal = false">
                            Cancelar
                        </DangerButton>
                        <PrimaryButton :class="{ 'opacity-25': startServiceForm.processing }" :disabled="startServiceForm.processing || (!selectedClientId) || (!startServiceForm.vehiculo_id && !newClientVehicle) || !startServiceForm.tipo_lavado_id">
                            Iniciar Servicio
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>