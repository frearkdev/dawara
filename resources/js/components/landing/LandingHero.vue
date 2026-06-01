<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <section
        class="section-shell grid min-h-[560px] gap-0 border-b border-stone-700/60 py-0 lg:grid-cols-2"
    >
        <div
            class="flex flex-col justify-center px-0 py-14 text-center lg:border-r lg:border-stone-700/60 lg:py-20 lg:text-left"
        >
            <div class="section-kicker text-amber-500">Leeuwarden's finest</div>
            <h1
                class="lg:max-w-1.5xl mx-auto mt-4 max-w-2xl text-3xl leading-[1.15] font-medium tracking-tight text-white sm:text-4xl md:text-5xl lg:mx-0"
            >
                Alles wat je nodig hebt
                <span class="block text-stone-400"
                    >om je haar en huid te verzorgen.</span
                >
            </h1>
            <p
                class="mx-auto mt-5 max-w-md text-sm leading-7 text-stone-400 md:text-[15px] lg:mx-0 lg:max-w-sm"
            >
                Bij Dawara Barbershop te Leeuwarden krijgt u speciale verzorging
                voor uw haar en huid met verschillende soorten kapsels,
                haarstyling en huidverzorging met de nieuwste apparaten en
                technologieën.
            </p>

            <div
                class="mt-8 flex flex-col justify-center gap-3 sm:flex-row lg:justify-start"
            >
                <Link
                    href="/boeken"
                    class="inline-flex items-center justify-center rounded-md border border-stone-600 bg-white px-6 py-3 text-[11px] font-semibold tracking-[0.12em] text-stone-950 uppercase transition-colors hover:bg-stone-200"
                >
                    Afspraak maken
                </Link>
                <a
                    href="#diensten"
                    class="inline-flex items-center justify-center rounded-md border border-stone-600 px-5 py-3 text-[11px] font-medium tracking-[0.12em] text-white uppercase transition-colors hover:border-stone-400 hover:bg-white/5"
                >
                    Bekijk diensten
                </a>
            </div>
        </div>

        <div
            class="relative min-h-[22rem] overflow-hidden bg-stone-950 text-white sm:min-h-[26rem] lg:min-h-full"
        >
            <img
                src="/landing-media/barber-portrait.png"
                alt="Dawara Barbershop interieur"
                class="absolute inset-0 h-full w-full object-cover opacity-70"
            />
            <div
                class="absolute inset-0 bg-[linear-gradient(180deg,rgba(15,15,15,0.2),rgba(15,15,15,0.85))]"
            ></div>
            <div class="absolute right-6 bottom-6 left-6">
                <div class="flex items-end justify-between">
                    <div>
                        <div
                            class="text-[10px] tracking-[0.15em] text-stone-400 uppercase"
                        >
                            Tevreden klanten
                        </div>
                        <div class="mt-1 text-3xl font-semibold text-white">
                            1.000+
                        </div>
                    </div>
                    <div class="flex items-center gap-1 text-amber-500">
                        <svg
                            v-for="i in 5"
                            :key="i"
                            xmlns="http://www.w3.org/2000/svg"
                            width="14"
                            height="14"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            stroke="none"
                        >
                            <path
                                d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
                            />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
