<script setup>
import PageHeaderSection from '@/components/PageHeaderSection.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';

// Your logic goes here
const page = usePage();
const pageSectionLinks = [
    {
        title: 'Home',
        href: route('home'),
    },
    {
        title: 'Cart',
        href: route('cart'),
    },
    {
        title: 'Error',
        href: '',
    },
];
</script>

<template>
    <Head title="Checkout Error" />

    <GuestLayout>
        <PageHeaderSection :title="'Checkout Error'" :links="pageSectionLinks" />

        <div class="container mx-auto my-10 max-w-3xl px-4">
            <div class="mt-10 text-center">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="mx-auto h-16 w-16 text-red-500"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h2 class="text-xl font-semibold text-red-600">Checkout Failed</h2>
                <p class="text-gray-500">Please try again or contact support if the issue persists.</p>
            </div>

            <div class="mt-6 text-center">
                <button @click="$router.push({ name: 'home' })" class="rounded bg-gray-300 px-4 py-2 text-gray-800 hover:bg-gray-400">
                    Return to Home
                </button>
            </div>
        </div>
    </GuestLayout>
</template>
