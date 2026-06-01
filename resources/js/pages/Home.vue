<template>
    <Head title="Dawara Barbershop — Leeuwarden" />

    <div class="bg-stone-950 text-stone-100">
        <LandingHero />
        <LandingServices :services="services" />
        <LandingBooking />
        <LandingBarbers :barbers="barbers" />
        <LandingReviews :reviews="reviews" />
        <LandingCta />
        <LandingContact />
    </div>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import LandingBarbers from '@/components/landing/LandingBarbers.vue';
import LandingBooking from '@/components/landing/LandingBooking.vue';
import LandingContact from '@/components/landing/LandingContact.vue';
import LandingCta from '@/components/landing/LandingCta.vue';
import LandingHero from '@/components/landing/LandingHero.vue';
import LandingReviews from '@/components/landing/LandingReviews.vue';
import LandingServices from '@/components/landing/LandingServices.vue';

type ServiceCard = {
    id: number;
    name: string;
    description: string;
    price_formatted: string;
    duration_label: string;
    color: string;
};

type BarberCard = {
    id: number;
    name: string;
    bio: string;
    specialties: string[];
    average_rating: number;
    review_count: number;
};

type ReviewCard = {
    id: number;
    rating: number;
    comment: string;
    customer_name: string;
    barber_name: string;
    source: string;
    reviewed_at: string;
};

defineProps<{
    services: ServiceCard[];
    barbers: BarberCard[];
    reviews: ReviewCard[];
}>();
</script>
