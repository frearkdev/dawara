<template>
  <Head title="Afspraak bevestigd" />
    <div class="min-h-screen bg-stone-950 px-4 py-10 text-stone-100 sm:py-14">
      <div class="mx-auto grid max-w-4xl gap-6 lg:grid-cols-[0.9fr_1.1fr]">
        <div class="overflow-hidden rounded-[1.5rem] border border-stone-700 bg-stone-900 shadow-[0_30px_100px_rgba(0,0,0,0.35)]">
          <img src="/landing-media/pic-1.jpg" alt="Barbershop interieur" class="h-full min-h-52 w-full object-cover sm:min-h-64" />
        </div>
        <div class="w-full rounded-[1.5rem] border border-stone-700 bg-stone-900 p-5 text-center shadow-[0_30px_100px_rgba(0,0,0,0.35)] sm:p-8">
        <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-500/10 ring-1 ring-emerald-500/20">
          <span class="text-2xl text-emerald-300">✓</span>
        </div>
        <h1 class="mb-2 text-xl font-medium text-white">Afspraak bevestigd!</h1>
        <p class="mb-7 text-sm text-stone-400">
          Je ontvangt een bevestiging op <strong class="break-all text-white">{{ appointment.customer_email }}</strong>
        </p>

        <div class="mb-7 space-y-3 rounded-xl border border-stone-700 bg-stone-950 p-5 text-left">
          <div class="flex items-start justify-between gap-3 text-xs">
            <span class="text-stone-400">Dienst</span>
            <span class="text-right font-medium text-white">{{ appointment.service_name }}</span>
          </div>
          <div class="flex items-start justify-between gap-3 text-xs">
            <span class="text-stone-400">Barber</span>
            <span class="text-right font-medium text-white">{{ appointment.barber_name }}</span>
          </div>
          <div class="flex items-start justify-between gap-3 text-xs">
            <span class="text-stone-400">Datum & tijd</span>
            <span class="text-right font-medium text-white">{{ appointment.starts_at }}</span>
          </div>
          <div class="flex items-start justify-between gap-3 text-xs">
            <span class="text-stone-400">Duur</span>
            <span class="text-right font-medium text-white">tot {{ appointment.ends_at }}</span>
          </div>
          <div class="flex items-start justify-between gap-3 border-t border-stone-700 pt-2 text-xs">
            <span class="text-stone-400">Prijs</span>
            <span class="text-right font-semibold text-white">{{ appointment.price_formatted }}</span>
          </div>
          <div v-if="appointment.is_paid" class="flex items-start justify-between gap-3 text-xs">
            <span class="text-stone-400">Betaling</span>
            <span class="text-right font-medium text-emerald-400">Betaald ✓</span>
          </div>
          <div v-else-if="appointment.payment_status === 'open'" class="flex items-start justify-between gap-3 text-xs">
            <span class="text-stone-400">Betaling</span>
            <span class="text-right font-medium text-amber-400">In afwachting</span>
          </div>
        </div>

        <div class="mb-6 text-xs leading-relaxed text-stone-400">
          Wortelhaven 83, 8911 GL Leeuwarden<br>
          Gratis annuleren tot 2 uur voor aanvang.
        </div>

        <!-- Payment button -->
            <a
                v-if="!appointment.is_paid && appointment.status !== 'cancelled'"
                :href="`/payment/${appointment.id}`"
                class="block w-full rounded-md border border-amber-500/30 bg-amber-500/10 py-3 text-center text-[11px] font-medium tracking-[0.18em] text-amber-400 transition-colors hover:bg-amber-500/20 sm:text-xs"
            >
                BETAAL NU
            </a>

        <div v-if="appointment.can_cancel && appointment.status !== 'cancelled'" class="mt-3 grid gap-2 sm:grid-cols-2 sm:gap-3">
          <Link
            :href="`/afspraken/${appointment.id}/herboeken`"
            class="rounded-md border border-stone-700 py-3 text-center text-[11px] text-stone-300 transition-colors hover:border-stone-500 hover:bg-white/5 sm:text-xs"
          >
            HERBOEKEN
          </Link>
          <button
            @click="confirmCancel"
            class="rounded-md border border-red-500/20 bg-red-500/10 py-3 text-center text-[11px] text-red-300 transition-colors hover:bg-red-500/20 sm:text-xs"
          >
            ANNULEREN
          </button>
        </div>
        <div v-else-if="appointment.status === 'cancelled'" class="mt-3 rounded-md border border-red-500/20 bg-red-500/10 py-3 text-center text-[11px] text-red-300 sm:text-xs">
          Deze afspraak is geannuleerd.
        </div>

        <Link href="/" class="mt-3 block w-full rounded-md border border-stone-700 bg-white py-3 text-center text-[11px] font-medium tracking-[0.18em] text-stone-950 transition-colors hover:bg-stone-200 sm:text-xs">
          TERUG NAAR HOME
        </Link>
        <Link href="/boeken" class="mt-3 block w-full rounded-md border border-stone-700 py-3 text-center text-[11px] text-stone-300 transition-colors hover:border-stone-500 hover:bg-white/5 sm:text-xs">
          Nog een afspraak maken
        </Link>
        </div>
      </div>
    </div>
</template>

<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'

const props = defineProps<{
  appointment: Record<string, any>
}>()

function confirmCancel() {
  if (confirm('Weet je zeker dat je deze afspraak wilt annuleren?')) {
    router.post(`/afspraken/${props.appointment.id}/annuleren`)
  }
}
</script>
