<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    href: string;
    active?: boolean;
}>();
</script>

<template>
    <Link
        :href="href"
        class="flex items-center gap-2 rounded-lg px-4 py-3 text-xs uppercase tracking-[0.24em] transition-colors whitespace-nowrap"
        :class="active
            ? 'bg-white/10 text-white ring-1 ring-white/10'
            : 'text-stone-400 hover:bg-white/5 hover:text-white'"
    >
        <slot />
    </Link>
</template>
