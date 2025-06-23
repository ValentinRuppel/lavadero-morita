<script setup>
import { useForm } from '@inertiajs/vue3'
import { Head, Link } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

function submit() {
    form.post('/register')
}
</script>

<template>
    <GuestLayout>

        <Head title="Registro" />

        <div class="min-h-screen flex items-center justify-center bg-gray-100">
            <div class="w-full max-w-md p-6 bg-white rounded shadow">
                <h1 class="text-xl font-bold text-center mb-4">Registrarse</h1>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block mb-1">Nombre</label>
                        <input v-model="form.name" class="w-full border px-3 py-2 rounded" type="text" />
                        <div v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</div>
                    </div>

                    <div>
                        <label class="block mb-1">Email</label>
                        <input v-model="form.email" class="w-full border px-3 py-2 rounded" type="email" />
                        <div v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</div>
                    </div>

                    <div>
                        <label class="block mb-1">Contraseña</label>
                        <input v-model="form.password" class="w-full border px-3 py-2 rounded" type="password" />
                        <div v-if="form.errors.password" class="text-red-500 text-sm">{{ form.errors.password }}</div>
                    </div>

                    <div>
                        <label class="block mb-1">Confirmar contraseña</label>
                        <input v-model="form.password_confirmation" class="w-full border px-3 py-2 rounded"
                            type="password" />
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Registrarse
                    </button>

                    <div class="text-sm text-center mt-2">
                        ¿Ya tenés cuenta?
                        <Link href="/login" class="text-blue-600 hover:underline">Iniciar sesión</Link>
                    </div>
                </form>
            </div>
        </div>
    </GuestLayout>
</template>
