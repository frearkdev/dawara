<script setup lang="ts">
defineProps<{
    label: string;
    value: string;
}>();
</script>

<template>
    <div class="rounded-[1.25rem] border border-stone-700 bg-stone-900 p-5 shadow-[0_20px_80px_rgba(0,0,0,0.12)]">
        <div class="text-2xl font-semibold text-white">{{ value }}</div>
        <div class="mt-1 text-xs uppercase tracking-[0.24em] text-stone-500">{{ label }}</div>
    </div>
</template>