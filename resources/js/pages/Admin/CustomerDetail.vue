<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/adminLayout.vue'
import AdminStatusBadge from '@/components/admin/AdminStatusBadge.vue'

const props = defineProps<{
  customer: {
    id: string
    name: string
    email: string
    phone: string
    created_at: string
  }
  appointments: Array<{
    id: string
    starts_at: string
    service_name: string
    barber_name: string
    status: string
    price_formatted: string
    is_paid: boolean
  }>
}>()
</script>

<template>
  <AdminLayout title="Klantdetails">
    <Head title="Admin — Klant" />

    <div class="mb-6 flex items-center gap-2 text-xs text-stone-500">
      <button @click="router.get('/admin/klanten')" class="transition-colors hover:text-amber-500">← Klanten</button>
      <span>/</span>
      <span class="text-stone-300">{{ customer.name }}</span>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
      <div class="col-span-1 overflow-hidden rounded-[1.25rem] border border-stone-700 bg-stone-900 p-6">
        <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-stone-800 text-lg font-semibold text-amber-500">
          {{ customer.name.charAt(0) }}
        </div>
        <h2 class="mb-1 text-sm font-medium text-white">{{ customer.name }}</h2>
        <p class="mb-5 text-xs text-stone-400">{{ customer.email }}</p>

        <div class="space-y-3">
          <div>
            <div class="text-[11px] uppercase tracking-wider text-stone-500">Telefoon</div>
            <div class="text-xs text-stone-300">{{ customer.phone || 'Niet ingevuld' }}</div>
          </div>
          <div>
            <div class="text-[11px] uppercase tracking-wider text-stone-500">Klant sinds</div>
            <div class="text-xs text-stone-300">{{ customer.created_at }}</div>
          </div>
          <div>
            <div class="text-[11px] uppercase tracking-wider text-stone-500">Afspraken</div>
            <div class="text-xs text-stone-300">{{ appointments.length }}</div>
          </div>
        </div>
      </div>

      <div class="col-span-1 lg:col-span-2 overflow-hidden rounded-[1.25rem] border border-stone-700 bg-stone-900">
        <div class="flex items-center justify-between border-b border-stone-800 px-5 py-4">
          <h2 class="text-sm font-medium text-white">Afspraakhistorie</h2>
        </div>
        <div v-if="appointments.length === 0" class="px-5 py-8 text-center text-xs text-stone-500">
          Geen afspraken gevonden.
        </div>
        <div v-else class="divide-y divide-stone-700">
          <div v-for="a in appointments" :key="a.id" class="flex flex-col gap-2 px-5 py-3.5 transition-colors hover:bg-white/5 sm:flex-row sm:items-center sm:gap-3">
            <div class="w-full text-left sm:w-20 sm:flex-shrink-0 sm:text-center">
              <div class="text-xs font-medium text-white">{{ a.starts_at.split(' ')[0] }}</div>
              <div class="text-[11px] text-stone-500">{{ a.starts_at.split(' ')[1] }}</div>
            </div>
            <div class="flex-1 min-w-0">
              <div class="truncate text-xs font-medium text-white">{{ a.service_name }}</div>
              <div class="truncate text-xs text-stone-400">{{ a.barber_name }}</div>
            </div>
            <div class="flex flex-wrap items-center gap-2 sm:flex-shrink-0">
              <span class="text-xs text-stone-400">{{ a.price_formatted }}</span>
              <span v-if="a.is_paid" class="rounded-full px-2 py-0.5 text-[10px] font-medium bg-emerald-500/10 text-emerald-300 ring-1 ring-emerald-500/20">Betaald</span>
              <span v-else class="rounded-full px-2 py-0.5 text-[10px] font-medium bg-amber-500/10 text-amber-300 ring-1 ring-amber-500/20">Open</span>
              <AdminStatusBadge :status="a.status" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>