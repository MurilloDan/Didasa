<script setup>
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: { type: Boolean },
    status:           { type: String },
});

const form = useForm({
    email:    '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="min-h-screen bg-didasa-black flex items-center justify-center p-4">
        <Head title="Iniciar sesión — Didasa" />

        <div class="w-full max-w-md">

            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="w-32 h-32 rounded-full bg-white flex items-center justify-center mx-auto mb-4 shadow-lg">
                  <img src="/images/logo.png" alt="Tecnicentro DIDASA" class="h-24 w-auto" />
                </div>
                <h1 class="text-2xl font-black text-white tracking-widest">TECNICENTRO DIDASA</h1>
                <p class="text-sm text-gray-400 mt-1">Centro de servicio automotriz integral</p>
                <p class="text-xs text-gray-500 mt-0.5">Mantenimiento · Reparación · Auto partes</p>
            </div>

            <!-- Tarjeta -->
            <div class="bg-white rounded-3xl shadow-2xl p-8 border-t-4 border-didasa-red">

                <div v-if="status" class="mb-5 text-sm font-medium text-green-600 bg-green-50 rounded-xl px-4 py-3 text-center">
                    {{ status }}
                </div>

                <h2 class="text-xl font-black text-didasa-black mb-6">Iniciar sesión</h2>

                <form @submit.prevent="submit" class="space-y-5">

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Correo electrónico
                        </label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="admin@didasa.com"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-didasa-red focus:border-transparent transition"
                        />
                        <InputError class="mt-1.5" :message="form.errors.email" />
                    </div>

                    <!-- Contraseña -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Contraseña
                            </label>
                            <a
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-xs text-didasa-red hover:underline"
                            >¿Olvidaste tu contraseña?</a>
                        </div>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-didasa-red focus:border-transparent transition"
                        />
                        <InputError class="mt-1.5" :message="form.errors.password" />
                    </div>

                    <!-- Recordarme -->
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input
                            v-model="form.remember"
                            type="checkbox"
                            class="w-4 h-4 rounded text-didasa-red border-gray-300 focus:ring-didasa-red"
                        />
                        <span class="text-sm text-gray-600">Mantener sesión iniciada</span>
                    </label>

                    <!-- Botón -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-3 px-4 bg-didasa-red hover:bg-didasa-redhov disabled:opacity-60 text-white font-black rounded-xl shadow-md transition-all duration-200 flex items-center justify-center gap-2 tracking-wide"
                    >
                        <svg v-if="form.processing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                        </svg>
                        {{ form.processing ? 'Ingresando...' : 'Ingresar' }}
                    </button>

                </form>
            </div>

            <!-- Volver al kiosko -->
            <p class="text-center text-sm text-gray-400 mt-6">
                <a href="/" class="hover:text-didasa-gold transition-colors">
                    &larr; Volver al kiosko de evaluación
                </a>
            </p>

        </div>
    </div>
</template>
