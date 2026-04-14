<template>
  <div class="h-screen bg-gray-50 flex flex-col overflow-hidden">

    <!-- Header -->
    <header class="bg-didasa-black border-b border-gray-800 px-6 py-4 flex items-center justify-between sticky top-0 z-10 shadow">
      <div class="flex items-center gap-3">
        <div class="bg-white rounded-lg p-1 flex items-center justify-center shadow-sm"><img src="/images/logo.png" alt="Tecnicentro DIDASA" class="h-9 w-auto" /></div>
        <div>
          <h1 class="font-black text-white tracking-wide">TECNICENTRO DIDASA</h1>
          <p class="text-xs text-gray-400">Mantenimiento · Reparación · Auto partes</p>
        </div>
      </div>
      <div class="flex items-center gap-3">
        <span class="hidden md:block text-sm text-gray-400">{{ page.props.auth.user.name }}</span>
        <button @click="cerrarSesion" class="hidden md:flex items-center gap-1.5 text-xs text-gray-400 hover:text-white border border-gray-700 hover:border-gray-500 px-3 py-1.5 rounded-lg transition-colors">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
          Salir
        </button>
        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-400 hover:text-white">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </div>
    </header>

    <div class="flex flex-1 overflow-hidden">

      <!-- Sidebar -->
      <aside
        :class="['bg-white border-r border-gray-200 flex-shrink-0 transition-all duration-200 z-20',
                 sidebarOpen ? 'w-56' : 'w-0 md:w-56',
                 'overflow-hidden']"
      >
        <nav class="w-56 py-4">
          <div class="px-4 pb-3 mb-2 border-b border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Menú</p>
          </div>
          <ul class="space-y-0.5 px-2">
            <li v-for="item in menuItems" :key="item.label">
              <a
                :href="item.href"
                :class="[
                  'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors',
                  item.active
                    ? 'bg-didasa-red text-white'
                    : 'text-gray-600 hover:bg-gray-100 hover:text-didasa-red'
                ]"
              >
                <span class="text-lg leading-none">{{ item.icon }}</span>
                {{ item.label }}
              </a>
            </li>
          </ul>
          <div class="mt-6 px-4 border-t border-gray-100 pt-4 space-y-1">
            <div class="px-3 py-2.5 flex items-center gap-2.5">
              <div class="w-7 h-7 rounded-full bg-didasa-red flex items-center justify-center text-white text-xs font-black flex-shrink-0">
                {{ page.props.auth.user.name.charAt(0).toUpperCase() }}
              </div>
              <span class="text-xs text-gray-700 font-medium truncate">{{ page.props.auth.user.name }}</span>
            </div>
            <a href="/" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-didasa-red transition-colors">
              <span class="text-lg">🖥️</span>
              Ir al Kiosko
            </a>
            <button @click="cerrarSesion" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-red-400 hover:bg-red-50 hover:text-didasa-red transition-colors">
              <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
              Cerrar sesión
            </button>
          </div>
        </nav>
      </aside>

      <!-- Contenido principal -->
      <main class="flex-1 min-w-0 px-4 md:px-8 py-8 overflow-y-auto">

        <!-- Título + botón nuevo -->
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
          <div>
            <h2 class="text-2xl font-black text-didasa-black">Talleres</h2>
            <p class="text-sm text-gray-500 mt-0.5">Gestión de talleres registrados</p>
          </div>
          <button
            @click="abrirModal()"
            class="flex items-center gap-2 bg-didasa-red hover:bg-didasa-redhov text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors"
          >
            <span class="text-lg leading-none">+</span> Nuevo taller
          </button>
        </div>

        <!-- Tabla desktop / Cards mobile -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
          <!-- Desktop table -->
          <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                  <th class="px-6 py-3 text-left">ID</th>
                  <th class="px-6 py-3 text-left">Nombre</th>
                  <th class="px-6 py-3 text-left">Ciudad</th>
                  <th class="px-6 py-3 text-center">Estado</th>
                  <th class="px-6 py-3 text-center">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
                <tr v-if="talleres.length === 0">
                  <td colspan="5" class="py-16 text-center text-gray-400">No hay talleres registrados.</td>
                </tr>
                <tr v-for="taller in talleres" :key="taller.id" class="hover:bg-gray-50 transition-colors">
                  <td class="px-6 py-4 font-mono text-gray-400 text-xs">{{ taller.id }}</td>
                  <td class="px-6 py-4 font-medium text-gray-800">{{ taller.name }}</td>
                  <td class="px-6 py-4 text-gray-500">{{ taller.city }}</td>
                  <td class="px-6 py-4 text-center">
                    <span :class="['text-xs font-semibold px-2.5 py-1 rounded-full', taller.active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500']">
                      {{ taller.active ? 'Activo' : 'Inactivo' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                      <button @click="abrirModal(taller)" class="text-xs text-didasa-red hover:underline font-medium">Editar</button>
                      <span class="text-gray-300">|</span>
                      <button @click="eliminar(taller)" class="text-xs text-gray-400 hover:text-red-600 hover:underline font-medium">Eliminar</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- Mobile cards -->
          <div class="md:hidden divide-y divide-gray-100">
            <div v-if="talleres.length === 0" class="py-16 text-center text-gray-400 text-sm">No hay talleres registrados.</div>
            <div v-for="taller in talleres" :key="taller.id" class="flex items-center justify-between px-4 py-4 gap-3">
              <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-800 truncate">{{ taller.name }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ taller.city }}</p>
              </div>
              <span :class="['text-xs font-semibold px-2.5 py-1 rounded-full flex-shrink-0', taller.active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500']">
                {{ taller.active ? 'Activo' : 'Inactivo' }}
              </span>
              <div class="flex items-center gap-3 flex-shrink-0">
                <button @click="abrirModal(taller)" class="text-xs text-didasa-red font-semibold">Editar</button>
                <button @click="eliminar(taller)" class="text-xs text-gray-400">Eliminar</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Contador -->
        <p class="text-xs text-gray-400 mt-3">{{ talleres.length }} taller(es) registrado(s)</p>

      </main>
    </div>

    <!-- Modal crear/editar -->
    <div v-if="modal.open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
          <h3 class="font-bold text-didasa-black">{{ modal.editando ? 'Editar Taller' : 'Nuevo Taller' }}</h3>
          <button @click="cerrarModal" class="text-gray-400 hover:text-gray-700 text-xl leading-none">&times;</button>
        </div>
        <form @submit.prevent="guardar" class="px-6 py-5 space-y-4">
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Nombre</label>
            <input
              v-model="form.name"
              type="text"
              placeholder="Nombre del taller"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-didasa-red"
              required
            />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Ciudad</label>
            <input
              v-model="form.city"
              type="text"
              placeholder="Ciudad"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-didasa-red"
              required
            />
          </div>
          <div v-if="modal.editando" class="flex items-center gap-2">
            <input id="active" v-model="form.active" type="checkbox" class="accent-didasa-red" />
            <label for="active" class="text-sm text-gray-600">Activo</label>
          </div>
          <div class="flex justify-end gap-3 pt-2">
            <button type="button" @click="cerrarModal" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancelar</button>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-5 py-2 bg-didasa-red hover:bg-didasa-redhov text-white text-sm font-semibold rounded-lg transition-colors disabled:opacity-50"
            >
              {{ modal.editando ? 'Guardar cambios' : 'Crear' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Confirmar eliminación -->
    <div v-if="confirmDialog.open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4">
        <div class="px-6 pt-6 pb-4 flex flex-col items-center text-center gap-3">
          <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
            <svg class="w-6 h-6 text-didasa-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
          </div>
          <h3 class="font-bold text-didasa-black">Confirmar eliminación</h3>
          <p class="text-gray-500 text-sm">{{ confirmDialog.message }}</p>
        </div>
        <div class="px-6 pb-6 flex justify-center gap-3">
          <button @click="confirmDialog.open = false" class="px-5 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">Cancelar</button>
          <button @click="aceptarConfirmacion" class="px-5 py-2 bg-didasa-red hover:bg-didasa-redhov text-white text-sm font-semibold rounded-lg transition-colors">Eliminar</button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useForm, router, usePage } from '@inertiajs/vue3'
const page = usePage()
function cerrarSesion() { router.post('/logout') }

const props = defineProps({ talleres: Array })

const sidebarOpen = ref(window.innerWidth >= 768)

const menuItems = [
  { label: 'Talleres',   icon: '🔧', href: '/talleres',  active: true  },
  { label: 'Empleados',  icon: '👷', href: '/employees', active: false },
  { label: 'Staff',      icon: '🧰', href: '/staff',     active: false },
  { label: 'Clientes',   icon: '🧑‍💼', href: '/clients',  active: false },
  { label: 'Evaluación', icon: '⭐', href: '#',          active: false },
  { label: 'Reportes',   icon: '📊', href: '/reportes',  active: false },
  { label: 'Usuarios',   icon: '👤', href: '/users',     active: false },
]

const modal         = reactive({ open: false, editando: null })
const confirmDialog = reactive({ open: false, message: '', onConfirm: null })

const form = useForm({ name: '', city: '', active: true })

function pedirConfirmacion(message, fn) {
  confirmDialog.message   = message
  confirmDialog.onConfirm = fn
  confirmDialog.open      = true
}

function aceptarConfirmacion() {
  confirmDialog.onConfirm?.()
  confirmDialog.open = false
}

function abrirModal(taller = null) {
  modal.editando = taller
  form.name   = taller?.name   ?? ''
  form.city   = taller?.city   ?? ''
  form.active = taller?.active ?? true
  modal.open = true
}

function cerrarModal() {
  modal.open = false
  form.reset()
}

function guardar() {
  if (modal.editando) {
    form.put(`/talleres/${modal.editando.id}`, { onSuccess: cerrarModal })
  } else {
    form.post('/talleres', { onSuccess: cerrarModal })
  }
}

function eliminar(taller) {
  pedirConfirmacion(`¿Eliminar el taller "${taller.name}"?`, () => router.delete(`/talleres/${taller.id}`))
}
</script>
