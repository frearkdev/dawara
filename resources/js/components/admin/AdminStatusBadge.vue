<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    status: string;
}>();

const cls = computed(() => {
    const map: Record<string, string> = {
        confirmed: 'bg-emerald-500/10 text-emerald-300 ring-1 ring-emerald-500/20',
        pending:   'bg-amber-500/10 text-amber-300 ring-1 ring-amber-500/20',
        completed: 'bg-stone-700/60 text-stone-200 ring-1 ring-stone-600',
        cancelled: 'bg-red-500/10 text-red-300 ring-1 ring-red-500/20',
        no_show:   'bg-orange-500/10 text-orange-300 ring-1 ring-orange-500/20',
    };

    return map[props.status] ?? 'bg-stone-700/60 text-stone-300 ring-1 ring-stone-600';
});

const label = computed(() => {
    const map: Record<string, string> = {
        confirmed: 'Bevestigd',
        pending: 'Pending',
        completed: 'Klaar',
        cancelled: 'Geannuleerd',
        no_show: 'No-show',
    };

    return map[props.status] ?? props.status;
});
</script>

<template>
    <span class="rounded-full px-2.5 py-1 text-xs font-medium" :class="cls">
        {{ label }}
    </span>
</template>