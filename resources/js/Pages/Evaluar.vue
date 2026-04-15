<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">

    <!-- Header -->
    <header class="bg-didasa-black shadow py-3 px-6 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center shadow">
          <div class="bg-white rounded-lg p-1 flex items-center justify-center shadow-sm"><img src="/images/logo.png" alt="Tecnicentro DIDASA" class="h-9 w-auto" /></div>
        </div>
        <div>
          <h1 class="text-base font-black text-white tracking-wide">TECNICENTRO DIDASA</h1>
          <p class="text-xs text-gray-400">Mantenimiento · Reparación · Auto partes</p>
        </div>
      </div>
      <a href="/login" class="text-xs text-gray-400 hover:text-didasa-gold transition-colors">Admin</a>
    </header>

    <!-- Pantalla de agradecimiento -->
    <transition name="fade">
      <div v-if="step === 'gracias'" class="flex-1 flex flex-col items-center justify-center p-8 text-center">
        <div class="bg-white rounded-3xl shadow-xl p-12 max-w-md w-full border-t-4 border-didasa-red">
          <div class="text-8xl mb-6">🙏</div>
          <h2 class="text-3xl font-black text-didasa-black mb-3">¡Gracias!</h2>
          <p class="text-gray-500 text-lg mb-2">Tu opinión nos ayuda a mejorar.</p>
          <p class="text-xs text-gray-400 mb-6">Tecnicentro DIDASA — Tecnología avanzada, atención personalizada</p>
          <div class="flex items-center justify-center gap-2">
            <div
              v-for="n in 3" :key="n"
              :class="['w-2 h-2 rounded-full transition-all duration-300', n === countdown ? 'bg-didasa-red w-4' : 'bg-gray-200']"
            />
          </div>
          <p class="text-sm text-gray-400 mt-3">Volviendo en {{ countdown }}...</p>
        </div>
      </div>
    </transition>

    <!-- Paso 0: Seleccionar taller -->
    <transition name="slide">
      <div v-if="step === 'taller'" class="flex-1 flex flex-col p-6 md:p-10 max-w-5xl mx-auto w-full">
        <div class="text-center mb-8">
          <div class="inline-flex items-center gap-2 bg-red-50 text-didasa-red border border-red-100 rounded-full px-4 py-1 text-sm font-semibold mb-4">
            <span class="w-5 h-5 bg-didasa-red text-white rounded-full flex items-center justify-center text-xs font-bold">1</span>
            Paso 1 de 3
          </div>
          <h2 class="text-2xl md:text-3xl font-black text-didasa-black">¿En qué taller te atendieron?</h2>
          <p class="text-gray-500 mt-2">Selecciona la sucursal que visitaste hoy</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5 justify-items-center">
          <button
            v-for="t in talleres" :key="t.id"
            @click="seleccionarTaller(t)"
            class="bg-white rounded-2xl shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-200 p-7 flex flex-col items-center gap-3 border-2 border-transparent hover:border-didasa-red group w-full max-w-xs"
          >
            <div class="w-16 h-16 rounded-full bg-didasa-red flex items-center justify-center shadow-md">
              <span class="text-white text-2xl">🔧</span>
            </div>
            <div class="text-center">
              <p class="font-bold text-gray-800 group-hover:text-didasa-red text-lg leading-tight">{{ t.name }}</p>
              <p class="text-sm text-gray-400 mt-0.5">{{ t.city }}</p>
            </div>
          </button>
        </div>
      </div>
    </transition>

    <!-- Paso 1: Seleccionar empleado -->
    <transition name="slide">
      <div v-if="step === 'empleado'" class="flex-1 flex flex-col p-6 md:p-10 max-w-5xl mx-auto w-full">
        <div class="text-center mb-8">
          <div class="inline-flex items-center gap-2 bg-red-50 text-didasa-red border border-red-100 rounded-full px-4 py-1 text-sm font-semibold mb-4">
            <span class="w-5 h-5 bg-didasa-red text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
            Paso 2 de 3
          </div>
          <h2 class="text-2xl md:text-3xl font-black text-didasa-black">¿Quién te atendió hoy?</h2>
          <p class="text-gray-500 mt-2">Selecciona al técnico o asesor que te brindó atención</p>
        </div>

        <!-- Filtro por área -->
        <div class="flex flex-wrap gap-2 justify-center mb-6" v-if="areasDeTaller.length > 1">
          <button
            @click="areaSeleccionada = null"
            :class="['px-4 py-1.5 rounded-full text-sm font-semibold transition-all',
              areaSeleccionada === null ? 'bg-didasa-red text-white shadow-md' : 'bg-white text-gray-600 hover:bg-red-50 border border-gray-200']"
          >Todos</button>
          <button
            v-for="area in areasDeTaller" :key="area.id"
            @click="areaSeleccionada = area.id"
            :class="['px-4 py-1.5 rounded-full text-sm font-semibold transition-all',
              areaSeleccionada === area.id ? 'bg-didasa-red text-white shadow-md' : 'bg-white text-gray-600 hover:bg-red-50 border border-gray-200']"
          >{{ area.name }}</button>
        </div>

        <!-- Grid de empleados -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
          <button
            v-for="emp in empleadosFiltrados"
            :key="emp.id"
            @click="seleccionarEmpleado(emp)"
            class="bg-white rounded-2xl shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-200 p-5 flex flex-col items-center gap-3 border-2 border-transparent hover:border-didasa-red group"
          >
            <!-- Avatar -->
            <div class="w-20 h-20 rounded-full overflow-hidden bg-didasa-red flex items-center justify-center shadow-md">
              <img v-if="emp.photo" :src="emp.photo" :alt="emp.first_name" class="w-full h-full object-cover" />
              <span v-else class="text-white text-2xl font-black">
                {{ emp.first_name.charAt(0) }}{{ emp.last_name.charAt(0) }}
              </span>
            </div>
            <div class="text-center">
              <p class="font-semibold text-gray-800 group-hover:text-didasa-red leading-tight">{{ emp.first_name }} {{ emp.last_name }}</p>
              <p class="text-xs text-gray-400 mt-0.5">{{ emp.position }}</p>
            </div>
          </button>
        </div>
      </div>
    </transition>

    <!-- Paso 2: Calificación con caritas -->
    <transition name="slide">
      <div v-if="step === 'calificacion'" class="flex-1 flex flex-col items-center justify-center p-6">
        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-12 max-w-lg w-full text-center">

          <!-- Info del empleado seleccionado -->
          <div class="flex flex-col items-center mb-8">
            <div class="w-20 h-20 rounded-full overflow-hidden bg-didasa-red flex items-center justify-center shadow-md mb-3">
              <img v-if="empleado.photo" :src="empleado.photo" class="w-full h-full object-cover" />
              <span v-else class="text-white text-2xl font-black">
                {{ empleado.first_name.charAt(0) }}{{ empleado.last_name.charAt(0) }}
              </span>
            </div>
            <p class="font-bold text-gray-800 text-lg">{{ empleado.first_name }} {{ empleado.last_name }}</p>
            <p class="text-sm text-gray-400">{{ empleado.position }}</p>
          </div>

          <div class="inline-flex items-center gap-2 bg-red-50 text-didasa-red border border-red-100 rounded-full px-4 py-1 text-sm font-semibold mb-6">
            <span class="w-5 h-5 bg-didasa-red text-white rounded-full flex items-center justify-center text-xs font-bold">3</span>
            Paso 3 de 3
          </div>
          <h2 class="text-2xl font-black text-didasa-black mb-2">¿Cómo fue la atención?</h2>
          <p class="text-gray-500 mb-8">Toca la carita que mejor describe tu experiencia</p>

          <!-- Caritas -->
          <div class="grid grid-cols-3 gap-4 mb-8">
            <button
              @click="calificar('good')"
              :disabled="enviando"
              :class="['flex flex-col items-center gap-2 p-4 md:p-6 rounded-2xl border-2 transition-all duration-200',
                calificacionSeleccionada === 'good'
                  ? 'border-green-400 bg-green-50 shadow-lg scale-105'
                  : 'border-gray-200 hover:border-green-300 hover:bg-green-50 hover:scale-105']"
            >
              <span class="text-5xl md:text-6xl">😊</span>
              <span class="text-sm font-semibold text-green-700">Excelente</span>
            </button>

            <button
              @click="calificar('fair')"
              :disabled="enviando"
              :class="['flex flex-col items-center gap-2 p-4 md:p-6 rounded-2xl border-2 transition-all duration-200',
                calificacionSeleccionada === 'fair'
                  ? 'border-yellow-400 bg-yellow-50 shadow-lg scale-105'
                  : 'border-gray-200 hover:border-yellow-300 hover:bg-yellow-50 hover:scale-105']"
            >
              <span class="text-5xl md:text-6xl">😐</span>
              <span class="text-sm font-semibold text-yellow-700">Regular</span>
            </button>

            <button
              @click="calificar('poor')"
              :disabled="enviando"
              :class="['flex flex-col items-center gap-2 p-4 md:p-6 rounded-2xl border-2 transition-all duration-200',
                calificacionSeleccionada === 'poor'
                  ? 'border-red-400 bg-red-50 shadow-lg scale-105'
                  : 'border-gray-200 hover:border-red-300 hover:bg-red-50 hover:scale-105']"
            >
              <span class="text-5xl md:text-6xl">😞</span>
              <span class="text-sm font-semibold text-red-700">Debe mejorar</span>
            </button>
          </div>

          <!-- Spinner de envío -->
          <div v-if="enviando" class="flex items-center justify-center gap-2 text-didasa-red">
            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
            </svg>
            <span class="text-sm">Enviando...</span>
          </div>

          <div class="flex flex-col items-center gap-1 mt-4">
            <button
              @click="step = 'empleado'"
              class="text-sm text-gray-400 hover:text-gray-600 transition-colors"
            >← Cambiar empleado</button>
            <button
              @click="step = 'taller'"
              class="text-sm text-gray-400 hover:text-gray-600 transition-colors"
            >← Cambiar taller</button>
          </div>
        </div>
      </div>
    </transition>

    <!-- Paso extra: Motivo de "Debe mejorar" -->
    <transition name="slide">
      <div v-if="step === 'motivo'" class="flex-1 flex flex-col items-center justify-center p-6">
        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 max-w-lg w-full text-center">

          <!-- Info del empleado -->
          <div class="flex flex-col items-center mb-6">
            <div class="w-16 h-16 rounded-full overflow-hidden bg-didasa-red flex items-center justify-center shadow-md mb-2">
              <img v-if="empleado.photo" :src="empleado.photo" class="w-full h-full object-cover" />
              <span v-else class="text-white text-xl font-black">
                {{ empleado.first_name.charAt(0) }}{{ empleado.last_name.charAt(0) }}
              </span>
            </div>
            <p class="font-bold text-gray-800">{{ empleado.first_name }} {{ empleado.last_name }}</p>
            <p class="text-sm text-gray-400">{{ empleado.position }}</p>
          </div>

          <div class="text-5xl mb-4">😞</div>
          <h2 class="text-2xl font-black text-didasa-black mb-1">¿Qué debe mejorar?</h2>
          <p class="text-gray-500 text-sm mb-6">Selecciona los aspectos a mejorar (puedes elegir más de uno)</p>

          <!-- Opciones de motivo -->
          <div class="grid grid-cols-2 gap-3 mb-4">
            <button
              v-for="m in motivosDisponibles" :key="m.id"
              @click="toggleMotivo(m.id)"
              :class="['flex items-center gap-3 px-4 py-3 rounded-xl border-2 text-sm font-semibold transition-all duration-150 text-left',
                motivosSeleccionados.includes(m.id)
                  ? 'border-didasa-red bg-red-50 text-didasa-red shadow-sm scale-[1.02]'
                  : 'border-gray-200 text-gray-600 hover:border-red-300 hover:bg-red-50']">
              <span class="text-xl flex-shrink-0">{{ m.icon }}</span>
              <span class="leading-tight">{{ m.name }}</span>
              <span :class="['ml-auto w-4 h-4 rounded border-2 flex-shrink-0 transition-all',
                motivosSeleccionados.includes(m.id) ? 'bg-didasa-red border-didasa-red' : 'border-gray-300']"></span>
            </button>
          </div>

          <!-- Campo libre cuando seleccionan "Otro" -->
          <transition name="fade">
            <div v-if="otroSeleccionado" class="mb-5">
              <textarea
                v-model="comentarioOtro"
                rows="3"
                maxlength="300"
                placeholder="Cuéntanos qué debería mejorar..."
                class="w-full border-2 border-red-200 focus:border-didasa-red rounded-xl px-4 py-3 text-sm text-gray-700 resize-none outline-none transition-colors"
              />
              <p class="text-right text-xs text-gray-400 mt-1">{{ comentarioOtro.length }}/300</p>
            </div>
          </transition>

          <!-- Botón enviar -->
          <button
            @click="enviarConMotivo"
            :disabled="enviando"
            class="w-full bg-didasa-red hover:bg-didasa-redhov text-white font-bold py-3 px-6 rounded-xl transition-colors disabled:opacity-50 flex items-center justify-center gap-2">
            <svg v-if="enviando" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
            </svg>
            {{ motivosSeleccionados.length ? 'Enviar comentario' : 'Omitir y enviar' }}
          </button>

          <button
            @click="step = 'calificacion'; motivosSeleccionados = []; comentarioOtro = ''"
            class="mt-3 text-sm text-gray-400 hover:text-gray-600 transition-colors"
          >← Volver</button>
        </div>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  talleres: Array,
  aspectos_mejora: { type: Array, default: () => [] },
})

