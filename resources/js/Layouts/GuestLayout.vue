<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0"
    >
        <div>
            <Link href="/">
                <!--<ApplicationLogo class="h-20 w-20 fill-current text-gray-500" />-->
                <img src="/storage/logo.png" alt="Logo Lavadero" class="w-24 h-auto mx-auto" />
            </Link>
        </div>

        <div class="w-[75vw] h-[75vh] bg-white flex items-center justify-center mx-auto">
            <slot />
        </div>

    </div>
</template>
