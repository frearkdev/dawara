<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const mobileMenuOpen = ref(false);

const isStaff = computed(() => {
    const role = page.props.auth?.user?.role;

    return role === 'admin' || role === 'barber';
});

function closeMobileMenu(): void {
    mobileMenuOpen.value = false;
}
</script>

<template>
    <header
        class="sticky top-0 z-50 border-b border-stone-700/60 bg-stone-950/90 backdrop-blur-xl"
    >
        <div
            class="section-shell flex h-14 items-center justify-between gap-3 md:h-20"
        >
            <Link
                href="/"
                class="flex items-center gap-3 text-xs font-semibold tracking-[0.3em] text-white sm:text-sm sm:tracking-[0.35em]"
            >
                <span>DAWARA<span class="text-amber-500">.</span></span>
            </Link>

            <nav class="hidden items-center gap-8 lg:flex">
                <a
                    href="/#diensten"
                    class="text-xs tracking-[0.28em] text-stone-400 uppercase transition-colors hover:text-white"
                    >Diensten</a
                >
                <a
                    href="/#team"
                    class="text-xs tracking-[0.28em] text-stone-400 uppercase transition-colors hover:text-white"
                    >Team</a
                >
                <a
                    href="/#reviews"
                    class="text-xs tracking-[0.28em] text-stone-400 uppercase transition-colors hover:text-white"
                    >Reviews</a
                >
                <a
                    href="/#contact"
                    class="text-xs tracking-[0.28em] text-stone-400 uppercase transition-colors hover:text-white"
                    >Contact</a
                >
            </nav>

            <div class="flex items-center gap-2 md:gap-3">
                <!--                <template v-if="page.props.auth?.user">-->
                <!--                    <Link v-if="isStaff" href="/admin" class="hidden text-xs uppercase tracking-[0.28em] text-stone-400 transition-colors hover:text-white sm:inline-flex">Dashboard</Link>-->
                <!--                    <Link href="/logout" method="post" as="button" class="hidden text-xs uppercase tracking-[0.28em] text-stone-400 transition-colors hover:text-white sm:inline-flex">Uitloggen</Link>-->
                <!--                </template>-->
                <!--                <template v-else>-->
                <!--                    <Link href="/login" class="hidden text-xs uppercase tracking-[0.28em] text-stone-400 transition-colors hover:text-white sm:inline-flex">Inloggen</Link>-->
                <!--                </template>-->
                <Link
                    href="/boeken"
                    class="inline-flex items-center rounded-md border border-stone-600 px-3 py-2 text-[10px] font-semibold tracking-[0.18em] text-white uppercase transition-colors hover:border-stone-400 hover:bg-white/5 sm:px-4 sm:text-[11px] md:px-5 md:py-2.5 md:text-xs md:tracking-[0.24em]"
                >
                    Boeken
                </Link>

                <button
                    type="button"
                    class="inline-flex size-10 items-center justify-center rounded-md border border-stone-700 text-stone-200 transition-colors hover:border-stone-500 hover:bg-white/5 lg:hidden"
                    :aria-expanded="mobileMenuOpen"
                    aria-label="Open menu"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                >
                    <span class="sr-only">Open menu</span>
                    <svg
                        v-if="!mobileMenuOpen"
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
                    <svg
                        v-else
                        viewBox="0 0 24 24"
                        class="size-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    >
                        <path d="M6 6l12 12" />
                        <path d="M18 6 6 18" />
                    </svg>
                </button>
            </div>
        </div>
        <div
            v-if="mobileMenuOpen"
            class="border-t border-stone-700/60 bg-stone-950 lg:hidden"
        >
            <div class="section-shell py-4">
                <nav class="grid gap-2 text-sm">
                    <a
                        href="/#diensten"
                        class="rounded-lg border border-stone-800 px-4 py-3 text-stone-300 transition-colors hover:border-stone-600 hover:bg-white/5"
                        @click="closeMobileMenu"
                        >Diensten</a
                    >
                    <a
                        href="/#team"
                        class="rounded-lg border border-stone-800 px-4 py-3 text-stone-300 transition-colors hover:border-stone-600 hover:bg-white/5"
                        @click="closeMobileMenu"
                        >Team</a
                    >
                    <a
                        href="/#reviews"
                        class="rounded-lg border border-stone-800 px-4 py-3 text-stone-300 transition-colors hover:border-stone-600 hover:bg-white/5"
                        @click="closeMobileMenu"
                        >Reviews</a
                    >
                    <a
                        href="/#contact"
                        class="rounded-lg border border-stone-800 px-4 py-3 text-stone-300 transition-colors hover:border-stone-600 hover:bg-white/5"
                        @click="closeMobileMenu"
                        >Contact</a
                    >

                    <!--                    <template v-if="page.props.auth?.user">-->
                    <!--                        <Link v-if="isStaff" href="/admin" class="rounded-lg border border-stone-800 px-4 py-3 text-stone-300 transition-colors hover:border-stone-600 hover:bg-white/5" @click="closeMobileMenu">Dashboard</Link>-->
                    <!--                        <Link href="/logout" method="post" as="button" class="rounded-lg border border-stone-800 px-4 py-3 text-left text-stone-300 transition-colors hover:border-stone-600 hover:bg-white/5" @click="closeMobileMenu">Uitloggen</Link>-->
                    <!--                    </template>-->
                    <!--                    <template v-else>-->
                    <!--                        <Link href="/login" class="rounded-lg border border-stone-800 px-4 py-3 text-stone-300 transition-colors hover:border-stone-600 hover:bg-white/5" @click="closeMobileMenu">Inloggen</Link>-->
                    <!--                    </template>-->
                </nav>
            </div>
        </div>
    </header>
</template>
