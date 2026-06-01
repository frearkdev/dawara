<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative flex min-h-svh flex-col overflow-hidden bg-stone-950 text-stone-100">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(184,134,11,0.14),_transparent_36%),linear-gradient(180deg,rgba(15,15,15,0.96),rgba(10,10,10,1))]"></div>
        <div class="relative flex min-h-svh items-center justify-center p-6 md:p-10">
            <div class="w-full max-w-4xl overflow-hidden rounded-[2rem] border border-stone-700 bg-stone-900/95 shadow-[0_30px_100px_rgba(0,0,0,0.35)] backdrop-blur">
                <div class="grid md:grid-cols-[1.05fr_0.95fr]">
                    <div class="flex flex-col justify-between border-b border-stone-700 p-8 md:border-b-0 md:border-r md:p-10">
                        <Link :href="home()" class="inline-flex items-center gap-3 text-sm font-semibold tracking-[0.35em] text-white">
                            <span>DAWARA<span class="text-amber-500">.</span></span>
                        </Link>

                        <div class="mt-12 max-w-md">
                            <h1 class="mt-4 text-3xl font-medium tracking-tight text-white md:text-5xl">{{ title }}</h1>
                            <p class="mt-4 text-sm leading-7 text-stone-400">
                                {{ description }}
                            </p>
                        </div>

                        <div class="mt-10">
                        </div>
                    </div>

                    <div class="flex flex-col justify-center p-8 md:p-10">
                        <div class="mb-8 overflow-hidden rounded-[1.5rem] border border-stone-700 bg-stone-950">
                            <img src="/landing-media/barber-portrait.png" alt="Dawara barber" class="h-56 w-full object-cover object-top opacity-90" />
                        </div>
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
