<template>
    <AdminLayout title="Afspraken">
        <Head title="Admin — Afspraken" />

        <!-- Filters -->
        <div
            class="mb-5 flex flex-wrap gap-3 rounded-[1.25rem] border border-stone-700 bg-stone-900 p-4"
        >
            <input
                type="date"
                v-model="filterDate"
                class="w-full rounded-lg border border-stone-700 bg-stone-950 px-3 py-2 text-xs text-white focus:border-stone-500 focus:outline-none sm:w-auto"
            />
            <select
                v-model="filterBarber"
                class="w-full rounded-lg border border-stone-700 bg-stone-950 px-3 py-2 text-xs text-white focus:border-stone-500 focus:outline-none sm:w-auto"
            >
                <option value="">Alle barbers</option>
                <option v-for="b in barbers" :key="b.id" :value="b.id">
                    {{ b.name }}
                </option>
            </select>
            <select
                v-model="filterStatus"
                class="w-full rounded-lg border border-stone-700 bg-stone-950 px-3 py-2 text-xs text-white focus:border-stone-500 focus:outline-none sm:w-auto"
            >
                <option value="">Alle statussen</option>
                <option value="confirmed">Bevestigd</option>
                <option value="pending">Pending</option>
                <option value="completed">Klaar</option>
                <option value="cancelled">Geannuleerd</option>
                <option value="no_show">No-show</option>
            </select>
            <button
                @click="applyFilters"
                class="w-full rounded-md border border-stone-700 bg-stone-950 px-4 py-2 text-xs text-white transition-colors hover:border-stone-500 hover:bg-white/5 sm:w-auto"
            >
                Filteren
            </button>
            <button
                @click="resetFilters"
                class="w-full px-2 py-2 text-left text-xs text-stone-500 hover:text-white sm:w-auto sm:py-0 sm:text-center"
            >
                Reset
            </button>
        </div>

        <!-- Tabel -->
        <div
            class="overflow-hidden rounded-[1.5rem] border border-stone-700 bg-stone-900"
        >
            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] text-xs">
                    <thead>
                        <tr class="border-b border-stone-700 bg-stone-950/70">
                            <th
                                class="px-5 py-3 text-left font-medium text-stone-500"
                            >
                                Klant
                            </th>
                            <th
                                class="px-4 py-3 text-left font-medium text-stone-500"
                            >
                                Dienst
                            </th>
                            <th
                                class="px-4 py-3 text-left font-medium text-stone-500"
                            >
                                Barber
                            </th>
                            <th
                                class="px-4 py-3 text-left font-medium text-stone-500"
                            >
                                Datum & tijd
                            </th>
                            <th
                                class="px-4 py-3 text-left font-medium text-stone-500"
                            >
                                Prijs
                            </th>
                            <th
                                class="px-4 py-3 text-left font-medium text-stone-500"
                            >
                                Status
                            </th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-700">
                        <tr
                            v-for="a in appointments.data"
                            :key="a.id"
                            class="transition-colors hover:bg-white/5"
                        >
                            <td class="px-5 py-3.5">
                                <div class="font-medium text-white">
                                    {{ a.customer_name }}
                                </div>
                                <div class="text-stone-500">
                                    {{ a.customer_phone }}
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-stone-300">
                                {{ a.service_name }}
                            </td>
                            <td class="px-4 py-3.5 text-stone-300">
                                {{ a.barber_name }}
                            </td>
                            <td class="px-4 py-3.5 text-stone-300">
                                {{ a.starts_at }}
                            </td>
                            <td class="px-4 py-3.5 text-stone-300">
                                {{ a.price_formatted }}
                            </td>
                            <td class="px-4 py-3.5">
                                <AdminStatusBadge :status="a.status" />
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex flex-wrap gap-1">
                                    <button
                                        v-if="a.status === 'confirmed'"
                                        @click="updateStatus(a.id, 'completed')"
                                        class="rounded border border-emerald-500/20 px-2.5 py-1 text-xs text-emerald-300 transition-colors hover:bg-emerald-500/10"
                                    >
                                        ✓ Klaar
                                    </button>
                                    <button
                                        v-if="a.can_cancel"
                                        @click="updateStatus(a.id, 'cancelled')"
                                        class="rounded border border-red-500/20 px-2.5 py-1 text-xs text-red-300 transition-colors hover:bg-red-500/10"
                                    >
                                        × Annuleer
                                    </button>
                                    <button
                                        v-if="a.status === 'confirmed'"
                                        @click="updateStatus(a.id, 'no_show')"
                                        class="rounded border border-orange-500/20 px-2.5 py-1 text-xs text-orange-300 transition-colors hover:bg-orange-500/10"
                                    >
                                        No-show
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                v-if="appointments.data.length === 0"
                class="py-12 text-center text-xs text-stone-500"
            >
                Geen afspraken gevonden.
            </div>

            <!-- Paginering -->
            <div
                v-if="appointments.last_page > 1"
                class="flex flex-col gap-3 border-t border-stone-700 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <span class="text-xs text-stone-500">
                    {{ appointments.from }}–{{ appointments.to }} van
                    {{ appointments.total }}
                </span>
                <div class="flex flex-wrap gap-1">
                    <a
                        v-for="link in appointments.links"
                        :key="link.label"
                        :href="link.url ?? '#'"
                        v-html="link.label"
                        class="rounded border px-3 py-1.5 text-xs transition-colors"
                        :class="
                            link.active
                                ? 'border-stone-500 bg-white/5 text-white'
                                : 'border-stone-700 text-stone-400 hover:border-stone-500 hover:text-white'
                        "
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminStatusBadge from '@/components/admin/AdminStatusBadge.vue';
import AdminLayout from '@/layouts/adminLayout.vue';

const props = defineProps<{
    appointments: Record<string, any>;
    barbers: Array<Record<string, any>>;
    filters: Record<string, string>;
}>();

const filterDate = ref(props.filters.date ?? '');
const filterBarber = ref(props.filters.barber_id ?? '');
const filterStatus = ref(props.filters.status ?? '');

function applyFilters() {
    router.get(
        '/admin/afspraken',
        {
            date: filterDate.value || undefined,
            barber_id: filterBarber.value || undefined,
            status: filterStatus.value || undefined,
        },
        { preserveState: true },
    );
}

function resetFilters() {
    filterDate.value = '';
    filterBarber.value = '';
    filterStatus.value = '';
    router.get('/admin/afspraken');
}

function updateStatus(id: string, status: string) {
    router.patch(
        `/admin/afspraken/${id}/status`,
        { status },
        { preserveScroll: true },
    );
}
</script>
