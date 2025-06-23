<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const resetError = ref('');
const isSubmitting = ref(false);

const submit = () => {
    resetError.value = ''; // Limpiar errores previos
    isSubmitting.value = true;

    form.post(route('password.email'), {
        onFinish: () => {
            isSubmitting.value = false;
        },
        onError: (errors) => {
            console.log('Errores recibidos:', errors); // Para debugging

            if (errors.email) {
                resetError.value = errors.email;
            } else if (errors.message) {
                resetError.value = errors.message;
            } else {
                // Si hay cualquier error, mostrar mensaje específico
                const errorKeys = Object.keys(errors);
                if (errorKeys.length > 0) {
                    resetError.value = errors[errorKeys[0]] || 'No se pudo enviar el enlace de recuperación. Verifica tu correo electrónico.';
                } else {
                    resetError.value = 'Ocurrió un error al intentar enviar el enlace. Por favor, inténtalo de nuevo.';
                }
            }
            isSubmitting.value = false;
        },
        onSuccess: () => {
            resetError.value = '';
            isSubmitting.value = false;
        }
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Recuperar Contraseña" />

        <!-- Contenido principal -->
        <div class="relative z-10 w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="relative inline-block">
                    <div class="absolute inset-0 bg-purple-300/20 rounded-full blur-xl animate-pulse"></div>
                    <div class="relative mb-8">
                        <div class="absolute inset-0 bg-purple-300/20 rounded-full blur-xl animate-pulse"></div>
                        <img src="/storage/Logo_renovado.png" alt="Morita Logo"
                            class="w-56 h-56 rounded-full object-cover shadow-inner" />
                    </div>
                </div>
            </div>

            <!-- Formulario -->
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-purple-300/20 shadow-2xl">
                <h2 class="text-2xl font-bold text-white mb-6 text-center">Recuperar Contraseña</h2>

                <!-- Descripción -->
                <div class="text-purple-100 leading-relaxed text-center mb-6">
                    ¿Olvidaste tu contraseña? No hay problema. Solo ingresa tu correo electrónico
                    y te enviaremos un enlace para que puedas crear una nueva contraseña.
                </div>

                <!-- Mensaje de éxito -->
                <div v-if="status" class="mb-4 p-3 bg-green-500/20 border border-green-400/30 rounded-lg">
                    <p class="text-sm font-medium text-green-100">{{ status }}</p>
                </div>

                <!-- Mensaje de error personalizado -->
                <div v-if="resetError" class="mb-4 p-3 bg-red-500/20 border border-red-400/30 rounded-lg animate-pulse">
                    <p class="text-sm font-medium text-red-100">{{ resetError }}</p>
                </div>

                <!-- Debug: Mostrar todos los errores del formulario -->
                <div v-if="Object.keys(form.errors).length > 0" class="mb-4 p-3 bg-yellow-500/20 border border-yellow-400/30 rounded-lg">
                    <p class="text-sm font-medium text-yellow-100">Errores del formulario:</p>
                    <pre class="text-xs text-yellow-100 mt-1">{{ JSON.stringify(form.errors, null, 2) }}</pre>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Email -->
                    <div class="space-y-2">
                        <InputLabel for="email" value="Correo electrónico" class="text-purple-100 font-medium" />
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full pl-10 bg-white/10 border-purple-300/30 text-white placeholder-purple-200 focus:border-purple-400 focus:ring-purple-400 rounded-lg backdrop-blur-sm"
                                :class="{ 'border-red-400': form.errors.email || resetError }"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="tu@email.com"
                            />
                        </div>
                        <InputError class="mt-2 text-pink-200" :message="form.errors.email" />
                    </div>

                    <!-- Botón enviar -->
                    <PrimaryButton
                        type="submit"
                        :disabled="form.processing || isSubmitting"
                        class="w-full bg-gradient-to-r from-purple-600 to-violet-600 hover:from-purple-700 hover:to-violet-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 border-2 border-purple-400/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                    >
                        <span v-if="form.processing || isSubmitting" class="flex items-center justify-center gap-2">
                            <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                            Enviando enlace...
                        </span>
                        <span v-else class="flex items-center justify-center gap-2">
                            📧 Enviar enlace de recuperación
                        </span>
                    </PrimaryButton>

                    <!-- Divider -->
                    <div class="my-6 flex items-center">
                        <div class="flex-1 h-px bg-gradient-to-r from-transparent via-purple-400/30 to-transparent"></div>
                        <span class="px-4 text-purple-200 text-sm">o</span>
                        <div class="flex-1 h-px bg-gradient-to-r from-transparent via-purple-400/30 to-transparent"></div>
                    </div>

                    <!-- Links de navegación -->
                    <div class="space-y-3">
                        <!-- Volver al login -->
                        <div class="text-center">
                            <Link :href="route('login')"
                                class="inline-flex items-center gap-2 text-purple-200 hover:text-purple-100 text-sm transition-colors duration-200">
                                🔑 Volver al inicio de sesión
                            </Link>
                        </div>

                        <!-- Volver al inicio -->
                        <div class="text-center">
                            <Link href="/"
                                class="inline-flex items-center gap-2 text-purple-200 hover:text-purple-100 text-sm transition-colors duration-200">
                                ← Volver al inicio
                            </Link>
                        </div>
                    </div>
                </form>
            </div>

            <br>
            <br>

            <!-- Información adicional -->
            <div class="mt-6 text-center">
                <p class="text-purple-200 text-sm">
                    ¿No tienes cuenta?
                    <Link href="/register" class="text-purple-100 hover:text-white underline font-medium">
                        Regístrate aquí
                    </Link>
                </p>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
/* Estilos personalizados para inputs */
input::placeholder {
    color: rgb(196 181 253 / 0.7);
}

input:focus::placeholder {
    color: rgb(196 181 253 / 0.5);
}

/* Animaciones */
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
</style>
