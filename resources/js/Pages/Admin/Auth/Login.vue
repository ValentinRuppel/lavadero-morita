<script setup>
import Checkbox from '@/Components/Checkbox.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

defineProps({
    canResetPassword: Boolean,
    status: String,
})

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const submit = () => {
    form.post(route('admin.login'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <GuestLayout>
        <Head title="Ingreso Administrador" />

        <div class="w-[65vw] flex items-center justify-center bg-gradient-to-br from-[#caf0f8] to-[#0077b6] rounded-xl h-[65vh] mx-auto my-10 shadow-lg">
    <div class="bg-white rounded-xl shadow-lg p-10 items-center justify-center px-8 py-6 w-full max-w-md center mx-auto">
        <h2 class="text-3xl font-bold text-[#0077b6] mb-8 text-center">Panel de Administración</h2>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="email" value="Correo electrónico" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Contraseña" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center">
                <Checkbox name="remember" v-model:checked="form.remember" />
                <span class="ml-2 text-sm text-gray-600">Recordarme</span>
            </div>

            <PrimaryButton :disabled="form.processing" class="w-full bg-[#0077b6] hover:bg-[#005f8a]">
                INGRESAR AL PANEL
            </PrimaryButton>
        </form>
    </div>
</div>

    </GuestLayout>
</template>
