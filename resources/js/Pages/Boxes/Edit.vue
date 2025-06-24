<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    box: Object,
    user: Object,
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
            form.reset();
            router.get(route('boxes.index'), {}, {
                preserveState: false,
                preserveScroll: false,
                replace: true,
            });
        },
        onError: (errors) => {
            console.error('Errores de validación:', errors);
        }
    });
};
</script>

<template>
    <Head :title="'Editar Box: ' + props.box.nombre_box" />

    <AppLayout :user="user">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Tarjeta principal con glassmorphism -->
                <div class="bg-white/15 backdrop-blur-md rounded-2xl shadow-2xl border border-white/20 p-8 relative overflow-hidden">
                    <!-- Partículas internas flotantes -->
                    <div class="absolute top-4 right-4 w-2 h-2 bg-pink-300/40 rounded-full animate-pulse"></div>
                    <div class="absolute top-8 left-8 w-1 h-1 bg-violet-400/50 rounded-full animate-bounce" style="animation-delay: 0.5s"></div>
                    <div class="absolute bottom-6 right-12 w-3 h-3 bg-purple-200/30 rounded-full animate-pulse" style="animation-delay: 1s"></div>
                    <div class="absolute bottom-12 left-6 w-2 h-2 bg-fuchsia-300/35 rounded-full animate-bounce" style="animation-delay: 1.5s"></div>

                    <!-- Título con decoración -->
                    <div class="text-center mb-8 relative">
                        <h2 class="text-3xl font-bold bg-gradient-to-r from-purple-200 to-pink-200 bg-clip-text text-transparent mb-4 pt-6">
                            Editar Box: {{ props.box.nombre_box }}
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

                    <!-- Formulario -->
                    <form @submit.prevent="submit" class="max-w-lg mx-auto">
                        <div>
                            <InputLabel for="nombre_box" value="Nombre del Box" class="text-purple-100" />
                            <TextInput
                                id="nombre_box"
                                type="text"
                                class="mt-1 block w-full bg-white/10 border-purple-300/30 text-purple-100 focus:border-purple-500 focus:ring-purple-500"
                                v-model="form.nombre_box"
                                required
                                autofocus
                            />
                            <InputError class="mt-2 text-red-200" :message="form.errors.nombre_box" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="descripcion" value="Descripción (Opcional)" class="text-purple-100" />
                            <textarea
                                id="descripcion"
                                class="mt-1 block w-full bg-white/10 border-purple-300/30 text-purple-100 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm"
                                v-model="form.descripcion"
                            ></textarea>
                            <InputError class="mt-2 text-red-200" :message="form.errors.descripcion" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="estado" value="Estado del Box" class="text-purple-100" />
                            <select
                                id="estado"
                                class="mt-1 block w-full bg-white/10 border-purple-300/30 text-purple-100 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm"
                                v-model="form.estado"
                            >
                                <option value="activo">Activo</option>
                                <option value="mantenimiento">Mantenimiento</option>
                            </select>
                            <InputError class="mt-2 text-red-200" :message="form.errors.estado" />
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <!-- Botón Volver -->
                            <Link
                                :href="route('boxes.index')"
                                class="group relative bg-purple-500/20 hover:bg-purple-500/30 text-purple-100 px-4 py-2 rounded-full transition-all duration-300 transform hover:scale-105 border border-purple-400/20 backdrop-blur-sm"
                            >
                                <span class="relative z-10 flex items-center gap-2">
                                    ⬅ Volver al Índice
                                </span>
                                <div class="absolute inset-0 bg-gradient-to-r from-purple-400/20 to-pink-400/20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </Link>

                            <!-- Botón Actualizar -->
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="group relative bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-bold py-2 px-6 rounded-full transition-all duration-300 transform hover:scale-105 border border-white/20 backdrop-blur-sm"
                            >
                                <span class="relative z-10 flex items-center gap-2">
                                    {{ form.processing ? 'Actualizando...' : 'Actualizar Box' }}
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
