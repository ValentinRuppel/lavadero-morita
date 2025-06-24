<script setup>
import { useForm } from '@inertiajs/vue3'
import { Head, Link } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { ref, onMounted, computed } from 'vue'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

const registerError = ref('')
const registerSuccess = ref('')
const isSubmitting = ref(false)
const frontendErrors = ref({})

// Validaciones en tiempo real
const nameValidation = computed(() => {
    if (!form.name) return null
    if (form.name.length < 2) return 'El nombre debe tener al menos 2 caracteres'
    if (form.name.length > 50) return 'El nombre no puede tener más de 50 caracteres'
    if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(form.name)) return 'El nombre solo puede contener letras y espacios'
    return null
})

const emailValidation = computed(() => {
    if (!form.email) return null
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!emailRegex.test(form.email)) return 'Ingresa un email válido'
    if (form.email.length > 100) return 'El email es demasiado largo'
    return null
})

const passwordValidation = computed(() => {
    if (!form.password) return null
    if (form.password.length < 8) return 'La contraseña debe tener al menos 8 caracteres'
    if (form.password.length > 100) return 'La contraseña es demasiado larga'
    if (!/(?=.*[a-z])/.test(form.password)) return 'Debe contener al menos una letra minúscula'
    if (!/(?=.*[A-Z])/.test(form.password)) return 'Debe contener al menos una letra mayúscula'
    if (!/(?=.*\d)/.test(form.password)) return 'Debe contener al menos un número'
    return null
})

const passwordConfirmationValidation = computed(() => {
    if (!form.password_confirmation) return null
    if (form.password !== form.password_confirmation) return 'Las contraseñas no coinciden'
    return null
})

// Validar si el formulario es válido
const isFormValid = computed(() => {
    return form.name &&
           form.email &&
           form.password &&
           form.password_confirmation &&
           !nameValidation.value &&
           !emailValidation.value &&
           !passwordValidation.value &&
           !passwordConfirmationValidation.value
})

// Verificar si hay mensaje de éxito desde el backend
onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search)
    if (urlParams.get('success')) {
        registerSuccess.value = 'Cuenta creada exitosamente. Ya puedes iniciar sesión.'
    }
})