const step                     = ref('taller')
const tallerSeleccionado       = ref(null)
const areaSeleccionada         = ref(null)
const empleado                 = ref(null)
const calificacionSeleccionada = ref(null)
const enviando                 = ref(false)
const countdown                = ref(3)
const motivosSeleccionados     = ref([])
const comentarioOtro           = ref('')

const motivosDisponibles = computed(() => {
  if (props.aspectos_mejora.length) return props.aspectos_mejora

  return [
    { id: 1, name: 'Tiempo de atención', icon: '⏱️', is_other: false },
    { id: 2, name: 'Trato al cliente', icon: '🤝', is_other: false },
    { id: 3, name: 'Calidad del trabajo', icon: '🔧', is_other: false },
    { id: 4, name: 'Explicación del servicio', icon: '💬', is_other: false },
    { id: 5, name: 'Limpieza del área', icon: '🧹', is_other: false },
    { id: 6, name: 'Otro', icon: '✏️', is_other: true },
  ]
})

const otroId = computed(() => motivosDisponibles.value.find(m => m.is_other)?.id ?? null)
const otroSeleccionado = computed(() => otroId.value !== null && motivosSeleccionados.value.includes(otroId.value))

function toggleMotivo(id) {
  const idx = motivosSeleccionados.value.indexOf(id)
  if (idx === -1) motivosSeleccionados.value.push(id)
  else motivosSeleccionados.value.splice(idx, 1)
}

