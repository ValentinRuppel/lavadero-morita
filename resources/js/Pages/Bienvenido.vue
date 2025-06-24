<template>
    <div class="min-h-screen bg-gradient-to-br from-purple-900 via-violet-800 to-indigo-900 relative overflow-hidden">
        <!-- Elementos de fondo animados -->
        <div class="absolute inset-0 overflow-hidden">
            <!-- Burbujas flotantes con colores mora -->
            <div class="absolute top-20 left-10 w-4 h-4 bg-pink-300/30 rounded-full animate-bounce"></div>
            <div class="absolute top-40 right-20 w-6 h-6 bg-purple-200/25 rounded-full animate-pulse"></div>
            <div class="absolute bottom-40 left-20 w-5 h-5 bg-violet-300/35 rounded-full animate-bounce"
                style="animation-delay: 0.5s"></div>
            <div class="absolute top-60 right-40 w-3 h-3 bg-fuchsia-200/30 rounded-full animate-pulse"
                style="animation-delay: 1s"></div>
            <div class="absolute top-80 left-40 w-4 h-4 bg-purple-300/20 rounded-full animate-bounce"
                style="animation-delay: 1.5s"></div>

            <!-- Nuevas burbujas adicionales -->
            <div class="absolute top-32 right-16 w-3 h-3 bg-violet-200/25 rounded-full animate-pulse"
                style="animation-delay: 0.3s"></div>
            <div class="absolute bottom-60 right-32 w-5 h-5 bg-pink-400/30 rounded-full animate-bounce"
                style="animation-delay: 0.8s"></div>
            <div class="absolute top-16 left-32 w-2 h-2 bg-purple-400/35 rounded-full animate-pulse"
                style="animation-delay: 1.2s"></div>
            <div class="absolute bottom-80 left-16 w-6 h-6 bg-fuchsia-300/20 rounded-full animate-bounce"
                style="animation-delay: 0.2s"></div>
            <div class="absolute top-72 right-24 w-4 h-4 bg-violet-400/30 rounded-full animate-pulse"
                style="animation-delay: 1.8s"></div>
            <div class="absolute bottom-24 right-48 w-3 h-3 bg-purple-200/40 rounded-full animate-bounce"
                style="animation-delay: 0.7s"></div>
            <div class="absolute top-48 left-24 w-5 h-5 bg-pink-200/25 rounded-full animate-pulse"
                style="animation-delay: 1.4s"></div>
            <div class="absolute bottom-72 right-12 w-2 h-2 bg-violet-300/45 rounded-full animate-bounce"
                style="animation-delay: 0.4s"></div>
            <div class="absolute top-24 right-52 w-4 h-4 bg-fuchsia-400/25 rounded-full animate-pulse"
                style="animation-delay: 1.6s"></div>
            <div class="absolute bottom-48 left-48 w-6 h-6 bg-purple-300/30 rounded-full animate-bounce"
                style="animation-delay: 0.9s"></div>
            <div class="absolute top-88 left-28 w-3 h-3 bg-pink-400/35 rounded-full animate-pulse"
                style="animation-delay: 2s"></div>
            <div class="absolute bottom-16 right-28 w-5 h-5 bg-violet-200/30 rounded-full animate-bounce"
                style="animation-delay: 0.6s"></div>
            <div class="absolute top-52 right-36 w-2 h-2 bg-purple-400/25 rounded-full animate-pulse"
                style="animation-delay: 1.3s"></div>
            <div class="absolute bottom-88 left-36 w-4 h-4 bg-fuchsia-200/40 rounded-full animate-bounce"
                style="animation-delay: 0.1s"></div>
            <div class="absolute top-36 left-52 w-3 h-3 bg-pink-300/20 rounded-full animate-pulse"
                style="animation-delay: 1.7s"></div>
            <!-- Ondas de agua con tonos mora -->
            <div class="absolute bottom-0 left-0 w-full h-32 bg-gradient-to-t from-purple-800/40 to-transparent">
                <div
                    class="absolute bottom-0 w-full h-16 bg-gradient-to-r from-violet-600/20 via-purple-400/20 to-violet-600/20 animate-pulse">
                </div>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="relative z-10 min-h-screen">

            <!-- Hero Section -->
            <div class="flex flex-col items-center justify-center text-center p-6 min-h-screen">
                <!-- Logo y efectos -->
                <div class="relative mb-8">
                    <div class="absolute inset-0 bg-purple-300/20 rounded-full blur-xl animate-pulse"></div>
                    <img src="/storage/Logo_renovado.png" alt="Morita Logo"
                        class="w-56 h-56 rounded-full object-cover shadow-inner" />
                </div>
                <!-- Título principal con efectos -->
                <div class="mb-6">
                    <h1 class="text-5xl md:text-7xl font-black text-white mb-2 drop-shadow-lg">
                        <span
                            class="bg-gradient-to-r from-white via-purple-100 to-pink-100 bg-clip-text text-transparent">
                            Morita
                        </span>
                    </h1>
                    <h2 class="text-2xl md:text-3xl font-bold text-purple-100 tracking-wide">
                        LAVADERO DE AUTOS
                    </h2>
                </div>

                <!-- Eslogan con estilo -->
                <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-2xl font-light leading-relaxed">
                    ¡Cuidamos tu vehículo como si fuera el nuestro!
                </p>

                <!-- Servicios destacados -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10 max-w-4xl">
                    <div
                        class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-purple-300/20 hover:bg-purple-200/20 transition-all duration-300 transform hover:scale-105">
                        <div class="text-3xl mb-2">🧽</div>
                        <p class="text-white text-sm font-medium">Lavado Completo</p>
                    </div>
                    <div
                        class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-purple-300/20 hover:bg-purple-200/20 transition-all duration-300 transform hover:scale-105">
                        <div class="text-3xl mb-2">✨</div>
                        <p class="text-white text-sm font-medium">Encerado</p>
                    </div>
                    <div
                        class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-purple-300/20 hover:bg-purple-200/20 transition-all duration-300 transform hover:scale-105">
                        <div class="text-3xl mb-2">🔧</div>
                        <p class="text-white text-sm font-medium">Detailing</p>
                    </div>
                    <div
                        class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-purple-300/20 hover:bg-purple-200/20 transition-all duration-300 transform hover:scale-105">
                        <div class="text-3xl mb-2">🏃‍♂️</div>
                        <p class="text-white text-sm font-medium">Servicio Rápido</p>
                    </div>
                </div>

                <!-- Botones de acción mejorados -->
                <div class="flex flex-col sm:flex-row gap-4 mb-8">
                    <Link href="/login"
                        class="group relative bg-white text-purple-700 px-8 py-4 rounded-full font-bold text-lg shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:scale-105 hover:bg-purple-50 border-2 border-purple-200/20">
                    <span class="relative z-10 flex items-center gap-2">
                        Iniciar Sesión
                    </span>
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-purple-400/20 to-violet-400/20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    </div>
                    </Link>

                    <Link href="/register"
                        class="group relative bg-gradient-to-r from-fuchsia-500 to-pink-500 text-white px-8 py-4 rounded-full font-bold text-lg shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:scale-105 border-2 border-purple-200/20">
                    <span class="relative z-10 flex items-center gap-2">
                        Registrarse
                    </span>
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-pink-600 to-purple-600 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    </div>
                    </Link>
                </div>

                <!-- Scroll indicator -->
                <div class="animate-bounce text-white/70 text-sm">
                    <div class="text-2xl mb-2">⬇️</div>
                    <p>Conoce más sobre nosotros</p>
                </div>
            </div>

            <!-- Sección Quiénes Somos -->
            <section class="py-20 px-6 bg-gradient-to-b from-transparent to-purple-800/20">
                <div class="">
                    <div class="text-center mb-16">
                        <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
                            <span class="bg-gradient-to-r from-purple-200 to-pink-200 bg-clip-text text-transparent">
                                ¿Quiénes Somos?
                            </span>
                        </h2>
                        <div class="w-24 h-1 bg-gradient-to-r from-purple-400 to-pink-400 mx-auto rounded-full"></div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-12 items-center">
                        <div class="space-y-6">
                            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-purple-300/20">
                                <h3 class="text-2xl font-bold text-white mb-4 flex items-center gap-3">
                                    Lavadero Morita
                                </h3>
                                <p class="text-purple-100 leading-relaxed">
                                    Somos una empresa de lavado de vehículos habituada en Comodoro Rivadavia,
                                    prestamos toda clase de servicios de lavado por mas de 2 años de servicio.
                                </p>
                            </div>

                            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-purple-300/20">
                                <h3 class="text-2xl font-bold text-white mb-4 flex items-center gap-3">
                                    Nuestra Misión
                                </h3>
                                <p class="text-purple-100 leading-relaxed">
                                    Brindar el mejor servicio de lavado y cuidado automotriz, prestamos los mejores
                                    productos
                                    y un equipo altamente capacitado para superar las expectativas de nuestros clientes.
                                </p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div
                                class="bg-gradient-to-br from-purple-600/20 to-violet-600/20 backdrop-blur-sm rounded-2xl p-8 border border-purple-300/20">
                                <h3 class="text-2xl font-bold text-white mb-6 text-center">Somos...</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center text-2xl">
                                            1</div>
                                        <div>
                                            <h4 class="font-bold text-white">Confiables</h4>
                                            <p class="text-purple-200 text-sm">Tu vehículo está en las mejores manos</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-12 h-12 bg-violet-500 rounded-full flex items-center justify-center text-2xl">
                                            2</div>
                                        <div>
                                            <h4 class="font-bold text-white">Eficientes</h4>
                                            <p class="text-purple-200 text-sm">Servicio rápido sin comprometer la
                                                calidad</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-12 h-12 bg-fuchsia-500 rounded-full flex items-center justify-center text-2xl">
                                            3</div>
                                        <div>
                                            <h4 class="font-bold text-white">Profesionales</h4>
                                            <p class="text-purple-200 text-sm">Nuestro servicio consta de todos </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Sección Nuestros Servicios -->
            <section class="py-20 px-6 bg-gradient-to-b from-purple-800/20 to-transparent">
                <div class="max-w-6xl mx-auto">
                    <div class="text-center mb-16">
                        <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
                            <span class="bg-gradient-to-r from-purple-200 to-pink-200 bg-clip-text text-transparent">
                                Nuestros Servicios
                            </span>
                        </h2>
                        <div class="w-24 h-1 bg-gradient-to-r from-purple-400 to-pink-400 mx-auto rounded-full"></div>
                    </div>

                    <div class="grid md:grid-cols-3 gap-8">
                        <div
                            class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-purple-300/20 hover:transform hover:scale-105 transition-all duration-300">
                            <h3 class="text-xl font-bold text-white mb-4 text-center">Lavado Rapido</h3>
                            <div class="text-center">
                                <span class="text-2xl font-bold text-purple-200">$5,000</span>
                            </div>
                        </div>

                        <div
                            class="bg-gradient-to-br from-purple-600/20 to-violet-700/20 backdrop-blur-sm rounded-2xl p-8 border border-purple-300/30 hover:transform hover:scale-105 transition-all duration-300 ring-2 ring-purple-400/50">
                            <h3 class="text-xl font-bold text-white mb-4 text-center">Espuma Activa</h3>
                            <div class="text-center">
                                <span class="text-2xl font-bold text-purple-200">$7,000</span>
                            </div>
                        </div>

                        <div
                            class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-purple-300/20 hover:transform hover:scale-105 transition-all duration-300">
                            <h3 class="text-xl font-bold text-white mb-4 text-center">Solo cera</h3>
                            <div class="text-center">
                                <span class="text-2xl font-bold text-purple-200">$6,000</span>
                            </div>
                        </div>
                        
                        <div
                            class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-purple-300/20 hover:transform hover:scale-105 transition-all duration-300">
                            <h3 class="text-xl font-bold text-white mb-4 text-center">Limpieza de LLantas</h3>
                            <div class="text-center">
                                <span class="text-2xl font-bold text-purple-200">$5,000</span>
                            </div>
                        </div>
                        <div
                            class="bg-gradient-to-br from-purple-600/20 to-violet-700/20 backdrop-blur-sm rounded-2xl p-8 border border-purple-300/30 hover:transform hover:scale-105 transition-all duration-300 ring-2 ring-purple-400/50">
                            <h3 class="text-xl font-bold text-white mb-4 text-center">Lavado Chasis</h3>
                            <div class="text-center">
                                <span class="text-2xl font-bold text-purple-200">$10,000</span>
                            </div>
                        </div>
                        <div
                            class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-purple-300/20 hover:transform hover:scale-105 transition-all duration-300">
                            <h3 class="text-xl font-bold text-white mb-4 text-center">Combo Rapido Total</h3>
                            <div class="text-center">
                                <span class="text-2xl font-bold text-purple-200">$15,000</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Footer / Contacto -->
            <footer class="py-12 px-6 bg-gradient-to-t from-purple-900 to-transparent">
                <div class="max-w-4xl mx-auto text-center">
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-purple-300/20">
                        <h3 class="text-2xl font-bold text-white mb-6">Visitanos</h3>
                        <div class="grid md:grid-cols-3 gap-6 text-purple-100">
                            <div>
                                <h4 class="font-bold text-white mb-2">Teléfono</h4>
                                <p>+54 297 507-4502</p>
                            </div>
                            <div>
                                <h4 class="font-bold text-white mb-2">Email</h4>
                                <p>info@moritaslavadero.com</p>
                            </div>
                            <div>
                                <h4 class="font-bold text-white mb-2">Dirección</h4>
                                <p>Rivadavia 513<br>Comodoro Rivadavia</p>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

// Opcional: mostrar logo si existe
const logoDisponible = true
</script>

<style scoped>
@keyframes float {

    0%,
    100% {
        transform: translateY(0px);
    }

    50% {
        transform: translateY(-10px);
    }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

/* Animación personalizada para las burbujas */
@keyframes bubble {
    0% {
        transform: translateY(0) scale(1);
        opacity: 0.7;
    }

    50% {
        transform: translateY(-20px) scale(1.1);
        opacity: 1;
    }

    100% {
        transform: translateY(-40px) scale(0.9);
        opacity: 0.3;
    }
}

/* Scroll suave */
html {
    scroll-behavior: smooth;
}
</style>
