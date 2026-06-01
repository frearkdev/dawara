<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps<{
  appointment: {
    id: string
    service_id: string
    barber_id: string
    starts_at: string
  }
  services: Array<{ id: string; name: string; duration_minutes: number }>
  barbers: Array<{ id: string; name: string }>
}>()

const form = ref({
  service_id: props.appointment.service_id,
  barber_id: props.appointment.barber_id,
  date: props.appointment.starts_at.split(' ')[0],
  time: props.appointment.starts_at.split(' ')[1]?.slice(0, 5) ?? '10:00',
})

const loadingSlots = ref(false)
const availableSlots = ref<string[]>([])
const errors = ref<Record<string, string>>({})

async function fetchSlots() {
  if (!form.value.service_id || !form.value.barber_id || !form.value.date) return
  loadingSlots.value = true
  try {
    const res = await fetch(`/boeken/beschikbaarheid?service_id=${form.value.service_id}&barber_id=${form.value.barber_id}&date=${form.value.date}`)
    const data = await res.json()
    availableSlots.value = data.slots ?? []
  } catch {
    availableSlots.value = []
  }
  loadingSlots.value = false
}

watch(() => [form.value.service_id, form.value.barber_id, form.value.date], fetchSlots, { immediate: true })

function submit() {
  router.post(`/afspraken/${props.appointment.id}/herboeken`, form.value)
}
</script>

<template>
  <Head title="Afspraak verplaatsen" />
  <div class="min-h-screen bg-stone-950 px-4 py-16 text-stone-100">
    <div class="mx-auto max-w-lg">
      <div class="mb-6 text-center">
        <h1 class="mb-1 text-xl font-medium text-white">Afspraak verplaatsen</h1>
        <p class="text-xs text-stone-400">Kies een nieuwe datum, tijd en eventueel andere dienst of barber.</p>
      </div>

      <form @submit.prevent="submit" class="space-y-4 rounded-[1.5rem] border border-stone-700 bg-stone-900 p-6 shadow-[0_30px_100px_rgba(0,0,0,0.35)]">
        <div>
          <label class="mb-1 block text-[11px] uppercase tracking-wider text-stone-500">Dienst</label>
          <select v-model="form.service_id" required class="w-full rounded-lg border border-stone-700 bg-stone-950 px-3 py-2.5 text-sm text-white focus:outline-none focus:border-stone-500">
            <option v-for="s in services" :key="s.id" :value="s.id">{{ s.name }} ({{ s.duration_minutes }} min)</option>
          </select>
        </div>

        <div>
          <label class="mb-1 block text-[11px] uppercase tracking-wider text-stone-500">Barber</label>
          <select v-model="form.barber_id" required class="w-full rounded-lg border border-stone-700 bg-stone-950 px-3 py-2.5 text-sm text-white focus:outline-none focus:border-stone-500">
            <option v-for="b in barbers" :key="b.id" :value="b.id">{{ b.name }}</option>
          </select>
        </div>

        <div>
          <label class="mb-1 block text-[11px] uppercase tracking-wider text-stone-500">Datum</label>
          <input v-model="form.date" type="date" required class="w-full rounded-lg border border-stone-700 bg-stone-950 px-3 py-2.5 text-sm text-white focus:outline-none focus:border-stone-500" />
        </div>

        <div>
          <label class="mb-1 block text-[11px] uppercase tracking-wider text-stone-500">Tijdslot</label>
          <div v-if="loadingSlots" class="py-2 text-xs text-stone-500">Beschikbaarheid laden...</div>
          <div v-else-if="availableSlots.length === 0" class="py-2 text-xs text-stone-500">Geen tijdsloten beschikbaar op deze dag.</div>
          <div v-else class="grid grid-cols-4 gap-2">
            <button
              v-for="slot in availableSlots" :key="slot"
              type="button"
              @click="form.time = slot"
              class="rounded-md border px-2 py-2 text-center text-xs transition-colors"
              :class="form.time === slot ? 'border-amber-500/30 bg-amber-500/10 text-amber-400' : 'border-stone-700 text-stone-300 hover:border-stone-500'"
            >
              {{ slot }}
            </button>
          </div>
          <input type="hidden" v-model="form.time" />
        </div>

        <div v-if="Object.keys(errors).length" class="rounded-lg border border-red-500/20 bg-red-500/10 p-3 text-xs text-red-300">
          <p v-for="(e, key) in errors" :key="key">{{ e }}</p>
        </div>

        <div class="flex gap-3 pt-2">
          <Link href="/" class="flex-1 rounded-md border border-stone-700 py-3 text-center text-xs text-stone-300 transition-colors hover:border-stone-500 hover:bg-white/5">
            Annuleren
          </Link>
          <button type="submit" class="flex-1 rounded-md border border-stone-600 bg-stone-950 py-3 text-xs text-white transition-colors hover:bg-stone-800">
            Verplaatsen
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
