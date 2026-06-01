<template>
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <SiteHeader />

        <main class="flex-1">
            <slot />
        </main>

        <SiteFooter />
    </div>
</template>

<script setup lang="ts">
import SiteFooter from '@/components/SiteFooter.vue';
import SiteHeader from '@/components/SiteHeader.vue';
</script>