function submit() {
    // Limpiar errores previos
    registerError.value = ''
    registerSuccess.value = ''
    frontendErrors.value = {}

    // Validar en el frontend antes de enviar
    const errors = {}

    if (nameValidation.value) errors.name = nameValidation.value
    if (emailValidation.value) errors.email = emailValidation.value
    if (passwordValidation.value) errors.password = passwordValidation.value
    if (passwordConfirmationValidation.value) errors.password_confirmation = passwordConfirmationValidation.value

    if (Object.keys(errors).length > 0) {
        frontendErrors.value = errors
        registerError.value = 'Por favor corrige los errores en el formulario'
        return
    }

    isSubmitting.value = true

    form.post('/register', {
        onFinish: () => {
            isSubmitting.value = false
        },
        onError: (errors) => {
            console.log('Errores del backend:', errors)
            isSubmitting.value = false

            // Manejar diferentes tipos de errores de registro
            if (errors.email && typeof errors.email === 'string' && errors.email.includes('already')) {
                registerError.value = 'Este email ya está registrado. Intenta con otro email o inicia sesión.'
            } else if (errors.name) {
                registerError.value = `Error en el nombre: ${Array.isArray(errors.name) ? errors.name[0] : errors.name}`
            } else if (errors.email) {
                registerError.value = `Error en el email: ${Array.isArray(errors.email) ? errors.email[0] : errors.email}`
            } else if (errors.password) {
                registerError.value = `Error en la contraseña: ${Array.isArray(errors.password) ? errors.password[0] : errors.password}`
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
        },
        onSuccess: () => {
            registerError.value = ''
            registerSuccess.value = '¡Cuenta creada exitosamente! Redirigiendo al login...'

            // Limpiar el formulario
            form.reset()

            // Redirigir al login después de 2 segundos
            setTimeout(() => {
                window.location.href = '/login?registered=1'
            }, 2000)
        }
    })
}

// Limpiar errores de frontend cuando el usuario corrige
function clearFieldError(field) {
    if (frontendErrors.value[field]) {
        delete frontendErrors.value[field]
        frontendErrors.value = { ...frontendErrors.value }
    }
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
                    <p class="text-sm font-medium text-yellow-100">Errores del backend:</p>
                    <pre class="text-xs text-yellow-100 mt-1 whitespace-pre-wrap">{{ JSON.stringify(form.errors, null, 2) }}</pre>
                </div>

                <!-- Debug: Errores de frontend -->
                <div v-if="Object.keys(frontendErrors).length > 0" class="mb-4 p-3 bg-orange-500/20 border border-orange-400/30 rounded-lg">
                    <p class="text-sm font-medium text-orange-100">Errores de validación:</p>
                    <pre class="text-xs text-orange-100 mt-1 whitespace-pre-wrap">{{ JSON.stringify(frontendErrors, null, 2) }}</pre>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Nombre -->
                    <div class="space-y-2">
                        <InputLabel for="name" value="Nombre completo" class="text-purple-100 font-medium" />
                        <div class="relative">
                            <TextInput id="name" type="text"
                                class="mt-1 block w-full pl-10 bg-white/10 border-purple-300/30 text-white placeholder-purple-200 focus:border-purple-400 focus:ring-purple-400 rounded-lg backdrop-blur-sm"
                                :class="{ 'border-red-400': form.errors.name || frontendErrors.name || nameValidation }"
                                v-model="form.name"
                                @input="clearFieldError('name')"
                                required autofocus autocomplete="name"
                                placeholder="Tu nombre completo" />
                        </div>
                        <InputError class="mt-2 text-pink-200" :message="form.errors.name || frontendErrors.name || nameValidation" />
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <InputLabel for="email" value="Correo electrónico" class="text-purple-100 font-medium" />
                        <div class="relative">
                            <TextInput id="email" type="email"
                                class="mt-1 block w-full pl-10 bg-white/10 border-purple-300/30 text-white placeholder-purple-200 focus:border-purple-400 focus:ring-purple-400 rounded-lg backdrop-blur-sm"
                                :class="{ 'border-red-400': form.errors.email || frontendErrors.email || emailValidation }"
                                v-model="form.email"
                                @input="clearFieldError('email')"
                                required autocomplete="username"
                                placeholder="tu@email.com" />
                        </div>
                        <InputError class="mt-2 text-pink-200" :message="form.errors.email || frontendErrors.email || emailValidation" />
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <InputLabel for="password" value="Contraseña" class="text-purple-100 font-medium" />
                        <div class="relative">
                            <TextInput id="password" type="password"
                                class="mt-1 block w-full pl-10 bg-white/10 border-purple-300/30 text-white placeholder-purple-200 focus:border-purple-400 focus:ring-purple-400 rounded-lg backdrop-blur-sm"
                                :class="{ 'border-red-400': form.errors.password || frontendErrors.password || passwordValidation }"
                                v-model="form.password"
                                @input="clearFieldError('password')"
                                required autocomplete="new-password"
                                placeholder="••••••••" />
                        </div>
                        <InputError class="mt-2 text-pink-200" :message="form.errors.password || frontendErrors.password || passwordValidation" />

                        <!-- Indicadores de fortaleza de contraseña -->
                        <div v-if="form.password" class="mt-2 space-y-1">
                            <div class="flex flex-wrap gap-2 text-xs">
                                <span :class="form.password.length >= 8 ? 'text-green-300' : 'text-red-300'">
                                    ✓ Mín. 8 caracteres
                                </span>
                                <span :class="/(?=.*[a-z])/.test(form.password) ? 'text-green-300' : 'text-red-300'">
                                    ✓ Minúscula
                                </span>
                                <span :class="/(?=.*[A-Z])/.test(form.password) ? 'text-green-300' : 'text-red-300'">
                                    ✓ Mayúscula
                                </span>
                                <span :class="/(?=.*\d)/.test(form.password) ? 'text-green-300' : 'text-red-300'">
                                    ✓ Número
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Confirmar Password -->
                    <div class="space-y-2">
                        <InputLabel for="password_confirmation" value="Confirmar contraseña" class="text-purple-100 font-medium" />
                        <div class="relative">
                            <TextInput id="password_confirmation" type="password"
                                class="mt-1 block w-full pl-10 bg-white/10 border-purple-300/30 text-white placeholder-purple-200 focus:border-purple-400 focus:ring-purple-400 rounded-lg backdrop-blur-sm"
                                :class="{ 'border-red-400': frontendErrors.password_confirmation || passwordConfirmationValidation }"
                                v-model="form.password_confirmation"
                                @input="clearFieldError('password_confirmation')"
                                required autocomplete="new-password"
                                placeholder="••••••••" />
                        </div>
                        <InputError class="mt-2 text-pink-200" :message="frontendErrors.password_confirmation || passwordConfirmationValidation" />
                    </div>

                    <!-- Submit button -->
                    <PrimaryButton type="submit"
                        :disabled="form.processing || isSubmitting || !isFormValid"
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

                    <!-- Mensaje de validación del botón -->
                    <div v-if="!isFormValid && (form.name || form.email || form.password || form.password_confirmation)"
                         class="text-center text-sm text-purple-200">
                        Complete todos los campos correctamente para continuar
                    </div>
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
