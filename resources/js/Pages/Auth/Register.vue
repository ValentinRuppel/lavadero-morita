<script setup>
import { useForm } from '@inertiajs/vue3'
import { Head, Link } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { ref, onMounted } from 'vue'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

const registerError = ref('')
const registerSuccess = ref('')
const isSubmitting = ref(false)

// Verificar si hay mensaje de éxito desde el backend
onMounted(() => {
    // Si hay un mensaje flash de éxito, mostrarlo
    const urlParams = new URLSearchParams(window.location.search)
    if (urlParams.get('success')) {
        registerSuccess.value = 'Cuenta creada exitosamente. Ya puedes iniciar sesión.'
    }
})

function submit() {
    registerError.value = ''
    registerSuccess.value = ''
    isSubmitting.value = true

    form.post('/register', {
        onFinish: () => {
            form.reset('password', 'password_confirmation')
            isSubmitting.value = false
        },
        onError: (errors) => {
            console.log('Errores recibidos:', errors)

            // Manejar diferentes tipos de errores de registro
            if (errors.name) {
                registerError.value = `Nombre: ${errors.name}`
            } else if (errors.email) {
                registerError.value = `Email: ${errors.email}`
            } else if (errors.password) {
                registerError.value = `Contraseña: ${errors.password}`
            } else if (errors.message) {
                registerError.value = errors.message
            } else {
                // Si hay cualquier error, mostrar mensaje específico
                const errorKeys = Object.keys(errors)
                if (errorKeys.length > 0) {
                    const firstError = errors[errorKeys[0]]
                    registerError.value = Array.isArray(firstError) ? firstError[0] : firstError || 'Error al registrar usuario. Verifica los datos ingresados.'
                } else {
                    registerError.value = 'Ocurrió un error al intentar registrar. Por favor, inténtalo de nuevo.'
                }
            }
            isSubmitting.value = false
        },
        onSuccess: () => {
            registerError.value = ''
            registerSuccess.value = '¡Cuenta creada exitosamente! Redirigiendo al login...'
            isSubmitting.value = false

            // Redirigir al login después de 2 segundos
            setTimeout(() => {
                window.location.href = '/login?registered=1'
            }, 2000)
        }
    })
}
</script>

<template>
    <Head title="Registro Cliente" />
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
                <h2 class="text-2xl font-bold text-white mb-6 text-center">Crea tu Cuenta</h2>

                <!-- Mensaje de éxito -->
                <div v-if="registerSuccess" class="mb-4 p-3 bg-green-500/20 border border-green-400/30 rounded-lg animate-pulse">
                    <p class="text-sm font-medium text-green-100">{{ registerSuccess }}</p>
                </div>

                <!-- Mensaje de error personalizado -->
                <div v-if="registerError" class="mb-4 p-3 bg-red-500/20 border border-red-400/30 rounded-lg animate-pulse">
                    <p class="text-sm font-medium text-red-100">{{ registerError }}</p>
                </div>

                <!-- Debug: Mostrar todos los errores del formulario -->
                <div v-if="Object.keys(form.errors).length > 0" class="mb-4 p-3 bg-yellow-500/20 border border-yellow-400/30 rounded-lg">
                    <p class="text-sm font-medium text-yellow-100">Errores del formulario:</p>
                    <pre class="text-xs text-yellow-100 mt-1">{{ JSON.stringify(form.errors, null, 2) }}</pre>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Nombre -->
                    <div class="space-y-2">
                        <InputLabel for="name" value="Nombre completo" class="text-purple-100 font-medium" />
                        <div class="relative">
                            <TextInput id="name" type="text"
                                class="mt-1 block w-full pl-10 bg-white/10 border-purple-300/30 text-white placeholder-purple-200 focus:border-purple-400 focus:ring-purple-400 rounded-lg backdrop-blur-sm"
                                :class="{ 'border-red-400': form.errors.name }"
                                v-model="form.name" required autofocus autocomplete="name"
                                placeholder="Tu nombre completo" />
                        </div>
                        <InputError class="mt-2 text-pink-200" :message="form.errors.name" />
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <InputLabel for="email" value="Correo electrónico" class="text-purple-100 font-medium" />
                        <div class="relative">
                            <TextInput id="email" type="email"
                                class="mt-1 block w-full pl-10 bg-white/10 border-purple-300/30 text-white placeholder-purple-200 focus:border-purple-400 focus:ring-purple-400 rounded-lg backdrop-blur-sm"
                                :class="{ 'border-red-400': form.errors.email }"
                                v-model="form.email" required autocomplete="username"
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
                                :class="{ 'border-red-400': form.errors.password }"
                                v-model="form.password" required autocomplete="new-password"
                                placeholder="••••••••" />
                        </div>
                        <InputError class="mt-2 text-pink-200" :message="form.errors.password" />
                    </div>

                    <!-- Confirmar Password -->
                    <div class="space-y-2">
                        <InputLabel for="password_confirmation" value="Confirmar contraseña" class="text-purple-100 font-medium" />
                        <div class="relative">
                            <TextInput id="password_confirmation" type="password"
                                class="mt-1 block w-full pl-10 bg-white/10 border-purple-300/30 text-white placeholder-purple-200 focus:border-purple-400 focus:ring-purple-400 rounded-lg backdrop-blur-sm"
                                v-model="form.password_confirmation" required autocomplete="new-password"
                                placeholder="••••••••" />
                        </div>
                    </div>

                    <!-- Submit button -->
                    <PrimaryButton type="submit" :disabled="form.processing || isSubmitting"
                        class="w-full bg-gradient-to-r from-purple-600 to-violet-600 hover:from-purple-700 hover:to-violet-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 border-2 border-purple-400/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                        <span v-if="form.processing || isSubmitting" class="flex items-center justify-center gap-2">
                            <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin">
                            </div>
                            Registrando...
                        </span>
                        <span v-else class="flex items-center justify-center gap-2">
                            🚀 Crear cuenta
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
                    ¿Ya tienes cuenta?
                    <Link href="/login" class="text-purple-100 hover:text-white underline font-medium">
                        Inicia sesión aquí
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
