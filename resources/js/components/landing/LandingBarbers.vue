<script setup lang="ts">
type BarberCard = {
    id: number;
    name: string;
    bio: string;
    specialties: string[];
    average_rating: number;
    review_count: number;
};

defineProps<{
    barbers: BarberCard[];
}>();
</script>

<template>
    <section id="team" class="border-t border-stone-700/60 bg-stone-950 py-16 text-white md:py-20">
        <div class="section-shell">
            <div class="max-w-2xl">
                <div class="section-kicker text-amber-500">Het team</div>
                <h2 class="section-title mt-3 text-white">Jouw barbers</h2>
                <p class="section-copy text-stone-400">
                    Vakkundige handen, vaste gezichten en aandacht voor detail in elke stoel.
                </p>
            </div>

            <div class="mt-8 grid gap-5 md:grid-cols-2">
                <article
                    v-for="barber in barbers"
                    :key="barber.id"
                    class="overflow-hidden rounded-[1.25rem] border border-stone-700 bg-stone-900"
                >
                    <div class="flex items-center gap-4 p-5 border-b border-stone-800">
                        <div class="h-12 w-12 rounded-full bg-stone-800 flex items-center justify-center text-amber-500 text-lg font-semibold flex-shrink-0">
                            {{ barber.name.charAt(0) }}
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-white">{{ barber.name }}</h3>
                            <div class="mt-0.5 text-xs text-stone-400">Barber</div>
                        </div>
                    </div>

                    <div class="p-5">
                        <p class="text-xs leading-6 text-stone-400 mb-4">{{ barber.bio }}</p>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="specialty in barber.specialties"
                                :key="specialty"
                                class="rounded-full border border-stone-700 bg-stone-950 px-3 py-1 text-xs text-stone-300"
                            >
                                {{ specialty }}
                            </span>
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            <div class="flex text-amber-500">
                                <svg v-for="i in 5" :key="i" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            </div>
                            <span class="text-xs text-stone-500">{{ barber.review_count }} reviews</span>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>
</template>