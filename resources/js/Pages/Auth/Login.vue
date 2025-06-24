<script setup>
import Checkbox from '@/Components/Checkbox.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'

defineProps({
    canResetPassword: Boolean,
    status: String,
})

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const loginError = ref('')
const registrationSuccess = ref('')
const isSubmitting = ref(false)

// Verificar si viene desde registro exitoso
onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search)
    if (urlParams.get('registered')) {
        registrationSuccess.value = '¡Cuenta creada exitosamente! Ya puedes iniciar sesión con tus credenciales.'
        // Limpiar la URL después de mostrar el mensaje
        window.history.replaceState({}, document.title, window.location.pathname)
    }
})

const submit = () => {
    loginError.value = ''
    registrationSuccess.value = ''
    isSubmitting.value = true

    form.post(route('login'), {
        onFinish: () => {
            form.reset('password')
            isSubmitting.value = false
        },
        onError: (errors) => {
            console.log('Errores completos recibidos:', errors)
            console.log('form.errors:', form.errors)

            // Resetear el estado de envío
            isSubmitting.value = false

            // Buscar el error más específico
            if (errors.email) {
                loginError.value = Array.isArray(errors.email) ? errors.email[0] : errors.email
            } else if (errors.password) {
                loginError.value = Array.isArray(errors.password) ? errors.password[0] : errors.password
            } else if (errors.credentials) {
                loginError.value = Array.isArray(errors.credentials) ? errors.credentials[0] : errors.credentials
            } else if (errors.login) {
                loginError.value = Array.isArray(errors.login) ? errors.login[0] : errors.login
            } else if (errors.message) {
                loginError.value = Array.isArray(errors.message) ? errors.message[0] : errors.message
            } else {
                // Si hay otros errores, tomar el primero disponible
                const errorKeys = Object.keys(errors)
                if (errorKeys.length > 0) {
                    const firstError = errors[errorKeys[0]]
                    loginError.value = Array.isArray(firstError) ? firstError[0] : firstError || 'Error inesperado.'
                } else {
                    loginError.value = 'Error al iniciar sesión. Verifique sus credenciales.'
                }
            }
        },
        onSuccess: () => {
            loginError.value = ''
            isSubmitting.value = false
        }
    })
}
</script>

<template>
    <Head title="Ingreso Cliente" />
    <GuestLayout>
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
                <h2 class="text-2xl font-bold text-white mb-6 text-center">Iniciar Sesión</h2>

                <!-- Mensaje de registro exitoso -->
                <div v-if="registrationSuccess" class="mb-4 p-3 bg-green-500/20 border border-green-400/30 rounded-lg animate-pulse">
                    <p class="text-sm font-medium text-green-100">{{ registrationSuccess }}</p>
                </div>

                <!-- Mensajes de estado -->
                <div v-if="status" class="mb-4 p-3 bg-green-500/20 border border-green-400/30 rounded-lg">
                    <p class="text-sm font-medium text-green-100">{{ status }}</p>
                </div>

                <!-- Mensaje de error personalizado -->
                <div v-if="loginError" class="mb-4 p-3 bg-red-500/20 border border-red-400/30 rounded-lg animate-pulse">
                    <p class="text-sm font-medium text-red-100">{{ loginError }}</p>
                </div>

                <!-- Debug: Mostrar todos los errores del formulario (solo en desarrollo) -->
                <div v-if="Object.keys(form.errors).length > 0" class="mb-4 p-3 bg-yellow-500/20 border border-yellow-400/30 rounded-lg">
                    <p class="text-sm font-medium text-yellow-100">Debug - Errores del formulario:</p>
                    <pre class="text-xs text-yellow-100 mt-1 whitespace-pre-wrap">{{ JSON.stringify(form.errors, null, 2) }}</pre>
                </div>


                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Email -->
                    <div class="space-y-2">
                        <InputLabel for="email" value="Correo electrónico" class="text-purple-100 font-medium" />
                        <div class="relative">
                            <TextInput id="email" type="email"
                                class="mt-1 block w-full pl-10 bg-white/10 border-purple-300/30 text-white placeholder-purple-200 focus:border-purple-400 focus:ring-purple-400 rounded-lg backdrop-blur-sm"
                                :class="{ 'border-red-400': form.errors.email || loginError }"
                                v-model="form.email" required autofocus autocomplete="username"
                                placeholder="tu@email.com" />
                        </div>
                        <InputError class="mt-2 text-pink-200" :message="form.errors.email" />
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <InputLabel for="password" value="Contraseña" class="text-purple-100 font-medium" />
                        <div class="relative">
                            <TextInput id="password" type="password"
                                class="mt-1 block w-full pl-10 bg-white/10 border-purple-300/30 text-white placeholder-purple-200 focus:border-purple-400 focus:ring-purple-400 rounded-lg backdrop-blur-sm"
                                :class="{ 'border-red-400': form.errors.password || loginError }"
                                v-model="form.password" required autocomplete="current-password"
                                placeholder="••••••••" />
                        </div>
                        <InputError class="mt-2 text-pink-200" :message="form.errors.password" />
                    </div>

                    <!-- Remember me -->
                    <div class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember"
                            class="text-purple-400 focus:ring-purple-400 focus:ring-offset-purple-900" />
                        <span class="ml-2 text-sm text-purple-100">Recordarme</span>
                    </div>

                    <!-- Forgot password link -->
                    <div class="text-center">
                        <Link v-if="canResetPassword" :href="route('password.request')"
                            class="text-sm text-purple-200 hover:text-purple-100 underline transition-colors duration-200">
                        ¿Olvidaste tu contraseña?
                        </Link>
                    </div>

                    <!-- Submit button -->
                    <PrimaryButton type="submit" :disabled="form.processing || isSubmitting"
                        class="w-full bg-gradient-to-r from-purple-600 to-violet-600 hover:from-purple-700 hover:to-violet-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 border-2 border-purple-400/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                        <span v-if="form.processing || isSubmitting" class="flex items-center justify-center gap-2">
                            <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin">
                            </div>
                            Ingresando...
                        </span>
                        <span v-else class="flex items-center justify-center gap-2">
                            🔑 Ingresar
                        </span>
                    </PrimaryButton>
                </form>

                <!-- Divider -->
                <div class="my-6 flex items-center">
                    <div class="flex-1 h-px bg-gradient-to-r from-transparent via-purple-400/30 to-transparent"></div>
                    <span class="px-4 text-purple-200 text-sm">o</span>
                    <div class="flex-1 h-px bg-gradient-to-r from-transparent via-purple-400/30 to-transparent"></div>
                </div>

                <!-- Back to home -->
                <div class="text-center">
                    <Link href="/"
                        class="inline-flex items-center gap-2 text-purple-200 hover:text-purple-100 text-sm transition-colors duration-200">
                    ← Volver al inicio
                    </Link>
                </div>
            </div>
            <br>
            <br>
            <!-- Additional info -->
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

/* Animaciones de burbujas */
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
