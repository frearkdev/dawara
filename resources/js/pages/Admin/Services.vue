<template>
  <AdminLayout title="Diensten">
    <Head title="Admin — Diensten" />

    <div class="flex justify-end mb-5">
      <button @click="showForm = true"
        class="rounded-md border border-stone-700 bg-stone-950 px-5 py-2.5 text-xs text-white transition-colors hover:border-stone-500 hover:bg-white/5">
        + Nieuwe dienst
      </button>
    </div>

    <!-- Diensten grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="s in services" :key="s.id"
        class="rounded-[1.25rem] border border-stone-700 bg-stone-900 p-5">
        <div class="flex items-start justify-between mb-3">
          <img src="/landing-media/pic-3.png" alt="Service media" class="h-12 w-12 rounded-full object-cover ring-1 ring-white/10" />
          <div class="flex gap-1">
            <button @click="startEdit(s)"
              class="rounded border border-stone-700 px-2.5 py-1 text-xs text-stone-300 hover:border-stone-500 hover:bg-white/5">
              Bewerk
            </button>
            <button @click="deleteService(s.id)"
              class="rounded border border-red-500/20 px-2.5 py-1 text-xs text-red-300 hover:bg-red-500/10">
              ×
            </button>
          </div>
        </div>
        <div class="mb-1 text-sm font-medium text-white">{{ s.name }}</div>
        <div class="mb-3 text-xs leading-relaxed text-stone-400">{{ s.description }}</div>
        <div class="flex items-center justify-between">
          <span class="text-base font-medium text-white">{{ s.price_formatted }}</span>
          <span class="text-xs text-stone-500">{{ s.duration_label }}</span>
        </div>
        <div class="mt-2">
          <span class="text-xs px-2 py-0.5 rounded-full"
            :class="s.active ? 'bg-emerald-500/10 text-emerald-300 ring-1 ring-emerald-500/20' : 'bg-stone-700/60 text-stone-300 ring-1 ring-stone-600'">
            {{ s.active ? 'Actief' : 'Inactief' }}
          </span>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
      <div class="w-full max-w-md rounded-[1.5rem] border border-stone-700 bg-stone-950 p-6 shadow-[0_30px_100px_rgba(0,0,0,0.4)]">
        <h2 class="mb-5 text-sm font-medium text-white">
          {{ editingService ? 'Dienst bewerken' : 'Nieuwe dienst' }}
        </h2>
        <form @submit.prevent="saveService" class="space-y-4">
          <div>
            <label class="text-xs text-stone-400 uppercase tracking-wide mb-1 block">Naam *</label>
            <input v-model="form.name" type="text" required
              class="w-full rounded-lg border border-stone-700 bg-stone-900 px-3 py-2.5 text-sm text-white focus:border-stone-500 focus:outline-none" />
          </div>
          <div>
            <label class="text-xs text-stone-400 uppercase tracking-wide mb-1 block">Omschrijving</label>
            <textarea v-model="form.description" rows="2"
              class="w-full resize-none rounded-lg border border-stone-700 bg-stone-900 px-3 py-2.5 text-sm text-white focus:border-stone-500 focus:outline-none" />
          </div>
          <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div>
              <label class="text-xs text-stone-400 uppercase tracking-wide mb-1 block">Duur (min) *</label>
              <input v-model.number="form.duration_minutes" type="number" min="5" required
                class="w-full rounded-lg border border-stone-700 bg-stone-900 px-3 py-2.5 text-sm text-white focus:border-stone-500 focus:outline-none" />
            </div>
            <div>
              <label class="text-xs text-stone-400 uppercase tracking-wide mb-1 block">Prijs (centen) *</label>
              <input v-model.number="form.price_cents" type="number" min="0" required
                class="w-full rounded-lg border border-stone-700 bg-stone-900 px-3 py-2.5 text-sm text-white focus:border-stone-500 focus:outline-none" />
              <p class="mt-1 text-xs text-stone-500">{{ (form.price_cents / 100).toFixed(2) }} EUR</p>
            </div>
          </div>
          <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div>
              <label class="text-xs text-stone-400 uppercase tracking-wide mb-1 block">Kleur</label>
              <input v-model="form.color" type="color"
                class="h-10 w-full cursor-pointer rounded-lg border border-stone-700 bg-stone-900" />
            </div>
            <div class="flex items-end pb-1">
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="form.active" type="checkbox" class="rounded" />
                <span class="text-xs text-stone-300">Actief</span>
              </label>
            </div>
          </div>
          <div class="flex gap-2 pt-2">
            <button type="submit"
              class="flex-1 rounded-md border border-stone-700 bg-stone-950 py-2.5 text-xs text-white transition-colors hover:border-stone-500 hover:bg-white/5">
              {{ editingService ? 'Opslaan' : 'Aanmaken' }}
            </button>
            <button type="button" @click="closeForm"
              class="flex-1 rounded-md border border-stone-700 text-xs py-2.5 text-stone-300 transition-colors hover:border-stone-500 hover:bg-white/5">
              Annuleren
            </button>
          </div>
        </form>
      </div>
    </div>

  </AdminLayout>
</template>

<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import AdminLayout from '@/layouts/adminLayout.vue'

defineProps<{ services: Array<Record<string, any>> }>()

const showForm      = ref(false)
const editingService = ref<Record<string, any> | null>(null)

const form = ref({
  name:             '',
  description:      '',
  duration_minutes: 30,
  price_cents:      2000,
  color:            '#B8860B',
  active:           true,
})

function startEdit(s: Record<string, any>) {
  editingService.value = s
  form.value = {
    name:             s.name,
    description:      s.description ?? '',
    duration_minutes: s.duration_minutes,
    price_cents:      s.price_cents,
    color:            s.color,
    active:           s.active,
  }
  showForm.value = true
}

function closeForm() {
  showForm.value = false
  editingService.value = null
  form.value = { name: '', description: '', duration_minutes: 30, price_cents: 2000, color: '#B8860B', active: true }
}

function saveService() {
  if (editingService.value) {
    router.patch(`/admin/diensten/${editingService.value.id}`, form.value, {
      onSuccess: closeForm, preserveScroll: true,
    })
  } else {
    router.post('/admin/diensten', form.value, {
      onSuccess: closeForm, preserveScroll: true,
    })
  }
}

function deleteService(id: string) {
  if (confirm('Weet je zeker dat je deze dienst wil verwijderen?')) {
    router.delete(`/admin/diensten/${id}`, { preserveScroll: true })
  }
}
</script>
