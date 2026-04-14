<template>
  <div class="h-screen bg-gray-50 flex flex-col overflow-hidden">

    <!-- Header -->
    <header class="bg-didasa-black border-b border-gray-800 px-3 md:px-6 py-3 md:py-4 flex items-center justify-between sticky top-0 z-10 shadow">
      <div class="flex items-center gap-3">
        <div class="bg-white rounded-lg p-1 flex items-center justify-center shadow-sm"><img src="/images/logo.png" alt="Tecnicentro DIDASA" class="h-8 md:h-9 w-auto" /></div>
        <div>
          <h1 class="font-black text-sm md:text-base text-white tracking-wide">TECNICENTRO DIDASA</h1>
          <p class="hidden sm:block text-xs text-gray-400">Mantenimiento · Reparación · Auto partes</p>
        </div>
      </div>
      <div class="flex items-center gap-3">
        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-400 hover:text-white">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
        <span class="hidden md:block text-sm text-gray-400">{{ page.props.auth.user.name }}</span>
        <button @click="cerrarSesion" class="hidden md:flex items-center gap-1.5 text-xs text-gray-400 hover:text-white border border-gray-700 hover:border-gray-500 px-3 py-1.5 rounded-lg transition-colors">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
          Salir
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
            <!-- Usuario activo -->
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
      <main class="flex-1 min-w-0 w-full px-4 md:px-8 py-8 overflow-y-auto">

      <!-- Período activo -->
      <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
          <h2 class="text-2xl font-black text-didasa-black">Dashboard</h2>
          <p class="text-sm text-gray-500 mt-0.5">
            Quincena {{ periodo.fortnight === 1 ? '1 (1–15)' : '2 (16–fin)' }} —
            {{ formatFecha(periodo.inicio) }} al {{ formatFecha(periodo.fin) }}
          </p>
        </div>
        <!-- Chip período -->
        <div class="flex items-center gap-2 bg-red-50 border border-red-100 rounded-xl px-4 py-2 text-sm text-didasa-red font-semibold">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          Auto-reporte cada 15 días
        </div>
      </div>

      <!-- Filtro por taller -->
      <div class="flex flex-wrap gap-2 mb-8 items-start">
        <a
          href="/reportes"
          :class="['px-4 py-1.5 rounded-full text-sm font-semibold transition-all border', !taller_id ? 'bg-didasa-red text-white border-didasa-red shadow-md' : 'bg-white text-gray-600 border-gray-200 hover:bg-red-50']"
        >Todos los talleres</a>
        <a
          v-for="t in talleres" :key="t.id"
          :href="`/reportes?taller=${t.id}`"
          :class="['px-4 py-1.5 rounded-full text-sm font-semibold transition-all border', taller_id === t.id ? 'bg-didasa-red text-white border-didasa-red shadow-md' : 'bg-white text-gray-600 border-gray-200 hover:bg-red-50']"
        >{{ t.name }}</a>
        <!-- Botones de exportación -->
        <div class="w-full sm:w-auto sm:ml-auto flex gap-2 justify-end">
          <a :href="excelUrl" class="flex items-center gap-1.5 px-3 md:px-4 py-1.5 rounded-full text-xs md:text-sm font-semibold bg-green-600 hover:bg-green-700 text-white transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Excel
          </a>
          <a :href="pdfUrl" class="flex items-center gap-1.5 px-3 md:px-4 py-1.5 rounded-full text-xs md:text-sm font-semibold bg-didasa-red hover:bg-didasa-redhov text-white transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            PDF
          </a>
        </div>
      </div>

      <!-- Tarjetas por taller (solo vista "Todos los talleres") -->
      <div v-if="!taller_id && talleres_stats.length > 0" class="mb-8">
        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-3">Resumen por taller</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <a
            v-for="t in talleres_stats" :key="t.id"
            :href="`/reportes?taller=${t.id}`"
            class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-didasa-red/30 transition-all group"
          >
            <div class="flex items-start justify-between mb-3">
              <div>
                <p class="font-bold text-didasa-black group-hover:text-didasa-red transition-colors">{{ t.name }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ t.total }} evaluaciones</p>
              </div>
              <span :class="['text-xl font-black', indicadorColor(t.satisfaction_index)]">{{ t.satisfaction_index }}%</span>
            </div>
            <div class="h-2 bg-gray-100 rounded-full overflow-hidden flex mb-3">
              <div class="bg-green-400"  :style="{ width: pct(t.good, t.total) + '%' }" />
              <div class="bg-yellow-400" :style="{ width: pct(t.fair, t.total) + '%' }" />
              <div class="bg-red-400"    :style="{ width: pct(t.poor, t.total) + '%' }" />
            </div>
            <div class="flex justify-between text-xs text-gray-500">
              <span class="text-green-600 font-medium">😊 {{ t.good }} ({{ pct(t.good, t.total) }}%)</span>
              <span class="text-yellow-600 font-medium">😐 {{ t.fair }} ({{ pct(t.fair, t.total) }}%)</span>
              <span class="text-red-500 font-medium">😞 {{ t.poor }} ({{ pct(t.poor, t.total) }}%)</span>
            </div>
          </a>
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

        <div v-else>
          <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                  <th class="px-6 py-3 text-left">#</th>
                  <th class="px-6 py-3 text-left">Empleado</th>
                  <th v-if="!taller_id" class="px-4 py-3 text-left">Taller</th>
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
                  <td v-if="!taller_id" class="px-4 py-4">
                    <a :href="`/reportes?taller=${talleres.find(t => t.name === emp.workshop)?.id ?? ''}`"
                       class="inline-block text-xs font-semibold bg-red-50 text-didasa-red border border-red-100 rounded-full px-2.5 py-1 hover:bg-red-100 transition-colors">
                      {{ emp.workshop }}
                    </a>
                  </td>
                  <td class="px-6 py-4 text-gray-500">{{ emp.department }}</td>
                  <td class="px-4 py-4 text-center font-semibold text-gray-700">{{ emp.total }}</td>
                  <td class="px-4 py-4 text-center"><div class="flex flex-col items-center"><span class="font-semibold text-green-600">{{ emp.good }}</span><span class="text-xs text-gray-400">{{ emp.pct_good }}%</span></div></td>
                  <td class="px-4 py-4 text-center"><div class="flex flex-col items-center"><span class="font-semibold text-yellow-600">{{ emp.fair }}</span><span class="text-xs text-gray-400">{{ emp.pct_fair }}%</span></div></td>
                  <td class="px-4 py-4 text-center"><div class="flex flex-col items-center"><span class="font-semibold text-red-500">{{ emp.poor }}</span><span class="text-xs text-gray-400">{{ emp.pct_poor }}%</span></div></td>
                  <td class="px-6 py-4">
                    <div class="flex flex-col items-center gap-1">
                      <span :class="['text-lg font-bold', indicadorColor(emp.satisfaction_index)]">{{ emp.satisfaction_index }}%</span>
                      <div class="w-24 h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div :class="['h-full rounded-full transition-all', barraColor(emp.satisfaction_index)]" :style="{ width: emp.satisfaction_index + '%' }" />
                      </div>
                      <span :class="['text-xs font-medium px-2 py-0.5 rounded-full', chipColor(emp.satisfaction_index)]">{{ nivelTexto(emp.satisfaction_index) }}</span>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="md:hidden divide-y divide-gray-100">
            <div v-for="(emp, i) in empleados" :key="emp.id" class="px-4 py-4">
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <p class="text-xs text-gray-400">#{{ i + 1 }}</p>
                  <p class="font-semibold text-gray-800 truncate">{{ emp.full_name }}</p>
                  <p class="text-xs text-gray-400 truncate">{{ emp.position }} · {{ emp.department }}</p>
                  <p v-if="!taller_id" class="text-xs text-didasa-red mt-0.5">{{ emp.workshop }}</p>
                </div>
                <span :class="['text-sm font-bold', indicadorColor(emp.satisfaction_index)]">{{ emp.satisfaction_index }}%</span>
              </div>
              <div class="grid grid-cols-4 gap-2 text-center mt-3">
                <div class="bg-gray-50 rounded-lg py-2"><p class="text-[11px] text-gray-400">Total</p><p class="text-sm font-semibold text-gray-700">{{ emp.total }}</p></div>
                <div class="bg-green-50 rounded-lg py-2"><p class="text-[11px] text-green-500">😊</p><p class="text-sm font-semibold text-green-600">{{ emp.good }}</p></div>
                <div class="bg-yellow-50 rounded-lg py-2"><p class="text-[11px] text-yellow-600">😐</p><p class="text-sm font-semibold text-yellow-600">{{ emp.fair }}</p></div>
                <div class="bg-red-50 rounded-lg py-2"><p class="text-[11px] text-red-500">😞</p><p class="text-sm font-semibold text-red-500">{{ emp.poor }}</p></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Comentarios de Mejora -->
      <div v-if="comentarios.length || motivos_freq.length" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <button
          @click="comentariosAbierto = !comentariosAbierto"
          class="w-full px-6 py-4 border-b border-gray-100 flex items-center justify-between hover:bg-gray-50 transition-colors"
        >
          <div class="flex items-center gap-2">
            <span class="text-lg">💬</span>
            <h3 class="font-semibold text-gray-700">Aspectos a Mejorar (Comentarios)</h3>
            <span class="text-xs bg-red-50 text-didasa-red border border-red-100 rounded-full px-2 py-0.5">{{ comentarios.length }} comentarios</span>
          </div>
          <svg :class="['w-5 h-5 text-gray-400 transition-transform', comentariosAbierto ? 'rotate-180' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>

        <div v-if="comentariosAbierto" class="p-6 space-y-6">

          <!-- Frecuencia de motivos -->
          <div v-if="motivos_freq.length">
            <h4 class="text-sm font-bold text-gray-600 uppercase tracking-wider mb-4">Aspectos más mencionados</h4>
            <div class="space-y-2">
              <div v-for="m in motivos_freq" :key="m.label" class="flex items-center gap-2 sm:gap-3">
                <span class="w-28 sm:w-40 text-xs font-medium text-gray-600 text-left sm:text-right flex-shrink-0">{{ m.label }}</span>
                <div class="flex-1 bg-gray-100 rounded-full h-5 overflow-hidden">
                  <div
                    class="h-full bg-didasa-red rounded-full flex items-center justify-end pr-2 transition-all duration-500"
                    :style="{ width: Math.round(m.count / motivos_freq[0].count * 100) + '%' }"
                  >
                    <span v-if="m.count / motivos_freq[0].count > 0.25" class="text-white text-xs font-bold">{{ m.count }}</span>
                  </div>
                </div>
                <span class="w-6 text-xs font-bold text-gray-500 flex-shrink-0">{{ m.count }}</span>
              </div>
            </div>
          </div>

          <!-- Tabla de comentarios recientes -->
          <div v-if="comentarios.length">
            <h4 class="text-sm font-bold text-gray-600 uppercase tracking-wider mb-3">Comentarios recientes</h4>
            <div class="hidden md:block overflow-x-auto">
              <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                  <tr>
                    <th class="px-4 py-3 text-left">Empleado</th>
                    <th class="px-4 py-3 text-left">Aspectos reportados</th>
                    <th class="px-4 py-3 text-right">Fecha</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                  <tr v-for="(c, i) in comentarios" :key="i" class="hover:bg-red-50 transition-colors">
                    <td class="px-4 py-3">
                      <p class="font-medium text-gray-800">{{ c.empleado }}</p>
                      <p class="text-xs text-gray-400">{{ c.cargo }}</p>
                    </td>
                    <td class="px-4 py-3">
                      <div class="flex flex-wrap gap-1">
                        <span
                          v-for="tag in c.comment.split(', ')" :key="tag"
                          class="inline-block text-xs font-medium bg-red-50 text-didasa-red border border-red-100 rounded-full px-2.5 py-0.5"
                        >{{ tag }}</span>
                      </div>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-400 text-right whitespace-nowrap">{{ c.fecha }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="md:hidden space-y-3">
              <div v-for="(c, i) in comentarios" :key="`m-${i}`" class="border border-red-100 rounded-xl p-3 bg-red-50/30">
                <div class="flex items-start justify-between gap-2">
                  <div>
                    <p class="font-semibold text-gray-800 text-sm">{{ c.empleado }}</p>
                    <p class="text-xs text-gray-400">{{ c.cargo }}</p>
                  </div>
                  <p class="text-[11px] text-gray-400 whitespace-nowrap">{{ c.fecha }}</p>
                </div>
                <div class="flex flex-wrap gap-1 mt-2">
                  <span v-for="tag in c.comment.split(', ')" :key="`tag-${i}-${tag}`" class="inline-block text-[11px] font-medium bg-red-50 text-didasa-red border border-red-100 rounded-full px-2 py-0.5">{{ tag }}</span>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Historial de Cortes Quincenales -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <button
          @click="historialAbierto = !historialAbierto"
          class="w-full px-6 py-4 border-b border-gray-100 flex items-center justify-between hover:bg-gray-50 transition-colors"
        >
          <div class="flex items-center gap-2">
            <span class="text-lg">🗂️</span>
            <h3 class="font-semibold text-gray-700">Historial de Cortes Quincenales</h3>
            <span class="text-xs bg-gray-100 text-gray-500 rounded-full px-2 py-0.5">{{ historico.length }} períodos guardados</span>
          </div>
          <svg :class="['w-5 h-5 text-gray-400 transition-transform', historialAbierto ? 'rotate-180' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>

        <div v-if="historialAbierto">
          <div v-if="historico.length === 0" class="py-12 text-center text-gray-400 text-sm">
            Aún no hay cortes quincenales guardados.
          </div>

          <div v-else>
            <div class="hidden md:block overflow-x-auto">
              <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                  <tr>
                    <th class="px-6 py-3 text-left">Período</th>
                    <th class="px-6 py-3 text-left">Fechas</th>
                    <th class="px-4 py-3 text-center">Total</th>
                    <th class="px-4 py-3 text-center">😊 Exc.</th>
                    <th class="px-4 py-3 text-center">😐 Reg.</th>
                    <th class="px-4 py-3 text-center">😞 Mej.</th>
                    <th class="px-4 py-3 text-center">Satisfacción</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                  <tr v-for="corte in historico" :key="`${corte.year}-${corte.month}-${corte.fortnight}`" class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3">
                      <div class="font-semibold text-didasa-black">Quincena {{ corte.fortnight }} — {{ nombreMes(corte.month) }} {{ corte.year }}</div>
                      <div class="text-xs text-gray-400">Q{{ corte.fortnight === 1 ? '1 (1–15)' : '2 (16–fin)' }}</div>
                    </td>
                    <td class="px-6 py-3 text-gray-500 text-xs">{{ formatFecha(corte.start_date) }} — {{ formatFecha(corte.end_date) }}</td>
                    <td class="px-4 py-3 text-center font-bold text-gray-700">{{ corte.total }}</td>
                    <td class="px-4 py-3 text-center text-green-600 font-semibold">{{ corte.good }}</td>
                    <td class="px-4 py-3 text-center text-yellow-600 font-semibold">{{ corte.fair }}</td>
                    <td class="px-4 py-3 text-center text-red-500 font-semibold">{{ corte.poor }}</td>
                    <td class="px-4 py-3 text-center"><span :class="['text-sm font-bold', indicadorColor(corte.satisfaction_index)]">{{ corte.satisfaction_index }}%</span></td>
                    <td class="px-4 py-3 text-center"><a :href="urlCorte(corte)" class="inline-flex items-center gap-1 text-xs font-semibold text-didasa-red hover:underline">Ver detalle →</a></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="md:hidden divide-y divide-gray-100">
              <div v-for="corte in historico" :key="`h-${corte.year}-${corte.month}-${corte.fortnight}`" class="px-4 py-4">
                <div class="flex items-start justify-between gap-2">
                  <div>
                    <p class="font-semibold text-didasa-black text-sm">Quincena {{ corte.fortnight }} — {{ nombreMes(corte.month) }} {{ corte.year }}</p>
                    <p class="text-xs text-gray-400">{{ formatFecha(corte.start_date) }} — {{ formatFecha(corte.end_date) }}</p>
                  </div>
                  <span :class="['text-sm font-bold', indicadorColor(corte.satisfaction_index)]">{{ corte.satisfaction_index }}%</span>
                </div>
                <div class="grid grid-cols-4 gap-2 text-center mt-3">
                  <div class="bg-gray-50 rounded-lg py-2"><p class="text-[11px] text-gray-400">Total</p><p class="text-sm font-semibold text-gray-700">{{ corte.total }}</p></div>
                  <div class="bg-green-50 rounded-lg py-2"><p class="text-[11px] text-green-500">😊</p><p class="text-sm font-semibold text-green-600">{{ corte.good }}</p></div>
                  <div class="bg-yellow-50 rounded-lg py-2"><p class="text-[11px] text-yellow-600">😐</p><p class="text-sm font-semibold text-yellow-600">{{ corte.fair }}</p></div>
                  <div class="bg-red-50 rounded-lg py-2"><p class="text-[11px] text-red-500">😞</p><p class="text-sm font-semibold text-red-500">{{ corte.poor }}</p></div>
                </div>
                <a :href="urlCorte(corte)" class="inline-flex items-center gap-1 text-xs font-semibold text-didasa-red mt-3">Ver detalle →</a>
              </div>
            </div>
          </div>
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
import { router, usePage } from '@inertiajs/vue3'
const page = usePage()
function cerrarSesion() { router.post('/logout') }

