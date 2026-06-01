<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <section class="section-shell py-8 md:py-12">
        <div class="rounded-[1.25rem] border border-stone-700 bg-stone-950 px-8 py-12 text-center text-white md:px-12 md:py-16">
            <div class="section-kicker text-amber-500">Klaar voor een nieuwe look?</div>
            <h2 class="mt-4 text-3xl font-medium tracking-tight md:text-5xl">Plan jouw bezoek vandaag</h2>
            <p class="mx-auto mt-5 max-w-2xl text-sm leading-7 text-stone-400 md:text-base">
                Kies je dienst, selecteer een barber en pick een tijdslot. Directe bevestiging, gratis annuleren.
            </p>

            <Link href="/boeken" class="mt-8 inline-flex items-center rounded-md border border-stone-600 px-8 py-3 text-xs font-semibold uppercase tracking-[0.3em] text-white transition-colors hover:border-stone-400 hover:bg-white/5">
                Afspraak maken
            </Link>
        </div>
    </section>
</template>