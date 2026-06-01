<template>
    <div class="flex min-h-screen bg-stone-950 text-stone-100">
        <aside class="fixed left-0 top-0 z-40 flex min-h-screen w-64 flex-col border-r border-stone-700/60 bg-stone-950">
            <div class="border-b border-stone-700/60 px-6 py-6">
                <Link href="/" class="text-sm font-semibold tracking-[0.35em] text-white">
                    DAWARA<span class="text-amber-500">.</span>
                </Link>
                <div class="mt-1 text-xs uppercase tracking-[0.28em] text-stone-500">
                    Admin dashboard
                </div>
            </div>

            <nav class="flex flex-1 flex-col gap-2 px-4 py-5">
                <AdminNavLink href="/admin" :active="isActive('/admin', true)">Dashboard</AdminNavLink>
                <AdminNavLink href="/admin/afspraken" :active="isActive('/admin/afspraken')">Afspraken</AdminNavLink>
                <AdminNavLink href="/admin/klanten" :active="isActive('/admin/klanten')">Klanten</AdminNavLink>
                <AdminNavLink href="/admin/reviews" :active="isActive('/admin/reviews')">Reviews</AdminNavLink>
                <AdminNavLink href="/admin/diensten" :active="isActive('/admin/diensten')">Diensten</AdminNavLink>
                <AdminNavLink href="/admin/barbers" :active="isActive('/admin/barbers')">Barbers</AdminNavLink>
                <AdminNavLink href="/admin/instellingen" :active="isActive('/admin/instellingen')">Instellingen</AdminNavLink>
            </nav>

            <div class="border-t border-stone-700/60 px-4 py-5">
                <div class="mb-3 truncate px-2 text-xs text-stone-500">
                    {{ userName }}
                </div>
                <Link href="/logout" method="post" as="button" class="w-full rounded-md border border-stone-700 px-3 py-2 text-left text-xs text-stone-300 transition-colors hover:border-stone-500 hover:bg-white/5">
                    Uitloggen
                </Link>
            </div>
        </aside>

        <div class="ml-64 flex flex-1 flex-col">
            <header class="sticky top-0 z-30 flex items-center justify-between border-b border-stone-700/60 bg-stone-950/90 px-8 py-5 backdrop-blur">
                <h1 class="text-sm font-medium tracking-[0.28em] text-white">
                    {{ title }}
                </h1>
                <span class="text-xs uppercase tracking-[0.24em] text-stone-500">{{ currentDate }}</span>
            </header>

            <Transition name="fade">
                <div v-if="flash" class="mx-8 mt-6 rounded-xl border px-4 py-3 text-xs" :class="flashClass">
                    {{ flash }}
                </div>
            </Transition>

            <main class="flex-1 px-8 py-8">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminNavLink from '@/components/admin/AdminNavLink.vue';

defineProps<{ title?: string }>();

const page = usePage();

const currentDate = computed(() =>
    new Date().toLocaleDateString('nl-NL', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
    }),
);

const flash = computed(
    () =>
        (page.props.flash as any)?.success ||
        (page.props.flash as any)?.error ||
        null,
);

const flashClass = computed(() =>
    (page.props.flash as any)?.success
        ? 'bg-green-50 border-green-200 text-green-800'
        : 'bg-red-50 border-red-200 text-red-800',
);

const userName = computed(() => page.props.auth?.user?.name ?? 'Admin');

function isActive(path: string, exact = false): boolean {
    return exact ? page.url === path : page.url.startsWith(path);
}

</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
