<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import AdminLayout from '@/layouts/adminLayout.vue'

const props = defineProps<{
  settings: Record<string, any>
  openingHours: Array<{ day: number; open: string | null; close: string | null }>
}>()

const form = ref({ ...props.settings })
const hours = ref([...props.openingHours.map(h => ({ ...h }))])
const saving = ref(false)

const dayNames = ['Zondag', 'Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag']

function isClosed(day: number) {
  return hours.value[day].open === null || hours.value[day].close === null
}

function setClosed(day: number, closed: boolean) {
  if (closed) {
    hours.value[day].open = null
    hours.value[day].close = null
  } else {
    hours.value[day].open = '10:00'
    hours.value[day].close = '18:00'
  }
}

function save() {
  saving.value = true
  router.post('/admin/instellingen', {
    ...form.value,
    opening_hours: hours.value,
  }, {
    onFinish: () => { saving.value = false },
    preserveScroll: true,
  })
}
</script>

<template>
  <AdminLayout title="Instellingen">
    <Head title="Admin — Instellingen" />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
      <!-- Business Info -->
      <div class="overflow-hidden rounded-[1.25rem] border border-stone-700 bg-stone-900 p-6">
        <h2 class="mb-5 text-sm font-medium text-white">Bedrijfsinformatie</h2>
        <div class="space-y-4">
          <div>
            <label class="mb-1 block text-[11px] uppercase tracking-wider text-stone-500">Naam</label>
            <input v-model="form.business_name" required class="w-full rounded-lg border border-stone-700 bg-stone-900 px-3 py-2 text-sm text-white focus:outline-none focus:border-stone-500" />
          </div>
          <div>
            <label class="mb-1 block text-[11px] uppercase tracking-wider text-stone-500">Adres</label>
            <input v-model="form.address" required class="w-full rounded-lg border border-stone-700 bg-stone-900 px-3 py-2 text-sm text-white focus:outline-none focus:border-stone-500" />
          </div>
          <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div>
              <label class="mb-1 block text-[11px] uppercase tracking-wider text-stone-500">Telefoon</label>
              <input v-model="form.phone" required class="w-full rounded-lg border border-stone-700 bg-stone-900 px-3 py-2 text-sm text-white focus:outline-none focus:border-stone-500" />
            </div>
            <div>
              <label class="mb-1 block text-[11px] uppercase tracking-wider text-stone-500">Email</label>
              <input v-model="form.email" type="email" required class="w-full rounded-lg border border-stone-700 bg-stone-900 px-3 py-2 text-sm text-white focus:outline-none focus:border-stone-500" />
            </div>
          </div>
          <div>
            <label class="mb-1 block text-[11px] uppercase tracking-wider text-stone-500">Website</label>
            <input v-model="form.website" type="url" class="w-full rounded-lg border border-stone-700 bg-stone-900 px-3 py-2 text-sm text-white focus:outline-none focus:border-stone-500" placeholder="https://..." />
          </div>
          <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div>
              <label class="mb-1 block text-[11px] uppercase tracking-wider text-stone-500">Instagram</label>
              <input v-model="form.instagram" type="url" class="w-full rounded-lg border border-stone-700 bg-stone-900 px-3 py-2 text-sm text-white focus:outline-none focus:border-stone-500" placeholder="https://..." />
            </div>
            <div>
              <label class="mb-1 block text-[11px] uppercase tracking-wider text-stone-500">TikTok</label>
              <input v-model="form.tiktok" type="url" class="w-full rounded-lg border border-stone-700 bg-stone-900 px-3 py-2 text-sm text-white focus:outline-none focus:border-stone-500" placeholder="https://..." />
            </div>
          </div>
          <div>
            <label class="mb-1 block text-[11px] uppercase tracking-wider text-stone-500">Google Maps URL</label>
            <input v-model="form.google_maps" type="url" class="w-full rounded-lg border border-stone-700 bg-stone-900 px-3 py-2 text-sm text-white focus:outline-none focus:border-stone-500" placeholder="https://..." />
          </div>
        </div>
      </div>

      <!-- Booking Config -->
      <div class="overflow-hidden rounded-[1.25rem] border border-stone-700 bg-stone-900 p-6">
        <h2 class="mb-5 text-sm font-medium text-white">Boekingsconfiguratie</h2>
        <div class="space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-xs font-medium text-white">Boekingen accepteren</div>
              <div class="text-[11px] text-stone-400">Sta online boekingen toe</div>
            </div>
            <label class="relative inline-flex cursor-pointer items-center">
              <input v-model="form.booking_enabled" type="checkbox" class="peer sr-only" />
              <div class="h-5 w-9 rounded-full bg-stone-700 transition-colors peer-checked:bg-amber-500 peer-focus:ring-2 peer-focus:ring-amber-500/30" />
              <div class="absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white transition-transform peer-checked:translate-x-4" />
            </label>
          </div>
          <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div>
              <label class="mb-1 block text-[11px] uppercase tracking-wider text-stone-500">Dagen vooruit</label>
              <input v-model.number="form.advance_booking_days" type="number" min="1" max="90" required class="w-full rounded-lg border border-stone-700 bg-stone-900 px-3 py-2 text-sm text-white focus:outline-none focus:border-stone-500" />
            </div>
            <div>
              <label class="mb-1 block text-[11px] uppercase tracking-wider text-stone-500">Slot interval (min)</label>
              <input v-model.number="form.slot_interval_minutes" type="number" min="5" max="60" required class="w-full rounded-lg border border-stone-700 bg-stone-900 px-3 py-2 text-sm text-white focus:outline-none focus:border-stone-500" />
            </div>
          </div>
          <div>
            <label class="mb-1 block text-[11px] uppercase tracking-wider text-stone-500">Valuta</label>
            <select v-model="form.currency" required class="w-full rounded-lg border border-stone-700 bg-stone-900 px-3 py-2 text-sm text-white focus:outline-none focus:border-stone-500">
              <option value="EUR">EUR (€)</option>
              <option value="USD">USD ($)</option>
              <option value="GBP">GBP (£)</option>
            </select>
          </div>
        </div>

        <h2 class="mt-8 mb-5 text-sm font-medium text-white">Openingstijden</h2>
        <div class="space-y-2">
          <div v-for="day in [1,2,3,4,5,6,0]" :key="day" class="flex flex-wrap items-center gap-2 border-b border-stone-800 py-2">
            <div class="w-24 flex-shrink-0 text-xs font-medium text-stone-300">{{ dayNames[day] }}</div>
            <label class="relative mr-2 inline-flex cursor-pointer items-center">
              <input :checked="!isClosed(day)" @change="e => setClosed(day, !(e.target as HTMLInputElement).checked)" type="checkbox" class="peer sr-only" />
              <div class="h-5 w-9 rounded-full bg-stone-700 transition-colors peer-checked:bg-amber-500" />
              <div class="absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white transition-transform peer-checked:translate-x-4" />
            </label>
            <template v-if="!isClosed(day)">
              <input v-model="hours[day].open" type="time" class="min-w-[120px] flex-1 rounded border border-stone-700 bg-stone-900 px-2 py-1 text-xs text-white focus:outline-none focus:border-stone-500" />
              <span class="text-xs text-stone-500">–</span>
              <input v-model="hours[day].close" type="time" class="min-w-[120px] flex-1 rounded border border-stone-700 bg-stone-900 px-2 py-1 text-xs text-white focus:outline-none focus:border-stone-500" />
            </template>
            <span v-else class="flex-1 text-xs text-stone-500">Gesloten</span>
          </div>
        </div>

        <div class="mt-6">
          <button
            @click="save"
            :disabled="saving"
            class="w-full rounded-lg border border-stone-600 bg-stone-900 py-2.5 text-xs text-white transition-colors hover:bg-stone-800 disabled:opacity-50"
          >
            {{ saving ? 'Opslaan...' : 'Opslaan' }}
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
