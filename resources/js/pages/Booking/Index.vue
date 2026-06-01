<template>
  <Head title="Afspraak maken" />

    <div class="min-h-screen bg-stone-950 px-4 py-12 text-stone-100">
      <div class="mx-auto max-w-4xl rounded-[1.75rem] border border-stone-700 bg-stone-900/95 p-6 shadow-[0_30px_100px_rgba(0,0,0,0.35)] md:p-8">

        <!-- Header -->
        <div class="text-center mb-10">
          <div class="text-xs tracking-widest text-amber-500 uppercase mb-2">Online reserveren</div>
          <h1 class="text-3xl font-medium text-white md:text-4xl">Plan jouw bezoek</h1>
          <p class="text-sm text-stone-400 mt-2">{{ stepLabels[step - 1] }}</p>
        </div>

        <!-- Stap indicator -->
        <div class="flex items-center justify-center gap-2 mb-10">
          <template v-for="n in 4" :key="n">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-medium transition-all"
              :class="n < step
                ? 'bg-white text-stone-950'
                : n === step
                  ? 'bg-amber-500 text-stone-950'
                  : 'bg-stone-700 text-stone-400'">
              <span v-if="n < step">✓</span>
              <span v-else>{{ n }}</span>
            </div>
            <div v-if="n < 4" class="w-8 h-px transition-colors"
              :class="n < step ? 'bg-white' : 'bg-stone-700'" />
          </template>
        </div>

        <div class="rounded-[1.5rem] border border-stone-700 bg-stone-950 p-7 shadow-[0_24px_90px_rgba(0,0,0,0.2)]">

          <!-- STAP 1: Dienst -->
          <template v-if="step === 1">
            <h2 class="mb-5 text-sm font-medium uppercase tracking-wide text-stone-400">Kies een dienst</h2>
            <div class="flex flex-col gap-2">
              <button v-for="s in services" :key="s.id" type="button"
                @click="selectService(s)"
                class="flex w-full cursor-pointer touch-manipulation items-center justify-between rounded-xl border p-4 text-left transition-all active:scale-[0.99]"
                :class="form.service_id === s.id ? 'border-amber-500 bg-white/5' : 'border-stone-700 hover:border-stone-500 hover:bg-white/5'">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-sm"
                    :style="{ background: s.color + '22', color: s.color }">
                    <img src="/landing-media/pic-3.png" alt="Dienst" class="h-full w-full rounded-full object-cover" />
                  </div>
                  <div>
                    <div class="text-sm font-medium text-white">{{ s.name }}</div>
                    <div class="text-xs text-stone-400">{{ s.duration_label }}</div>
                  </div>
                </div>
                <span class="text-sm font-medium text-white">{{ s.price_formatted }}</span>
              </button>
            </div>
          </template>

          <!-- STAP 2: Barber -->
          <template v-if="step === 2">
            <h2 class="mb-5 text-sm font-medium uppercase tracking-wide text-stone-400">Kies je barber</h2>
            <div class="flex flex-col gap-3">
              <button v-for="b in availableBarbers" :key="b.id" type="button"
                @click="selectBarber(b)"
                class="flex w-full cursor-pointer touch-manipulation items-center gap-4 rounded-xl border p-4 text-left transition-all active:scale-[0.99]"
                :class="form.barber_id === b.id ? 'border-amber-500 bg-white/5' : 'border-stone-700 hover:border-stone-500 hover:bg-white/5'">
                <div class="w-12 h-12 rounded-full overflow-hidden bg-stone-800 flex-shrink-0">
                  <img src="/landing-media/barber-portrait.png" alt="Barber" class="h-full w-full object-cover" />
                </div>
                <div>
                  <div class="text-sm font-medium text-white">{{ b.name }}</div>
                  <div class="flex flex-wrap gap-1 mt-1.5">
                    <span v-for="spec in barberSpecialties(b)" :key="spec"
                      class="text-xs px-2 py-0.5 bg-stone-700 text-stone-300 rounded-full">{{ spec }}</span>
                  </div>
                </div>
              </button>
            </div>
          </template>

          <!-- STAP 3: Datum & tijd -->
          <template v-if="step === 3">
            <h2 class="mb-5 text-sm font-medium uppercase tracking-wide text-stone-400">Kies datum en tijd</h2>
            <div class="mb-5">
                <label for="date" class="mb-2 block text-sm font-medium text-stone-600">Datum</label>
                  <MonthPicker v-model="form.date" :min-date="minDate" @update:modelValue="loadSlots" />
            </div>
            <div v-if="form.date">
              <label class="text-xs text-stone-400 uppercase tracking-wide mb-3 block">Beschikbare tijden</label>
              <div v-if="loadingSlots" class="text-xs text-stone-400 text-center py-6">Beschikbaarheid laden…</div>
              <div v-else-if="slots.length === 0" class="text-xs text-stone-400 text-center py-6 bg-stone-900 rounded-xl border border-stone-700">
                Geen beschikbare tijden op deze dag. Kies een andere datum.
              </div>
              <div v-else class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                <button v-for="slot in slots" :key="slot" type="button"
                  @click="form.time = slot"
                  class="cursor-pointer touch-manipulation py-2.5 text-xs border rounded-lg transition-all font-medium active:scale-[0.99]"
                  :class="form.time === slot
                    ? 'bg-white text-stone-950 border-white'
                    : 'border-stone-700 text-stone-300 hover:border-stone-500 hover:bg-white/5'">
                  {{ slot }}
                </button>
              </div>
            </div>
          </template>

          <!-- STAP 4: Gegevens -->
          <template v-if="step === 4">
            <h2 class="mb-5 text-sm font-medium uppercase tracking-wide text-stone-400">Jouw gegevens</h2>

            <!-- Samenvatting -->
            <div class="rounded-xl border border-stone-700 bg-stone-900 p-4 mb-6 space-y-2.5">
              <div class="flex justify-between text-xs">
                <span class="text-stone-400">Dienst</span>
                <span class="font-medium text-white">{{ selectedService?.name }}</span>
              </div>
              <div class="flex justify-between text-xs">
                <span class="text-stone-400">Barber</span>
                <span class="font-medium text-white">{{ selectedBarber?.name }}</span>
              </div>
              <div class="flex justify-between text-xs">
                <span class="text-stone-400">Datum & tijd</span>
                <span class="font-medium text-white">{{ formattedDateTime }}</span>
              </div>
              <div class="flex justify-between text-xs pt-2 border-t border-stone-700">
                <span class="text-stone-400">Prijs</span>
                <span class="font-semibold text-white">{{ selectedService?.price_formatted }}</span>
              </div>
            </div>

            <form @submit.prevent="submitBooking" class="space-y-4">
              <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div>
                  <label class="text-xs text-stone-400 uppercase tracking-wide mb-1 block">Naam *</label>
                  <input v-model="form.name" type="text" required placeholder="Jouw naam"
                    class="w-full border border-stone-700 rounded-lg px-3 py-2.5 text-sm text-white bg-stone-900 focus:outline-none focus:border-stone-500" />
                  <p v-if="errors.name" class="text-xs text-red-500 mt-1">{{ errors.name }}</p>
                </div>
                <div>
                  <label class="text-xs text-stone-400 uppercase tracking-wide mb-1 block">Telefoon *</label>
                  <input v-model="form.phone" type="tel" required placeholder="+31 6 ..."
                    class="w-full border border-stone-700 rounded-lg px-3 py-2.5 text-sm text-white bg-stone-900 focus:outline-none focus:border-stone-500" />
                  <p v-if="errors.phone" class="text-xs text-red-500 mt-1">{{ errors.phone }}</p>
                </div>
              </div>
              <div>
                <label class="text-xs text-stone-400 uppercase tracking-wide mb-1 block">E-mail *</label>
                <input v-model="form.email" type="email" required placeholder="jouw@email.nl"
                  class="w-full border border-stone-700 rounded-lg px-3 py-2.5 text-sm text-white bg-stone-900 focus:outline-none focus:border-stone-500" />
                <p v-if="errors.email" class="text-xs text-red-500 mt-1">{{ errors.email }}</p>
              </div>
              <div>
                <label class="text-xs text-stone-400 uppercase tracking-wide mb-1 block">Opmerkingen</label>
                <textarea v-model="form.notes" rows="3" placeholder="Bijv. wensen of allergieën"
                  class="w-full resize-none border border-stone-700 rounded-lg px-3 py-2.5 text-sm text-white bg-stone-900 focus:outline-none focus:border-stone-500" />
              </div>
              <p v-if="errors.starts_at" class="text-xs text-red-500 bg-red-50 p-3 rounded-lg border border-red-100">
                {{ errors.starts_at }}
              </p>
              <button type="submit" :disabled="submitting"
                class="w-full bg-white text-stone-950 text-xs py-3.5 tracking-widest hover:bg-stone-200 disabled:opacity-40 transition-colors">
                {{ submitting ? 'BEZIG...' : 'AFSPRAAK BEVESTIGEN' }}
              </button>
            </form>
          </template>

        </div>

        <!-- Vorige / volgende -->
        <div class="flex justify-between mt-5">
          <button v-if="step > 1" type="button" @click="step--"
            class="text-xs text-stone-400 hover:text-white transition-colors">
            ← Vorige stap
          </button>
          <div v-else />
          <button v-if="step < 4" type="button" @click="nextStep" :disabled="!canProceed"
            class="bg-white text-stone-950 text-xs px-6 py-2.5 hover:bg-stone-200 disabled:opacity-30 transition-colors">
            Volgende →
          </button>
        </div>

      </div>
    </div>