const sidebarOpen = ref(window.innerWidth >= 768)

const menuItems = [
  { label: 'Talleres',   icon: '🔧', href: '/talleres',  active: false },
  { label: 'Empleados',  icon: '👷', href: '/employees', active: false },
  { label: 'Staff',      icon: '🧰', href: '/staff',     active: false },
  { label: 'Clientes',   icon: '🧑‍💼', href: '/clients',  active: false },
  { label: 'Evaluación', icon: '⭐', href: '#',          active: false },
  { label: 'Reportes',   icon: '📊', href: '/reportes',  active: true  },
  { label: 'Usuarios',   icon: '👤', href: '/users',     active: false },
]

const props = defineProps({
  global:         Object,
  empleados:      Array,
  evolucion:      Array,
  periodo:        Object,
  talleres:       Array,
  taller_id:      Number,
  historico:      { type: Array, default: () => [] },
  talleres_stats: { type: Array, default: () => [] },
  comentarios:    { type: Array, default: () => [] },
  motivos_freq:   { type: Array, default: () => [] },
})

const historialAbierto    = ref(true)
const comentariosAbierto  = ref(true)

const satisfaccionGlobal = computed(() => {
  const t = props.global?.total ?? 0
  if (!t) return 0
  return Math.round(((props.global.good * 100) + (props.global.fair * 50)) / t)
})

const excelUrl = computed(() => {
  const base = '/reportes/export/excel'
  return props.taller_id ? `${base}?taller=${props.taller_id}` : base
})

const pdfUrl = computed(() => {
  const base = '/reportes/export/pdf'
  return props.taller_id ? `${base}?taller=${props.taller_id}` : base
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

const MESES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']
function nombreMes(m) { return MESES[(m - 1)] ?? '' }

function urlCorte(corte) {
  const base = `/reportes?fecha_inicio=${corte.start_date}&fecha_fin=${corte.end_date}`
  return props.taller_id ? `${base}&taller=${props.taller_id}` : base
}
</script>
