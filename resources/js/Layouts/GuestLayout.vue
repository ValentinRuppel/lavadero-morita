<template>
    <div class="min-h-screen bg-gradient-to-br from-purple-900 via-violet-800 to-indigo-900 relative overflow-hidden">
        <!-- Burbujas decorativas -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-20 left-10 w-4 h-4 bg-pink-300/30 rounded-full animate-bounce"></div>
            <div class="absolute top-40 right-20 w-6 h-6 bg-purple-200/25 rounded-full animate-pulse"></div>
            <div class="absolute bottom-40 left-20 w-5 h-5 bg-violet-300/35 rounded-full animate-bounce" style="animation-delay: 0.5s"></div>
            <div class="absolute top-60 right-40 w-3 h-3 bg-fuchsia-200/30 rounded-full animate-pulse" style="animation-delay: 1s"></div>
            <div class="absolute top-80 left-40 w-4 h-4 bg-purple-300/20 rounded-full animate-bounce" style="animation-delay: 1.5s"></div>
            <div class="absolute bottom-0 left-0 w-full h-32 bg-gradient-to-t from-purple-800/40 to-transparent">
                <div class="absolute bottom-0 w-full h-16 bg-gradient-to-r from-violet-600/20 via-purple-400/20 to-violet-600/20 animate-pulse"></div>
            </div>
        </div>

        <!-- Contenido principal (slot) -->
        <div class="relative z-10 flex items-center justify-center min-h-screen px-4">
            <slot />
        </div>
    </div>
</template>

<script setup>
// No se requiere lógica por ahora
</script>

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
</style>

