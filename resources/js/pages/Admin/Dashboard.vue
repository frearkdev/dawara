<template>
  <AdminLayout title="Dashboard">
    <Head title="Admin — Dashboard" />

    <div class="mb-8 grid grid-cols-2 gap-4 md:grid-cols-4">
      <AdminStatCard label="Afspraken vandaag" :value="String(stats.today_count)" />
      <AdminStatCard label="Deze week" :value="String(stats.week_count)" />
      <AdminStatCard label="Omzet deze maand" :value="'€' + stats.month_revenue" />
      <AdminStatCard label="Klanten totaal" :value="String(stats.customer_count)" />
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

      <div class="overflow-hidden rounded-[1.5rem] border border-stone-700 bg-stone-900">
        <div class="flex items-center justify-between border-b border-stone-700 px-5 py-4">
          <h2 class="text-sm font-medium text-white">Vandaag</h2>
          <span class="text-xs text-stone-500">{{ todayAppointments.length }} afspraken</span>
        </div>
        <div v-if="todayAppointments.length === 0" class="px-5 py-8 text-center text-xs text-stone-500">
          Geen afspraken vandaag.
        </div>
        <div v-else class="divide-y divide-stone-700">
          <div v-for="a in todayAppointments" :key="a.id"
            class="flex items-center gap-3 px-5 py-3.5 transition-colors hover:bg-white/5">
            <div class="text-center flex-shrink-0 w-12">
              <div class="text-sm font-medium text-white">{{ a.starts_at }}</div>
              <div class="text-xs text-stone-500">{{ a.ends_at }}</div>
            </div>
            <div class="flex-1 min-w-0">
              <div class="truncate text-xs font-medium text-white">{{ a.customer_name }}</div>
              <div class="truncate text-xs text-stone-400">{{ a.service_name }} · {{ a.barber_name }}</div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
              <span class="text-xs text-stone-400">{{ a.price_formatted }}</span>
              <AdminStatusBadge :status="a.status" />
            </div>
          </div>
        </div>
      </div>

      <div class="overflow-hidden rounded-[1.5rem] border border-stone-700 bg-stone-900">
        <div class="flex items-center justify-between border-b border-stone-700 px-5 py-4">
          <h2 class="text-sm font-medium text-white">Aankomend</h2>
          <a href="/admin/afspraken" class="text-xs text-amber-500 hover:underline">Alles →</a>
        </div>
        <div v-if="upcomingAppointments.length === 0" class="px-5 py-8 text-center text-xs text-stone-500">
          Geen komende afspraken.
        </div>
        <div v-else class="divide-y divide-stone-700">
          <div v-for="a in upcomingAppointments" :key="a.id"
            class="flex items-center gap-3 px-5 py-3.5 transition-colors hover:bg-white/5">
            <div class="flex-1 min-w-0">
              <div class="truncate text-xs font-medium text-white">{{ a.customer_name }}</div>
              <div class="truncate text-xs text-stone-400">{{ a.service_name }}</div>
            </div>
            <div class="text-right flex-shrink-0">
              <div class="text-xs font-medium text-stone-200">{{ a.starts_at }}</div>
              <div class="text-xs text-stone-500">{{ a.barber_name }}</div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AdminStatCard from '@/components/admin/AdminStatCard.vue'
import AdminStatusBadge from '@/components/admin/AdminStatusBadge.vue'
import AdminLayout from '@/layouts/adminLayout.vue'

defineProps<{
  stats:                Record<string, any>
  todayAppointments:    Array<Record<string, any>>
  upcomingAppointments: Array<Record<string, any>>
  barbers:              Array<Record<string, any>>
}>()

</script>
