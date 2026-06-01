<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/layouts/adminLayout.vue';

const props = defineProps<{
    barbers: Array<Record<string, any>>;
    allServices: Array<{ id: string; name: string }>;
}>();

const dayNames = [
    'Zondag',
    'Maandag',
    'Dinsdag',
    'Woensdag',
    'Donderdag',
    'Vrijdag',
    'Zaterdag',
];

const editingBarber = ref<Record<string, any> | null>(null);
const scheduleForm = ref<
    Array<{
        day_of_week: number;
        start_time: string;
        end_time: string;
        active: boolean;
    }>
>([]);

function openSchedule(b: Record<string, any>) {
    editingBarber.value = b;
    scheduleForm.value = [1, 2, 3, 4, 5, 6, 0].map((day) => {
        const existing = (b.availability as any[]).find(
            (a: any) => a.day_of_week === day,
        );

        return {
            day_of_week: day,
            start_time: existing?.start_time ?? '10:00',
            end_time: existing?.end_time ?? '18:00',
            active: existing?.active ?? false,
        };
    });
}

function saveSchedule() {
    if (!editingBarber.value) {
        return;
    }

    router.patch(
        `/admin/barbers/${editingBarber.value.id}/beschikbaarheid`,
        { availability: scheduleForm.value },
        {
            onSuccess: () => {
                editingBarber.value = null;
            },
            preserveScroll: true,
        },
    );
}

const profileBarber = ref<Record<string, any> | null>(null);
const profileForm = ref<{
    bio: string;
    avatar: string;
    active: boolean;
    sort_order: number;
    specialties: string[];
    newSpecialty: string;
}>({
    bio: '',
    avatar: '',
    active: true,
    sort_order: 0,
    specialties: [],
    newSpecialty: '',
});

function openProfile(b: Record<string, any>) {
    profileBarber.value = b;
    profileForm.value = {
        bio: b.bio ?? '',
        avatar: b.avatar ?? '',
        active: b.active ?? true,
        sort_order: b.sort_order ?? 0,
        specialties: [...(b.specialties ?? [])],
        newSpecialty: '',
    };
}

function addSpecialty() {
    const s = profileForm.value.newSpecialty.trim();

    if (s && !profileForm.value.specialties.includes(s)) {
        profileForm.value.specialties.push(s);
    }

    profileForm.value.newSpecialty = '';
}

function removeSpecialty(idx: number) {
    profileForm.value.specialties.splice(idx, 1);
}

function saveProfile() {
    if (!profileBarber.value) {
        return;
    }

    router.patch(
        `/admin/barbers/${profileBarber.value.id}`,
        {
            bio: profileForm.value.bio,
            avatar: profileForm.value.avatar,
            active: profileForm.value.active,
            sort_order: profileForm.value.sort_order,
            specialties: profileForm.value.specialties,
        },
        {
            onSuccess: () => {
                profileBarber.value = null;
            },
            preserveScroll: true,
        },
    );
}

const servicesBarber = ref<Record<string, any> | null>(null);
const selectedServiceIds = ref<string[]>([]);

function openServices(b: Record<string, any>) {
    servicesBarber.value = b;
    selectedServiceIds.value = [...(b.service_ids ?? [])];
}

function saveServices() {
    if (!servicesBarber.value) {
        return;
    }

    router.patch(
        `/admin/barbers/${servicesBarber.value.id}/diensten`,
        {
            service_ids: selectedServiceIds.value,
        },
        {
            onSuccess: () => {
                servicesBarber.value = null;
            },
            preserveScroll: true,
        },
    );
}

function toggleService(id: string) {
    const idx = selectedServiceIds.value.indexOf(id);

    if (idx >= 0) {
        selectedServiceIds.value.splice(idx, 1);
    } else {
        selectedServiceIds.value.push(id);
    }
}
</script>