</template>

<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import MonthPicker from '../../components/MonthPicker.vue'

interface Service {
  id: string
  name: string
  duration_label: string
  price_formatted: string
  color: string
}

interface Barber {
  id: string
  name: string
  specialties?: string[]
  service_ids?: string[]
}

const props = defineProps<{
  services: Service[]
  barbers: Barber[]
}>()

const step         = ref(1)
const slots        = ref<string[]>([])
const loadingSlots = ref(false)
const submitting   = ref(false)
const errors       = ref<Record<string, string>>({})

const form = ref({
  service_id: '' as string,
  barber_id:  '' as string,
  date:       '',
  time:       '',
  name:       '',
  email:      '',
  phone:      '',
  notes:      '',
})

const stepLabels = ['Kies een dienst', 'Kies je barber', 'Kies datum en tijd', 'Vul je gegevens in']

const selectedService = computed(() => props.services.find((service: Service) => service.id === form.value.service_id))
const selectedBarber  = computed(() => props.barbers.find((barber: Barber) => barber.id === form.value.barber_id))

function barberSpecialties(barber: Barber): string[] {
  return (barber.specialties ?? []).slice(0, 3)
}

const availableBarbers = computed(() =>
  form.value.service_id
    ? props.barbers.filter((barber: Barber) => (barber.service_ids ?? []).includes(form.value.service_id))
    : props.barbers
)