// Empleados del taller seleccionado
const empleadosDeTaller = computed(() =>
  tallerSeleccionado.value?.empleados ?? []
)

// Áreas únicas del taller seleccionado
const areasDeTaller = computed(() => {
  const mapa = new Map()
  empleadosDeTaller.value.forEach(e => {
    if (e.area) mapa.set(e.area.id, e.area)
  })
  return [...mapa.values()]
})

// Empleados filtrados por área
const empleadosFiltrados = computed(() =>
  areaSeleccionada.value === null
    ? empleadosDeTaller.value
    : empleadosDeTaller.value.filter(e => e.department_id === areaSeleccionada.value)
)

function seleccionarTaller(t) {
  tallerSeleccionado.value = t
  areaSeleccionada.value   = null
  step.value               = 'empleado'
}

function seleccionarEmpleado(emp) {
  empleado.value = emp
  calificacionSeleccionada.value = null
  step.value = 'calificacion'
}

function calificar(cal) {
  if (enviando.value) return
  calificacionSeleccionada.value = cal
  if (cal === 'poor') {
    motivosSeleccionados.value = []
    comentarioOtro.value = ''
    step.value = 'motivo'
    return
  }
  enviarEvaluacion(cal, null)
}

function enviarConMotivo() {
  enviarEvaluacion('poor', null)
}

