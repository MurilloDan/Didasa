<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">

    <!-- Header -->
    <header class="bg-didasa-black border-b border-gray-800 px-6 py-4 flex items-center justify-between sticky top-0 z-10 shadow">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow">
          <img src="/images/logo.png" alt="DIDASA" class="h-8 w-auto" />
        </div>
        <div>
          <h1 class="font-black text-white tracking-wide">TECNICENTRO DIDASA</h1>
          <p class="text-xs text-gray-400">Mantenimiento · Reparación · Auto partes</p>
        </div>
      </div>
      <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-400 hover:text-white">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
    </header>

    <div class="flex flex-1">
      <!-- Sidebar -->
      <aside :class="['bg-white border-r border-gray-200 flex-shrink-0 transition-all duration-200 overflow-hidden', sidebarOpen ? 'w-56' : 'w-0 md:w-56']">
        <nav class="w-56 py-4">
          <div class="px-4 pb-3 mb-2 border-b border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Menú</p>
          </div>
          <ul class="space-y-0.5 px-2">
            <li v-for="item in menuItems" :key="item.label">
              <a :href="item.href" :class="['flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors', item.active ? 'bg-didasa-red text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-didasa-red']">
                <span class="text-lg leading-none">{{ item.icon }}</span>{{ item.label }}
              </a>
            </li>
          </ul>
          <div class="mt-6 px-4 border-t border-gray-100 pt-4">
            <a href="/" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-didasa-red transition-colors">
              <span class="text-lg">🖥️</span>Ir al Kiosko
            </a>
          </div>
        </nav>
      </aside>

      <!-- Main -->
      <main class="flex-1 min-w-0 px-4 md:px-8 py-8">

        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
          <div>
            <h2 class="text-2xl font-black text-didasa-black">Clients</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ clients.length }} client(s) registered</p>
          </div>
          <button @click="abrirModal()" class="flex items-center gap-2 bg-didasa-red hover:bg-didasa-redhov text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
            <span class="text-lg leading-none">+</span> New client
          </button>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                  <th class="px-6 py-3 text-left">ID</th>
                  <th class="px-6 py-3 text-left">Name</th>
                  <th class="px-6 py-3 text-left">Email</th>
                  <th class="px-6 py-3 text-left">Phone</th>
                  <th class="px-6 py-3 text-left">Registered</th>
                  <th class="px-6 py-3 text-center">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
                <tr v-if="clients.length === 0">
                  <td colspan="6" class="py-16 text-center text-gray-400">No clients registered yet.</td>
                </tr>
                <tr v-for="client in clients" :key="client.id" class="hover:bg-gray-50 transition-colors">
                  <td class="px-6 py-4 font-mono text-gray-400 text-xs">{{ client.id }}</td>
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                      <div class="w-9 h-9 rounded-full bg-didasa-red flex items-center justify-center text-white text-xs font-black flex-shrink-0">
                        {{ initials(client.name) }}
                      </div>
                      <span class="font-medium text-gray-800">{{ client.name }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 text-gray-500">{{ client.email ?? '—' }}</td>
                  <td class="px-6 py-4 text-gray-500">{{ client.phone ?? '—' }}</td>
                  <td class="px-6 py-4 text-gray-400 text-xs">{{ formatFecha(client.created_at) }}</td>
                  <td class="px-6 py-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                      <button @click="abrirModal(client)" class="text-xs text-didasa-red hover:underline font-medium">Edit</button>
                      <span class="text-gray-300">|</span>
                      <button @click="eliminar(client)" class="text-xs text-gray-400 hover:text-red-600 hover:underline font-medium">Delete</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>

    <!-- Modal -->
    <div v-if="modal.open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
          <h3 class="font-bold text-didasa-black">{{ modal.editando ? 'Edit Client' : 'New Client' }}</h3>
          <button @click="cerrarModal" class="text-gray-400 hover:text-gray-700 text-xl">&times;</button>
        </div>
        <form @submit.prevent="guardar" class="px-6 py-5 space-y-4">
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Full Name</label>
            <input v-model="form.name" type="text" placeholder="e.g. Carlos Mendoza" required
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-didasa-red" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Email <span class="text-gray-400 font-normal">(optional)</span></label>
            <input v-model="form.email" type="email" placeholder="email@example.com"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-didasa-red" />
            <p v-if="form.errors.email" class="text-xs text-red-500 mt-1">{{ form.errors.email }}</p>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Phone <span class="text-gray-400 font-normal">(optional)</span></label>
            <input v-model="form.phone" type="tel" placeholder="+505 8888-0000"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-didasa-red" />
          </div>
          <div class="flex justify-end gap-3 pt-2">
            <button type="button" @click="cerrarModal" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
            <button type="submit" :disabled="form.processing"
              class="px-5 py-2 bg-didasa-red hover:bg-didasa-redhov text-white text-sm font-semibold rounded-lg transition-colors disabled:opacity-50">
              {{ modal.editando ? 'Save changes' : 'Create' }}
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useForm, router } from '@inertiajs/vue3'

const props = defineProps({ clients: Array })

const sidebarOpen = ref(true)

const menuItems = [
  { label: 'Talleres',   icon: '🔧', href: '/talleres',  active: false },
  { label: 'Empleados',  icon: '👷', href: '/employees', active: false },
  { label: 'Staff',      icon: '🧰', href: '/staff',     active: false },
  { label: 'Clientes',   icon: '🧑‍💼', href: '/clients',  active: true  },
  { label: 'Evaluación', icon: '⭐', href: '#',          active: false },
  { label: 'Reportes',   icon: '📊', href: '/reportes',  active: false },
  { label: 'Usuarios',   icon: '👤', href: '#',          active: false },
]

const modal = reactive({ open: false, editando: null })
const form  = useForm({ name: '', email: '', phone: '' })

function abrirModal(client = null) {
  modal.editando = client
  form.name  = client?.name  ?? ''
  form.email = client?.email ?? ''
  form.phone = client?.phone ?? ''
  modal.open = true
}

function cerrarModal() { modal.open = false; form.reset() }

function guardar() {
  if (modal.editando) {
    form.put(`/clients/${modal.editando.id}`, { onSuccess: cerrarModal })
  } else {
    form.post('/clients', { onSuccess: cerrarModal })
  }
}

function eliminar(client) {
  if (confirm(`Delete "${client.name}"?`)) router.delete(`/clients/${client.id}`)
}

function initials(name) {
  return name.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase()
}

function formatFecha(iso) {
  if (!iso) return '—'
  return new Date(iso).toLocaleDateString('es-NI', { day: '2-digit', month: 'short', year: 'numeric' })
}
</script>
