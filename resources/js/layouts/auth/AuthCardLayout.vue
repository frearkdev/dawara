<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative flex min-h-svh flex-col overflow-hidden bg-stone-950 text-stone-100">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(184,134,11,0.12),_transparent_36%),linear-gradient(180deg,rgba(15,15,15,0.96),rgba(10,10,10,1))]"></div>
        <div class="relative flex min-h-svh items-center justify-center p-6 md:p-10">
            <div class="flex w-full max-w-md flex-col gap-6">
                <Link :href="home()" class="flex items-center gap-2 self-center font-medium text-white">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/10">
                        <AppLogoIcon class="size-9 fill-current text-white" />
                    </div>
                </Link>

                <Card class="rounded-[1.5rem] border-stone-700 bg-stone-900/95 text-stone-100 shadow-[0_30px_100px_rgba(0,0,0,0.35)] backdrop-blur">
                    <CardHeader class="px-8 pt-8 pb-0 text-center">
                        <div class="section-kicker text-amber-500">Dawara Barbershop</div>
                        <CardTitle class="mt-3 text-2xl text-white">{{ title }}</CardTitle>
                        <CardDescription class="text-stone-400">
                            {{ description }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="px-8 py-8">
                        <slot />
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
