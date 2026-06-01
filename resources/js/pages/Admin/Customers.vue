<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminLayout from '@/layouts/adminLayout.vue';

const props = defineProps<{
    customers: {
        data: Array<{
            id: string;
            name: string;
            email: string;
            phone: string;
            created_at: string;
            appointments_count: number;
        }>;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
}>();

const search = ref('');

function goToCustomer(id: string) {
    router.get(`/admin/klanten/${id}`);
}

const filtered = ref(props.customers.data);

watch(search, (q) => {
    const term = q.toLowerCase();
    filtered.value = props.customers.data.filter(
        (c) =>
            c.name.toLowerCase().includes(term) ||
            c.email.toLowerCase().includes(term) ||
            (c.phone && c.phone.includes(term)),
    );
});
</script>

<template>
    <AdminLayout title="Klanten">
        <Head title="Admin — Klanten" />

        <div
            class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="text-xs text-stone-500">
                {{ filtered.length }} klanten
            </div>
            <input
                v-model="search"
                type="text"
                placeholder="Zoek op naam of email..."
                class="w-full rounded-lg border border-stone-700 bg-stone-900 px-3 py-2 text-xs text-white placeholder-stone-500 focus:border-stone-500 focus:outline-none sm:w-64"
            />
        </div>

        <div
            class="overflow-hidden rounded-[1.25rem] border border-stone-700 bg-stone-900"
        >
            <div class="overflow-x-auto">
                <div class="min-w-[760px]">
                    <div
                        class="grid grid-cols-12 border-b border-stone-800 px-5 py-3 text-[11px] font-medium tracking-wider text-stone-500 uppercase"
                    >
                        <div class="col-span-3">Naam</div>
                        <div class="col-span-4">Email</div>
                        <div class="col-span-2">Telefoon</div>
                        <div class="col-span-2">Afspraken</div>
                        <div class="col-span-1" />
                    </div>
                    <div
                        v-if="filtered.length === 0"
                        class="px-5 py-8 text-center text-xs text-stone-500"
                    >
                        Geen klanten gevonden.
                    </div>
                    <div
                        v-for="c in filtered"
                        :key="c.id"
                        class="grid cursor-pointer grid-cols-12 items-center border-b border-stone-800 px-5 py-3.5 transition-colors hover:bg-white/5"
                        @click="goToCustomer(c.id)"
                    >
                        <div class="col-span-3 text-xs font-medium text-white">
                            {{ c.name }}
                        </div>
                        <div
                            class="col-span-4 truncate pr-3 text-xs text-stone-400"
                        >
                            {{ c.email }}
                        </div>
                        <div class="col-span-2 text-xs text-stone-400">
                            {{ c.phone || '-' }}
                        </div>
                        <div class="col-span-2 text-xs text-stone-400">
                            {{ c.appointments_count }}
                        </div>
                        <div class="col-span-1 text-right">
                            <span class="text-xs text-amber-500 hover:underline"
                                >Details →</span
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-if="customers.links.length > 3"
            class="mt-5 flex flex-wrap items-center gap-2"
        >
            <template v-for="link in customers.links" :key="link.label">
                <button
                    v-if="link.url"
                    @click="router.get(link.url)"
                    class="rounded-md border px-3 py-1.5 text-[11px] transition-colors"
                    :class="
                        link.active
                            ? 'border-amber-500/30 bg-amber-500/10 text-amber-400'
                            : 'border-stone-700 text-stone-400 hover:border-stone-500 hover:text-white'
                    "
                    v-html="link.label"
                />
                <span
                    v-else
                    class="px-3 py-1.5 text-[11px] text-stone-600"
                    v-html="link.label"
                />
            </template>
        </div>
    </AdminLayout>
</template>
