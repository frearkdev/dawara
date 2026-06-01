<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import AdminLayout from '@/layouts/adminLayout.vue'

const props = defineProps<{
  reviews: {
    data: Array<{
      id: string
      rating: number
      comment: string
      visible: boolean
      source: string
      customer_name: string
      barber_name: string
      created_at: string
    }>
    links: Array<{ url: string | null; label: string; active: boolean }>
  }
  googlePlaceId: string
}>()

const importPlaceId = ref(props.googlePlaceId)
const importing = ref(false)
const pastedText = ref('')
const pasting = ref(false)
const manual = ref({ author: '', rating: 5, source: 'google', comment: '' })
const adding = ref(false)

function toggleVisibility(review: any) {
  router.patch(`/admin/reviews/${review.id}`, { visible: !review.visible }, { preserveScroll: true })
}

function importGoogle() {
  importing.value = true
  router.post('/admin/reviews/import-google', { place_id: importPlaceId.value || null }, {
    preserveScroll: true,
    onFinish: () => { importing.value = false },
  })
}

function importPasted() {
  if (!pastedText.value.trim()) return
  pasting.value = true
  router.post('/admin/reviews/import-pasted', { text: pastedText.value }, {
    preserveScroll: true,
    onFinish: () => { pasting.value = false },
  })
}

function addManual() {
  adding.value = true
  router.post('/admin/reviews', manual.value, {
    preserveScroll: true,
    onSuccess: () => {
      manual.value = { author: '', rating: 5, source: 'google', comment: '' }
    },
    onFinish: () => { adding.value = false },
  })
}
</script>

