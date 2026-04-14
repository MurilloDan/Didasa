<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">

    <!-- Header -->
    <header class="bg-didasa-black border-b border-gray-800 px-6 py-4 flex items-center justify-between sticky top-0 z-10 shadow">
      <div class="flex items-center gap-3">
        <img src="/images/logo.png" alt="Tecnicentro DIDASA" class="h-9 w-auto" />
        <div>
          <h1 class="font-black text-white tracking-wide">TECNICENTRO DIDASA</h1>
          <p class="text-xs text-gray-400">Mantenimiento · Reparación · Auto partes</p>
        </div>
      </div>
      <div class="flex items-center gap-4">
        <!-- Botón menú móvil -->
        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-400 hover:text-white">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
        <a href="/" class="text-sm text-didasa-gold hover:underline hidden md:flex items-center gap-1">
          <span>← Kiosko</span>
        </a>
      </div>
    </header>

    <div class="flex flex-1">

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

          <div class="mt-6 px-4 border-t border-gray-100 pt-4">
            <a href="/" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-didasa-red transition-colors">
              <span class="text-lg">🖥️</span>
              Ir al Kiosko
            </a>
          </div>
        </nav>
      </aside>

      <!-- Contenido principal -->
      <main class="flex-1 min-w-0 px-4 md:px-8 py-8 max-w-6xl">

      <!-- Período activo -->
      <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
        <div>
          <h2 class="text-2xl font-black text-didasa-black">Dashboard</h2>
          <p class="text-sm text-gray-500 mt-0.5">
            Fortnight {{ periodo.fortnight === 1 ? '1 (1–15)' : '2 (16–end)' }} —
            {{ formatFecha(periodo.inicio) }} al {{ formatFecha(periodo.fin) }}
          </p>
        </div>
        <!-- Chip período -->
        <div class="flex items-center gap-2 bg-red-50 border border-red-100 rounded-xl px-4 py-2 text-sm text-didasa-red font-semibold">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          Auto-reporte cada 15 días
        </div>
      </div>

      <!-- Tarjetas globales -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border-l-4 border-didasa-red p-5">
          <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Total Evaluaciones</p>
          <p class="text-4xl font-black text-didasa-black">{{ global?.total ?? 0 }}</p>
        </div>
        <div class="bg-green-50 rounded-2xl shadow-sm border border-green-100 p-5">
          <p class="text-xs text-green-600 font-medium uppercase tracking-wide mb-1">😊 Excelente</p>
          <p class="text-4xl font-bold text-green-700">{{ global?.good ?? 0 }}</p>
          <p class="text-sm text-green-500 mt-1">{{ pct(global?.good, global?.total) }}%</p>
        </div>
        <div class="bg-yellow-50 rounded-2xl shadow-sm border border-yellow-100 p-5">
          <p class="text-xs text-yellow-600 font-medium uppercase tracking-wide mb-1">😐 Regular</p>
          <p class="text-4xl font-bold text-yellow-700">{{ global?.fair ?? 0 }}</p>
          <p class="text-sm text-yellow-500 mt-1">{{ pct(global?.fair, global?.total) }}%</p>
        </div>
        <div class="bg-red-50 rounded-2xl shadow-sm border border-red-100 p-5">
          <p class="text-xs text-red-500 font-medium uppercase tracking-wide mb-1">😞 Debe mejorar</p>
          <p class="text-4xl font-bold text-red-600">{{ global?.poor ?? 0 }}</p>
          <p class="text-sm text-red-400 mt-1">{{ pct(global?.poor, global?.total) }}%</p>
        </div>
      </div>

      <!-- Barra de satisfacción global -->
      <div v-if="(global?.total ?? 0) > 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
        <div class="flex items-center justify-between mb-3">
          <h3 class="font-semibold text-gray-700">Índice de satisfacción global</h3>
          <span :class="['text-2xl font-black', indicadorColor(satisfaccionGlobal)]">
            {{ satisfaccionGlobal }}%
          </span>
        </div>
        <div class="h-4 bg-gray-100 rounded-full overflow-hidden flex">
          <div class="bg-green-400 transition-all duration-700"  :style="{ width: pct(global?.good,  global?.total) + '%' }" />
          <div class="bg-yellow-400 transition-all duration-700" :style="{ width: pct(global?.fair, global?.total) + '%' }" />
          <div class="bg-red-400 transition-all duration-700"    :style="{ width: pct(global?.poor, global?.total) + '%' }" />
        </div>
        <div class="flex gap-6 mt-2 text-xs text-gray-400">
          <span class="flex items-center gap-1"><span class="w-2 h-2 bg-green-400 rounded-full"></span>Excelente</span>
          <span class="flex items-center gap-1"><span class="w-2 h-2 bg-yellow-400 rounded-full"></span>Regular</span>
          <span class="flex items-center gap-1"><span class="w-2 h-2 bg-red-400 rounded-full"></span>Debe mejorar</span>
        </div>
      </div>

      <!-- Tabla por empleado -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
          <h3 class="font-semibold text-gray-700">Desempeño por empleado</h3>
          <span class="text-xs text-gray-400">Ordenado por satisfacción</span>
        </div>

        <div v-if="empleados.length === 0" class="py-16 text-center text-gray-400">
          No hay evaluaciones en este período.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
              <tr>
                <th class="px-6 py-3 text-left">#</th>
                <th class="px-6 py-3 text-left">Empleado</th>
                <th class="px-6 py-3 text-left">Área</th>
                <th class="px-4 py-3 text-center">Total</th>
                <th class="px-4 py-3 text-center">😊</th>
                <th class="px-4 py-3 text-center">😐</th>
                <th class="px-4 py-3 text-center">😞</th>
                <th class="px-6 py-3 text-center">Satisfacción</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="(emp, i) in empleados" :key="emp.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 font-bold text-gray-300">{{ i + 1 }}</td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-didasa-red flex items-center justify-center text-white text-xs font-black flex-shrink-0">
                      {{ emp.full_name.split(' ').map(n => n[0]).slice(0, 2).join('') }}
                    </div>
                    <div>
                      <p class="font-medium text-gray-800">{{ emp.full_name }}</p>
                      <p class="text-xs text-gray-400">{{ emp.position }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 text-gray-500">{{ emp.department }}</td>
                <td class="px-4 py-4 text-center font-semibold text-gray-700">{{ emp.total }}</td>
                <td class="px-4 py-4 text-center">
                  <div class="flex flex-col items-center">
                    <span class="font-semibold text-green-600">{{ emp.good }}</span>
                    <span class="text-xs text-gray-400">{{ emp.pct_good }}%</span>
                  </div>
                </td>
                <td class="px-4 py-4 text-center">
                  <div class="flex flex-col items-center">
                    <span class="font-semibold text-yellow-600">{{ emp.fair }}</span>
                    <span class="text-xs text-gray-400">{{ emp.pct_fair }}%</span>
                  </div>
                </td>
                <td class="px-4 py-4 text-center">
                  <div class="flex flex-col items-center">
                    <span class="font-semibold text-red-500">{{ emp.poor }}</span>
                    <span class="text-xs text-gray-400">{{ emp.pct_poor }}%</span>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="flex flex-col items-center gap-1">
                    <span :class="['text-lg font-bold', indicadorColor(emp.satisfaction_index)]">
                      {{ emp.satisfaction_index }}%
                    </span>
                    <div class="w-24 h-2 bg-gray-100 rounded-full overflow-hidden">
                      <div
                        :class="['h-full rounded-full transition-all', barraColor(emp.satisfaction_index)]"
                        :style="{ width: emp.satisfaction_index + '%' }"
                      />
                    </div>
                    <span :class="['text-xs font-medium px-2 py-0.5 rounded-full', chipColor(emp.satisfaction_index)]">
                      {{ nivelTexto(emp.satisfaction_index) }}
                    </span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Footer -->
      <p class="text-center text-xs text-gray-300">
        Tecnicentro DIDASA · Centro de servicio automotriz integral · Est. 2020
      </p>
    </main>

    </div><!-- /flex -->
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const sidebarOpen = ref(true)

const menuItems = [
  { label: 'Talleres',   icon: '🔧', href: '/talleres',  active: false },
  { label: 'Empleados',  icon: '👷', href: '/employees', active: false },
  { label: 'Staff',      icon: '🧰', href: '/staff',     active: false },
  { label: 'Clientes',   icon: '🧑‍💼', href: '/clients',  active: false },
  { label: 'Evaluación', icon: '⭐', href: '#',          active: false },
  { label: 'Reportes',   icon: '📊', href: '/reportes',  active: true  },
  { label: 'Usuarios',   icon: '👤', href: '#',          active: false },
]

const props = defineProps({
  global:    Object,
  empleados: Array,
  evolucion: Array,
  periodo:   Object,
})

const satisfaccionGlobal = computed(() => {
  const t = props.global?.total ?? 0
  if (!t) return 0
  return Math.round(((props.global.good * 100) + (props.global.fair * 50)) / t)
})

function pct(parte, total) {
  if (!total || !parte) return 0
  return Math.round((parte / total) * 100)
}

function formatFecha(fecha) {
  if (!fecha) return ''
  const [y, m, d] = fecha.split('-')
  const meses = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic']
  return `${parseInt(d)} ${meses[parseInt(m)-1]} ${y}`
}

function indicadorColor(val) {
  if (val >= 75) return 'text-green-600'
  if (val >= 40) return 'text-yellow-600'
  return 'text-red-500'
}

function barraColor(val) {
  if (val >= 75) return 'bg-green-400'
  if (val >= 40) return 'bg-yellow-400'
  return 'bg-red-400'
}

function chipColor(val) {
  if (val >= 75) return 'bg-green-100 text-green-700'
  if (val >= 40) return 'bg-yellow-100 text-yellow-700'
  return 'bg-red-100 text-red-600'
}

function nivelTexto(val) {
  if (val >= 75) return '😊 Óptimo'
  if (val >= 40) return '😐 Regular'
  return '😞 Crítico'
}
</script>
