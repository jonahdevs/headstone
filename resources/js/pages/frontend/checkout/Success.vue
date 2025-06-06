<script setup>
import PageHeaderSection from '@/components/PageHeaderSection.vue';
import { Button } from '@/components/ui/button';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, router } from '@inertiajs/vue3';

// Your logic goes here
const props = defineProps({
    customer: Object,
});

console.log('Customer Data:', props.customer);

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
        title: 'Success',
        href: '',
    },
];

const customer = props.customer.data;
const order = customer.latest_order;
const memorials = order.memorials;
</script>

<template>
    <Head title="Checkout success" />

    <GuestLayout>
        <PageHeaderSection :title="'Checkout Success'" :links="pageSectionLinks" />

        <section class="flex min-h-screen items-center justify-center px-6 py-12 sm:px-12">
            <div class="bg-card text-secondary-foreground w-full max-w-3xl rounded-xl border p-8">
                <!-- Header -->
                <div class="border-b pb-6 text-center">
                    <h1 class="mb-2 text-2xl font-semibold text-green-700 dark:text-green-400">Order Successfully Placed</h1>
                    <p class="text-muted-foreground text-sm">
                        Thank you, {{ customer.name }}. Your memorial order has been received and is being processed with care.
                    </p>
                </div>

                <!-- Order Summary -->
                <div class="text-muted-foreground mt-6 space-y-4 text-sm">
                    <div class="flex justify-between">
                        <span class="font-medium">Order ID:</span>
                        <span>#{{ order.id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">Payment Method:</span>
                        <span>{{ order.payment_method }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">Total Paid:</span>
                        <span class="text-primary">{{ order.total }}</span>
                    </div>
                </div>

                <!-- Headstones List -->
                <div class="mt-8">
                    <h2 class="mb-4 text-lg font-semibold">Memorials</h2>

                    <!-- Item 1 -->
                    <template v-for="memorial in memorials" :key="memorial.id">
                        <div class="bg-secondary mb-4 rounded-lg border p-4">
                            <h3 class="font-medium">{{ memorial.title }}</h3>
                            <p class="text-secondary-foreground text-sm">Material: {{ memorial.materials.join(', ') }}</p>
                            <p class="text-secondary-foreground text-sm">Quantity: {{ memorial.quantity }}</p>
                            <p class="text-secondary-foreground text-sm">Estimated Delivery: {{ memorial.estimated_delivery }}</p>
                            <p class="text-sm font-semibold">{{ memorial.total }}</p>
                        </div>
                    </template>
                </div>

                <!-- Next Steps -->
                <div class="my-8 border-t pt-6">
                    <h2 class="mb-3 text-lg font-semibold">Next Steps</h2>
                    <ul class="text-muted-foreground list-inside list-disc space-y-2 text-sm">
                        <li>Our team will review each headstone’s customization.</li>
                        <li>You will receive design proofs for approval within 48 hours.</li>
                        <li>We will coordinate delivery with cemetery staff after confirmation.</li>
                    </ul>
                </div>

                <!-- Support -->
                <div class="text-muted-foreground mt-8 rounded-lg p-4 text-sm">
                    <p>
                        Questions? Email
                        <a href="mailto:support@everstone.com" class="text-green-700 underline dark:text-green-400"> contact@everstone.co.ke </a>
                        or call <span class="font-medium">+254 700 000 0000</span>.
                    </p>
                </div>

                <!-- Actions -->
                <div class="mt-10 flex flex-col justify-center gap-4 sm:flex-row">
                    <Button @click="router.visit(route('customer.orders'))">View My Orders</Button>
                    <Button @click="router.visit(route('home'))" variant="outline" class="border-primary border dark:border-white"
                        >Return to Homepage</Button
                    >
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
