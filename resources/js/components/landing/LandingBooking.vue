<template>
    <section class="section-shell py-8 md:py-12">
        <div class="overflow-hidden rounded-[1.25rem] border border-stone-700 bg-stone-900 p-6 text-stone-100 shadow-[0_20px_80px_rgba(0,0,0,0.22)] md:p-8 lg:grid lg:grid-cols-[0.95fr_1.05fr] lg:gap-10">
            <div>
                <div class="section-kicker text-amber-500">KNIPPEN & STYLEN</div>
                <h2 class="mt-3 text-2xl font-medium tracking-tight text-white sm:text-3xl">Verder dan de basis</h2>
                <p class="mt-4 max-w-xl text-sm leading-7 text-stone-400">
                    Gespecialiseerde herenkapsels, baardverzorging en haarstyling met de nieuwste apparaten en technieken.
                </p>

                <div class="mt-8 space-y-4">
                    <div class="flex items-start gap-3">
                        <span class="mt-2 size-2 rounded-full bg-amber-500"></span>
                        <p class="text-sm leading-7 text-stone-400"><strong class="font-medium text-white">Hooggeschoold en ervaren</strong> — elke afspraak voelt als een persoonlijke sessie in plaats van een snelle knipbeurt.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-2 size-2 rounded-full bg-amber-500"></span>
                        <p class="text-sm leading-7 text-stone-400"><strong class="font-medium text-white">Speciale verzorging</strong> — voor haar én huid, met aandacht voor afwerking en comfort.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-2 size-2 rounded-full bg-amber-500"></span>
                        <p class="text-sm leading-7 text-stone-400"><strong class="font-medium text-white">Strakke planning</strong> — open van maandag tot zaterdag, met donderdag extra ruime avonduren.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-2 size-2 rounded-full bg-amber-500"></span>
                        <p class="text-sm leading-7 text-stone-400"><strong class="font-medium text-white">Bezoek ons</strong> — Wortelhaven 83, 8911 GL Leeuwarden.</p>
                    </div>
                </div>

                <Link href="/boeken" class="mt-8 inline-flex w-full items-center justify-center rounded-md border border-stone-600 px-5 py-3 text-[11px] font-medium uppercase tracking-[0.12em] text-white transition-colors hover:border-stone-400 hover:bg-white/5 sm:w-auto">
                    Afspraak maken
                </Link>
            </div>

            <div class="mt-8 lg:mt-0">
                <div class="grid gap-4 md:grid-cols-[1.15fr_0.85fr]">
                    <img src="/landing-media/pic-4.png" alt="Scheerkwast en mes" class="h-full min-h-56 rounded-[1.25rem] object-cover ring-1 ring-white/10" />
                    <div class="grid gap-4">
                        <img src="/landing-media/pic-3.png" alt="Barber tools" class="h-40 w-full rounded-[1.25rem] object-cover ring-1 ring-white/10" />
                        <div class="rounded-[1.25rem] border border-stone-700 bg-stone-950 p-5">
                            <div class="text-xs uppercase tracking-[0.3em] text-stone-500">openingstijden</div>
                            <div class="mt-4 space-y-2 text-sm text-stone-300">
                                <div>Ma. 10.00 – 18.00</div>
                                <div>Di. 10.00 – 18.00</div>
                                <div>Wo. 10.00 – 18.00</div>
                                <div>Do. 10.00 – 20.00</div>
                                <div>Vr. 10.00 – 18.00</div>
                                <div>Za. 10.00 – 18.00</div>
                                <div>Zo. Gesloten</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>