<template>
  <AdminLayout title="Reviews">
    <Head title="Admin — Reviews" />

    <!-- Google Import -->
    <div class="mb-5 overflow-hidden rounded-[1.25rem] border border-stone-700 bg-stone-900 p-5">
      <h3 class="mb-3 text-sm font-medium text-white">Google Reviews Importeren</h3>
      <p class="mb-4 text-[11px] text-stone-400">
        Klik 'Zoek reviews' om automatisch reviews op te halen via Google (gratis, geen API key nodig).
        Of plak reviews handmatig via het tekstvak hieronder.
      </p>

      <form @submit.prevent="importGoogle" class="flex items-end gap-3 mb-5">
        <div class="flex-1">
          <label class="mb-1 block text-[11px] uppercase tracking-wider text-stone-500">Google Place ID (optioneel — voor API)</label>
          <input
            v-model="importPlaceId"
            type="text"
            placeholder="ChIJ... (alleen nodig als je de betaalde API wilt gebruiken)"
            class="w-full rounded-lg border border-stone-700 bg-stone-950 px-3 py-2 text-sm text-white placeholder-stone-500 focus:outline-none focus:border-stone-500"
          />
        </div>
        <button
          type="submit"
          :disabled="importing"
          class="rounded-md border border-stone-600 bg-stone-950 px-4 py-2 text-xs text-white transition-colors hover:bg-stone-800 disabled:opacity-50"
        >
          {{ importing ? 'Zoeken...' : 'Zoek reviews' }}
        </button>
      </form>

      <div class="border-t border-stone-800 pt-4">
        <label class="mb-1 block text-[11px] uppercase tracking-wider text-stone-500">Reviews plakken van Google Maps</label>
        <form @submit.prevent="importPasted" class="flex flex-col gap-3">
          <textarea
            v-model="pastedText"
            rows="6"
            placeholder="Open Google Maps → Dawara Barbershop → Reviews → Kopieer de tekst van alle reviews hier...&#10;&#10;Voorbeeld:&#10;Ahmed&#10;★★★★★&#10;Goeie service en vriendelijk personeel!&#10;2 weken geleden"
            class="w-full resize-none rounded-lg border border-stone-700 bg-stone-950 px-3 py-2 text-sm text-white placeholder-stone-600 focus:outline-none focus:border-stone-500"
          />
          <div class="flex justify-end">
            <button
              type="submit"
              :disabled="pasting"
              class="rounded-md border border-stone-600 bg-stone-950 px-4 py-2 text-xs text-white transition-colors hover:bg-stone-800 disabled:opacity-50"
            >
              {{ pasting ? 'Importeren...' : 'Plaktekst importeren' }}
            </button>
          </div>
        </form>
      </div>

      <div class="border-t border-stone-800 pt-4">
        <label class="mb-1 block text-[11px] uppercase tracking-wider text-stone-500">Handmatig review toevoegen</label>
        <form @submit.prevent="addManual" class="grid grid-cols-2 gap-3">
          <div class="col-span-2">
            <input v-model="manual.author" type="text" placeholder="Naam klant" required class="w-full rounded-lg border border-stone-700 bg-stone-950 px-3 py-2 text-sm text-white placeholder-stone-600 focus:outline-none focus:border-stone-500" />
          </div>
          <div>
            <select v-model.number="manual.rating" required class="w-full rounded-lg border border-stone-700 bg-stone-950 px-3 py-2 text-sm text-white focus:outline-none focus:border-stone-500">
              <option :value="5">★★★★★ (5)</option>
              <option :value="4">★★★★☆ (4)</option>
              <option :value="3">★★★☆☆ (3)</option>
              <option :value="2">★★☆☆☆ (2)</option>
              <option :value="1">★☆☆☆☆ (1)</option>
            </select>
          </div>
          <div>
            <select v-model="manual.source" required class="w-full rounded-lg border border-stone-700 bg-stone-950 px-3 py-2 text-sm text-white focus:outline-none focus:border-stone-500">
              <option value="google">Google</option>
              <option value="system">Systeem (eigen)</option>
            </select>
          </div>
          <div class="col-span-2">
            <textarea v-model="manual.comment" rows="3" placeholder="Review tekst..." required class="w-full resize-none rounded-lg border border-stone-700 bg-stone-950 px-3 py-2 text-sm text-white placeholder-stone-600 focus:outline-none focus:border-stone-500" />
          </div>
          <div class="col-span-2 flex justify-end">
            <button
              type="submit"
              :disabled="adding"
              class="rounded-md border border-stone-600 bg-stone-950 px-4 py-2 text-xs text-white transition-colors hover:bg-stone-800 disabled:opacity-50"
            >
              {{ adding ? 'Toevoegen...' : 'Review toevoegen' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="mb-5 flex items-center justify-between">
      <div class="text-xs text-stone-500">{{ reviews.data.length }} reviews</div>
    </div>

    <div class="overflow-hidden rounded-[1.25rem] border border-stone-700 bg-stone-900">
      <div class="grid grid-cols-12 border-b border-stone-800 px-5 py-3 text-[11px] font-medium uppercase tracking-wider text-stone-500">
        <div class="col-span-2">Klant</div>
        <div class="col-span-1">Bron</div>
        <div class="col-span-1">Score</div>
        <div class="col-span-4">Comment</div>
        <div class="col-span-2">Status</div>
        <div class="col-span-2 text-right">Actie</div>
      </div>
      <div v-if="reviews.data.length === 0" class="px-5 py-8 text-center text-xs text-stone-500">
        Geen reviews gevonden.
      </div>
      <div v-for="r in reviews.data" :key="r.id" class="grid grid-cols-12 items-center border-b border-stone-800 px-5 py-3.5 transition-colors hover:bg-white/5">
        <div class="col-span-2 text-xs font-medium text-white truncate">{{ r.customer_name }}</div>
        <div class="col-span-1">
          <span
            class="rounded-full px-2 py-0.5 text-[10px] font-medium ring-1"
            :class="r.source === 'google' ? 'bg-blue-500/10 text-blue-300 ring-blue-500/20' : 'bg-stone-700 text-stone-400 ring-stone-600'"
          >
            {{ r.source === 'google' ? 'Google' : 'Systeem' }}
          </span>
        </div>
        <div class="col-span-1 text-xs text-amber-500 font-semibold">{{ r.rating }}/5</div>
        <div class="col-span-4 text-xs text-stone-300 truncate pr-3" :title="r.comment">{{ r.comment || '—' }}</div>
        <div class="col-span-2">
          <span v-if="r.visible" class="rounded-full px-2 py-0.5 text-[10px] font-medium bg-emerald-500/10 text-emerald-300 ring-1 ring-emerald-500/20">Zichtbaar</span>
          <span v-else class="rounded-full px-2 py-0.5 text-[10px] font-medium bg-stone-700 text-stone-400 ring-1 ring-stone-600">Verborgen</span>
        </div>
        <div class="col-span-2 text-right">
          <button
            @click="toggleVisibility(r)"
            class="text-[11px] transition-colors hover:text-amber-500"
            :class="r.visible ? 'text-stone-400' : 'text-amber-500'"
          >
            {{ r.visible ? 'Verberg' : 'Toon' }}
          </button>
        </div>
      </div>
    </div>

    <div v-if="reviews.links.length > 3" class="mt-5 flex items-center gap-2">
      <template v-for="link in reviews.links" :key="link.label">
        <button
          v-if="link.url"
          @click="router.get(link.url)"
          class="rounded-md border px-3 py-1.5 text-[11px] transition-colors"
          :class="link.active ? 'border-amber-500/30 bg-amber-500/10 text-amber-400' : 'border-stone-700 text-stone-400 hover:border-stone-500 hover:text-white'"
          v-html="link.label"
        />
        <span v-else class="px-3 py-1.5 text-[11px] text-stone-600" v-html="link.label" />
      </template>
    </div>
  </AdminLayout>
</template>