function enviarEvaluacion(cal, comment) {
  if (enviando.value) return
  enviando.value = true

  router.post('/evaluar', {
    employee_id: empleado.value.id,
    rating:      cal,
    comment:     comment,
    aspect_ids:  cal === 'poor' ? motivosSeleccionados.value : [],
    other_comment: cal === 'poor' && otroSeleccionado.value
      ? (comentarioOtro.value.trim() || null)
      : null,
  }, {
    preserveState:  true,
    preserveScroll: true,
    onSuccess: () => {
      motivosSeleccionados.value = []
      comentarioOtro.value = ''
      step.value = 'gracias'
      iniciarContador()
    },
    onFinish: () => {
      enviando.value = false
    },
  })
}

function iniciarContador() {
  countdown.value = 3
  const t = setInterval(() => {
    countdown.value--
    if (countdown.value <= 0) {
      clearInterval(t)
      step.value                     = 'taller'
      tallerSeleccionado.value       = null
      empleado.value                 = null
      calificacionSeleccionada.value = null
    }
  }, 1000)
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.4s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.slide-enter-active, .slide-leave-active { transition: all 0.35s ease; }
.slide-enter-from { opacity: 0; transform: translateX(30px); }
.slide-leave-to  { opacity: 0; transform: translateX(-30px); }
</style>
