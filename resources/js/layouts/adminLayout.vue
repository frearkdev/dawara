<template>
    <div class="flex min-h-screen bg-stone-950 text-stone-100">
        <button
            v-if="mobileNavOpen"
            type="button"
            class="fixed inset-0 z-30 bg-black/55 lg:hidden"
            aria-label="Sluit menu"
            @click="mobileNavOpen = false"
        />

        <aside
            class="fixed inset-y-0 left-0 z-40 flex w-72 max-w-[85vw] flex-col border-r border-stone-700/60 bg-stone-950 transition-transform duration-200 lg:w-64"
            :class="
                mobileNavOpen
                    ? 'translate-x-0'
                    : '-translate-x-full lg:translate-x-0'
            "
        >
            <div class="border-b border-stone-700/60 px-6 py-6">
                <Link
                    href="/"
                    class="text-sm font-semibold tracking-[0.35em] text-white"
                    @click="mobileNavOpen = false"
                >
                    DAWARA<span class="text-amber-500">.</span>
                </Link>
                <div
                    class="mt-1 text-xs tracking-[0.24em] text-stone-500 uppercase"
                >
                    Admin dashboard
                </div>
            </div>

            <nav class="flex flex-1 flex-col gap-2 overflow-y-auto px-4 py-5">
                <AdminNavLink
                    href="/admin"
                    :active="isActive('/admin', true)"
                    @click="mobileNavOpen = false"
                    >Dashboard</AdminNavLink
                >
                <AdminNavLink
                    href="/admin/afspraken"
                    :active="isActive('/admin/afspraken')"
                    @click="mobileNavOpen = false"
                    >Afspraken</AdminNavLink
                >
                <AdminNavLink
                    href="/admin/klanten"
                    :active="isActive('/admin/klanten')"
                    @click="mobileNavOpen = false"
                    >Klanten</AdminNavLink
                >
                <AdminNavLink
                    href="/admin/reviews"
                    :active="isActive('/admin/reviews')"
                    @click="mobileNavOpen = false"
                    >Reviews</AdminNavLink
                >
                <AdminNavLink
                    href="/admin/diensten"
                    :active="isActive('/admin/diensten')"
                    @click="mobileNavOpen = false"
                    >Diensten</AdminNavLink
                >
                <AdminNavLink
                    href="/admin/barbers"
                    :active="isActive('/admin/barbers')"
                    @click="mobileNavOpen = false"
                    >Barbers</AdminNavLink
                >
                <AdminNavLink
                    href="/admin/instellingen"
                    :active="isActive('/admin/instellingen')"
                    @click="mobileNavOpen = false"
                    >Instellingen</AdminNavLink
                >
            </nav>

            <div class="border-t border-stone-700/60 px-4 py-5">
                <div class="mb-3 truncate px-2 text-xs text-stone-500">
                    {{ userName }}
                </div>
                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="w-full rounded-md border border-stone-700 px-3 py-2 text-left text-xs text-stone-300 transition-colors hover:border-stone-500 hover:bg-white/5"
                >
                    Uitloggen
                </Link>
            </div>
        </aside>

        <div class="flex flex-1 flex-col lg:ml-64">
            <header
                class="sticky top-0 z-20 border-b border-stone-700/60 bg-stone-950/90 px-4 py-4 backdrop-blur sm:px-6 lg:px-8 lg:py-5"
            >
                <div class="flex items-center justify-between gap-3">
                    <div class="flex min-w-0 items-center gap-3">
                        <button
                            type="button"
                            class="inline-flex size-9 items-center justify-center rounded-md border border-stone-700 text-stone-200 transition-colors hover:border-stone-500 hover:bg-white/5 lg:hidden"
                            aria-label="Open menu"
                            @click="mobileNavOpen = true"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                class="size-5"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            >
                                <path d="M4 7h16" />
                                <path d="M4 12h16" />
                                <path d="M4 17h16" />
                            </svg>
                        </button>
                        <h1
                            class="truncate text-xs font-medium tracking-[0.2em] text-white uppercase sm:text-sm sm:tracking-[0.28em]"
                        >
                            {{ title }}
                        </h1>
                    </div>
                    <span
                        class="hidden text-xs tracking-[0.24em] text-stone-500 uppercase sm:inline"
                        >{{ currentDate }}</span
                    >
                </div>
            </header>

            <Transition name="fade">
                <div
                    v-if="flash"
                    class="mx-4 mt-4 rounded-xl border px-4 py-3 text-xs sm:mx-6 lg:mx-8 lg:mt-6"
                    :class="flashClass"
                >
                    {{ flash }}
                </div>
            </Transition>

            <main class="flex-1 px-4 py-5 sm:px-6 sm:py-6 lg:px-8 lg:py-8">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AdminNavLink from '@/components/admin/AdminNavLink.vue';

defineProps<{ title?: string }>();

const page = usePage();
const mobileNavOpen = ref(false);

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