<template>
    <AdminLayout title="Barbers">
        <Head title="Admin — Barbers" />

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div
                v-for="b in barbers"
                :key="b.id"
                class="overflow-hidden rounded-[1.25rem] border border-stone-700 bg-stone-900"
            >
                <div
                    class="flex items-center gap-4 border-b border-stone-800 p-5"
                >
                    <div
                        class="flex h-14 w-14 flex-shrink-0 items-center justify-center overflow-hidden rounded-full bg-stone-800 text-xl font-semibold text-amber-500"
                    >
                        <img
                            v-if="b.avatar"
                            :src="b.avatar"
                            :alt="b.name"
                            class="h-full w-full object-cover"
                        />
                        <span v-else>{{ b.name.charAt(0) }}</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <div class="text-sm font-medium text-white">
                                {{ b.name }}
                            </div>
                            <span
                                v-if="!b.active"
                                class="rounded-full bg-red-500/10 px-2 py-0.5 text-[10px] font-medium text-red-300 ring-1 ring-red-500/20"
                                >Inactief</span
                            >
                        </div>
                        <div class="mt-0.5 text-xs text-stone-400">
                            {{ b.email }}
                        </div>
                    </div>
                    <div class="text-xs text-stone-500">
                        #{{ b.sort_order }}
                    </div>
                </div>

                <div class="p-5">
                    <p class="mb-4 text-xs leading-6 text-stone-400">
                        {{ b.bio || 'Geen bio ingesteld.' }}
                    </p>
                    <div class="mb-5 flex flex-wrap gap-1.5">
                        <span
                            v-for="spec in b.specialties"
                            :key="spec"
                            class="rounded-full border border-stone-700 bg-stone-950 px-2.5 py-0.5 text-[11px] text-stone-300"
                            >{{ spec }}</span
                        >
                        <span
                            v-if="!b.specialties?.length"
                            class="text-[11px] text-stone-500"
                            >Geen specialiteiten</span
                        >
                    </div>

                    <div
                        class="mb-2 text-xs tracking-widest text-stone-500 uppercase"
                    >
                        Diensten
                    </div>
                    <div class="mb-5 flex flex-wrap gap-1.5">
                        <span
                            v-for="svc in b.services"
                            :key="svc"
                            class="rounded-full border border-stone-700/60 bg-stone-800 px-2.5 py-0.5 text-[11px] text-stone-300"
                            >{{ svc }}</span
                        >
                        <span
                            v-if="!b.services?.length"
                            class="text-[11px] text-stone-500"
                            >Geen diensten gekoppeld</span
                        >
                    </div>

                    <div
                        class="mb-2 text-xs tracking-widest text-stone-500 uppercase"
                    >
                        Werkrooster
                    </div>
                    <div class="mb-5 space-y-1.5">
                        <div
                            v-for="slot in b.availability"
                            :key="slot.day_of_week"
                            class="flex items-center justify-between"
                        >
                            <div class="flex items-center gap-2">
                                <div
                                    class="h-2 w-2 flex-shrink-0 rounded-full"
                                    :class="
                                        slot.active
                                            ? 'bg-emerald-400'
                                            : 'bg-stone-600'
                                    "
                                />
                                <span class="w-20 text-xs text-stone-400">{{
                                    slot.day_name
                                }}</span>
                            </div>
                            <span
                                v-if="slot.active"
                                class="text-xs text-stone-300"
                                >{{ slot.start_time }} –
                                {{ slot.end_time }}</span
                            >
                            <span v-else class="text-xs text-stone-500"
                                >Vrij</span
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                        <button
                            @click="openProfile(b)"
                            class="rounded-md border border-stone-700 bg-stone-950 px-3 py-2 text-[11px] text-white transition-colors hover:border-stone-500 hover:bg-white/5"
                        >
                            Profiel bewerken
                        </button>
                        <button
                            @click="openServices(b)"
                            class="rounded-md border border-stone-700 bg-stone-950 px-3 py-2 text-[11px] text-white transition-colors hover:border-stone-500 hover:bg-white/5"
                        >
                            Diensten
                        </button>
                        <button
                            @click="openSchedule(b)"
                            class="rounded-md border border-stone-700 bg-stone-950 px-3 py-2 text-[11px] text-white transition-colors hover:border-stone-500 hover:bg-white/5"
                        >
                            Rooster
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-if="editingBarber"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
        >
            <div
                class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-[1.5rem] border border-stone-700 bg-stone-950 p-6 shadow-[0_30px_100px_rgba(0,0,0,0.4)]"
            >
                <h2 class="mb-5 text-sm font-medium text-white">
                    Rooster — {{ editingBarber.name }}
                </h2>
                <form @submit.prevent="saveSchedule" class="space-y-3">
                    <div
                        v-for="day in scheduleForm"
                        :key="day.day_of_week"
                        class="flex flex-wrap items-center gap-2 border-b border-stone-800 py-2"
                    >
                        <div class="flex w-24 flex-shrink-0 items-center gap-2">
                            <input
                                v-model="day.active"
                                type="checkbox"
                                :id="`day-${day.day_of_week}`"
                                class="rounded border-stone-600 bg-stone-900 text-amber-500 focus:ring-amber-500"
                            />
                            <label
                                :for="`day-${day.day_of_week}`"
                                class="cursor-pointer text-xs text-stone-300"
                                >{{ dayNames[day.day_of_week] }}</label
                            >
                        </div>
                        <template v-if="day.active">
                            <input
                                v-model="day.start_time"
                                type="time"
                                class="min-w-[120px] flex-1 rounded border border-stone-700 bg-stone-900 px-2 py-1 text-xs text-white focus:border-stone-500 focus:outline-none"
                            />
                            <span class="text-xs text-stone-500">–</span>
                            <input
                                v-model="day.end_time"
                                type="time"
                                class="min-w-[120px] flex-1 rounded border border-stone-700 bg-stone-900 px-2 py-1 text-xs text-white focus:border-stone-500 focus:outline-none"
                            />
                        </template>
                        <span v-else class="flex-1 text-xs text-stone-500"
                            >Vrij</span
                        >
                    </div>
                    <div class="flex gap-2 pt-3">
                        <button
                            type="submit"
                            class="flex-1 rounded-md border border-stone-600 bg-stone-900 py-2.5 text-xs text-white transition-colors hover:bg-stone-800"
                        >
                            Opslaan
                        </button>
                        <button
                            type="button"
                            @click="editingBarber = null"
                            class="flex-1 rounded-md border border-stone-700 py-2.5 text-xs text-stone-400 transition-colors hover:border-stone-500 hover:text-white"
                        >
                            Annuleren
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div
            v-if="profileBarber"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
        >
            <div
                class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-[1.5rem] border border-stone-700 bg-stone-950 p-6 shadow-[0_30px_100px_rgba(0,0,0,0.4)]"
            >
                <h2 class="mb-5 text-sm font-medium text-white">
                    Profiel — {{ profileBarber.name }}
                </h2>
                <form @submit.prevent="saveProfile" class="space-y-4">
                    <div>
                        <label
                            class="mb-1 block text-xs tracking-wide text-stone-500 uppercase"
                            >Bio</label
                        >
                        <textarea
                            v-model="profileForm.bio"
                            rows="3"
                            class="w-full resize-none rounded-lg border border-stone-700 bg-stone-900 px-3 py-2.5 text-sm text-white focus:border-stone-500 focus:outline-none"
                        />
                    </div>
                    <div>
                        <label
                            class="mb-1 block text-xs tracking-wide text-stone-500 uppercase"
                            >Avatar URL</label
                        >
                        <input
                            v-model="profileForm.avatar"
                            type="url"
                            placeholder="https://..."
                            class="w-full rounded-lg border border-stone-700 bg-stone-900 px-3 py-2 text-sm text-white focus:border-stone-500 focus:outline-none"
                        />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label
                                class="mb-1 block text-xs tracking-wide text-stone-500 uppercase"
                                >Sort order</label
                            >
                            <input
                                v-model.number="profileForm.sort_order"
                                type="number"
                                min="0"
                                class="w-full rounded-lg border border-stone-700 bg-stone-900 px-3 py-2 text-sm text-white focus:border-stone-500 focus:outline-none"
                            />
                        </div>
                        <div class="flex items-end pb-1">
                            <label
                                class="flex cursor-pointer items-center gap-2"
                            >
                                <input
                                    v-model="profileForm.active"
                                    type="checkbox"
                                    class="rounded border-stone-600 bg-stone-900 text-amber-500 focus:ring-amber-500"
                                />
                                <span class="text-xs text-stone-300"
                                    >Actief</span
                                >
                            </label>
                        </div>
                    </div>
                    <div>
                        <label
                            class="mb-1 block text-xs tracking-wide text-stone-500 uppercase"
                            >Specialiteiten</label
                        >
                        <div class="mb-2 flex flex-wrap gap-1.5">
                            <span
                                v-for="(spec, idx) in profileForm.specialties"
                                :key="spec"
                                class="inline-flex items-center gap-1 rounded-full border border-stone-700 bg-stone-900 px-2.5 py-1 text-[11px] text-stone-300"
                            >
                                {{ spec
                                }}<button
                                    type="button"
                                    @click="removeSpecialty(idx)"
                                    class="text-stone-500 hover:text-red-400"
                                >
                                    ×
                                </button>
                            </span>
                        </div>
                        <div class="flex gap-2">
                            <input
                                v-model="profileForm.newSpecialty"
                                type="text"
                                placeholder="Nieuwe specialiteit..."
                                class="flex-1 rounded-lg border border-stone-700 bg-stone-900 px-3 py-2 text-sm text-white focus:border-stone-500 focus:outline-none"
                                @keydown.enter.prevent="addSpecialty"
                            />
                            <button
                                type="button"
                                @click="addSpecialty"
                                class="rounded-md border border-stone-600 bg-stone-900 px-3 py-2 text-xs text-white transition-colors hover:bg-stone-800"
                            >
                                +
                            </button>
                        </div>
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button
                            type="submit"
                            class="flex-1 rounded-md border border-stone-600 bg-stone-900 py-2.5 text-xs text-white transition-colors hover:bg-stone-800"
                        >
                            Opslaan
                        </button>
                        <button
                            type="button"
                            @click="profileBarber = null"
                            class="flex-1 rounded-md border border-stone-700 py-2.5 text-xs text-stone-400 transition-colors hover:border-stone-500 hover:text-white"
                        >
                            Annuleren
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div
            v-if="servicesBarber"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
        >
            <div
                class="max-h-[90vh] w-full max-w-sm overflow-y-auto rounded-[1.5rem] border border-stone-700 bg-stone-950 p-6 shadow-[0_30px_100px_rgba(0,0,0,0.4)]"
            >
                <h2 class="mb-5 text-sm font-medium text-white">
                    Diensten — {{ servicesBarber.name }}
                </h2>
                <div class="mb-6 space-y-2">
                    <label
                        v-for="svc in allServices"
                        :key="svc.id"
                        class="flex cursor-pointer items-center gap-3 rounded-lg border border-stone-800 bg-stone-900 px-4 py-3 transition-colors hover:border-stone-600"
                        :class="
                            selectedServiceIds.includes(svc.id)
                                ? 'border-amber-500/30 bg-amber-500/5'
                                : ''
                        "
                    >
                        <input
                            type="checkbox"
                            :checked="selectedServiceIds.includes(svc.id)"
                            @change="toggleService(svc.id)"
                            class="rounded border-stone-600 bg-stone-900 text-amber-500 focus:ring-amber-500"
                        />
                        <span class="text-xs text-stone-300">{{
                            svc.name
                        }}</span>
                    </label>
                </div>
                <div class="flex gap-2">
                    <button
                        @click="saveServices"
                        class="flex-1 rounded-md border border-stone-600 bg-stone-900 py-2.5 text-xs text-white transition-colors hover:bg-stone-800"
                    >
                        Opslaan
                    </button>
                    <button
                        @click="servicesBarber = null"
                        class="flex-1 rounded-md border border-stone-700 py-2.5 text-xs text-stone-400 transition-colors hover:border-stone-500 hover:text-white"
                    >
                        Annuleren
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
