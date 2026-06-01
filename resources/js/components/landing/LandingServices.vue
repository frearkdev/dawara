<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

type ServiceCard = {
    id: number;
    name: string;
    description: string;
    price_formatted: string;
    duration_label: string;
    color: string;
};

defineProps<{
    services: ServiceCard[];
}>();

</script>

<template>
    <section id="diensten" class="section-shell py-16 md:py-20">
        <div class="section-kicker mb-2 text-amber-500">Wat we aanbieden</div>
        <div class="flex items-end justify-between gap-6">
            <div>
                <h2 class="section-title text-white">ONZE DIENSTEN</h2>
                <p class="section-copy mt-1 text-stone-400">
                    Gespecialiseerde herenkapsels, baardverzorging en huidverzorging met de nieuwste apparaten en technologieën.
                </p>
            </div>
            <Link href="/boeken" class="hidden rounded-md border border-stone-600 px-4 py-2 text-[11px] uppercase tracking-[0.08em] text-stone-300 transition-colors hover:border-stone-400 hover:text-white md:inline-flex">
                Direct boeken →
            </Link>
        </div>
        <div class="mt-9 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="service in services"
                :key="service.id"
                class="group rounded-[1.25rem] border border-stone-700 bg-stone-900 p-5 transition-colors hover:border-stone-500 hover:bg-white/[0.02]"
            >
                <div class="flex items-center gap-3 mb-3">
                    <div class="text-sm font-medium text-white">{{ service.name }}</div>
                </div>
                <div class="text-xs leading-6 text-stone-400 mb-4">{{ service.description }}</div>
                <div class="flex items-baseline justify-between border-t border-stone-800 pt-3">
                    <span class="text-base font-medium text-white">{{ service.price_formatted }}</span>
                    <span class="text-xs text-stone-500">{{ service.duration_label }}</span>
                </div>
            </div>
        </div>
    </section>
</template>