const minDate = computed(() => new Date().toISOString().split('T')[0])

const formattedDateTime = computed(() => {
  if (!form.value.date || !form.value.time) {
return '–'
}

  const d = new Date(`${form.value.date}T${form.value.time}`)

  return d.toLocaleDateString('nl-NL', { weekday: 'short', day: 'numeric', month: 'short' }) + ' ' + form.value.time
})

const canProceed = computed(() => {
  if (step.value === 1) {
return !!form.value.service_id
}

  if (step.value === 2) {
return !!form.value.barber_id
}

  if (step.value === 3) {
return !!(form.value.date && form.value.time)
}

  return false
})

function selectService(s: Service) {
  form.value.service_id = s.id
  form.value.barber_id  = ''
  step.value = 2
}

function selectBarber(b: Barber) {
  form.value.barber_id = b.id
  step.value = 3
}

function nextStep() {
  if (canProceed.value) {
step.value++
}
}

async function loadSlots() {
  if (!form.value.date || !form.value.barber_id || !form.value.service_id) {
return
}

  form.value.time = ''
  loadingSlots.value = true

  try {
    const params = new URLSearchParams({
      service_id: form.value.service_id,
      barber_id:  form.value.barber_id,
      date:       form.value.date,
    })
    const res  = await fetch(`/boeken/beschikbaarheid?${params}`)
    const data = await res.json()
    slots.value = data.slots ?? []
  } finally {
    loadingSlots.value = false
  }
}

function submitBooking() {
  submitting.value = true
  errors.value = {}
  router.post('/boeken', form.value as any, {
    onError:  (e: Record<string, string>) => {
 errors.value = e 
},
    onFinish: () => {
 submitting.value = false 
},
  })
}
</script>
