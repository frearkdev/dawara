<script setup lang="ts">
type ReviewCard = {
    id: number;
    rating: number;
    comment: string;
    customer_name: string;
    barber_name: string;
    source: string;
    reviewed_at: string;
};

defineProps<{
    reviews: ReviewCard[];
}>();
</script>

<template>
    <section id="reviews" class="bg-stone-950 py-16 md:py-20">
        <div class="mx-auto max-w-7xl px-6">
            <div class="mb-8 max-w-2xl">
                <div
                    class="text-sm font-medium tracking-widest text-amber-500 uppercase"
                >
                    Wat klanten zeggen
                </div>
                <h2 class="mt-3 text-2xl font-semibold text-white">Reviews</h2>
                <p class="mt-2 text-sm leading-relaxed text-stone-400">
                    Geverifieerd via Google & ons eigen systeem. Alle reviews
                    zijn echt.
                </p>
            </div>

            <div v-if="reviews.length" class="relative">
                <!-- Horizontal scroll container -->
                <div
                    class="scrollbar-hide -mx-6 flex snap-x snap-mandatory gap-4 overflow-x-auto px-6 pb-4"
                >
                    <article
                        v-for="review in reviews"
                        :key="review.id"
                        class="w-[85vw] max-w-[320px] shrink-0 snap-start rounded-[1rem] border border-stone-700 bg-stone-900 p-5 transition-transform hover:border-stone-600"
                    >
                        <div
                            class="flex items-center gap-1 text-sm text-amber-500"
                        >
                            <span
                                v-for="n in 5"
                                :key="n"
                                :class="
                                    n <= review.rating ? '' : 'text-stone-600'
                                "
                                >★</span
                            >
                        </div>
                        <p
                            class="mt-4 line-clamp-6 text-sm leading-7 text-stone-300"
                        >
                            {{ review.comment }}
                        </p>
                        <div class="mt-6 flex items-center justify-between">
                            <div>
                                <div class="text-sm font-semibold text-white">
                                    {{ review.customer_name }}
                                </div>
                                <div
                                    v-if="review.barber_name"
                                    class="mt-0.5 text-xs text-stone-500"
                                >
                                    Door {{ review.barber_name }}
                                </div>
                            </div>
                            <div
                                class="text-[11px] tracking-wider text-stone-500 uppercase"
                            >
                                {{
                                    review.source === 'google'
                                        ? 'Google'
                                        : 'Dawara'
                                }}
                            </div>
                        </div>
                        <div
                            v-if="review.reviewed_at"
                            class="mt-2 text-[11px] text-stone-600"
                        >
                            {{ review.reviewed_at }}
                        </div>
                    </article>
                </div>

                <!-- Scroll hint -->
                <div
                    class="mt-2 text-center text-[11px] text-stone-600 md:hidden"
                >
                    Swipe voor meer reviews →
                </div>
            </div>

            <p v-else class="text-sm text-stone-500">Nog geen reviews.</p>
        </div>
    </section>
</template>

<style scoped>
/* Hide scrollbar for Chrome/Safari/Opera */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
/* Hide scrollbar for IE/Edge/Firefox */
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
