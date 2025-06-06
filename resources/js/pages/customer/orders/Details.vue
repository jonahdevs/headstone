<script setup>
import { AspectRatio } from '@/components/ui/aspect-ratio';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import CustomerLayout from '@/layouts/CustomerLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useEchoModel } from '@laravel/echo-vue';
import axios from 'axios';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

// Your logic goes here
const props = defineProps({ order: Object });
const downloadingReceipt = ref(false);

useEchoModel('App.Models.Order', props.order.data.id, ['.order.updated'], (e) => {
    router.reload();
});

const breadcrumbs = [
    {
        title: 'Home',
        href: '/',
    },
    {
        title: 'Orders',
        href: '/customer/orders',
    },
    {
        title: 'Details',
        href: '/customer/orders/show',
    },
];

const orderStatusClasses = (status) => {
    switch (status) {
        case 'completed':
            return 'bg-emerald-500 text-white';
        case 'processing':
            return 'bg-blue-500 text-white';
        case 'pending':
            return 'bg-yellow-400 text-black';
        case 'cancelled':
            return 'bg-rose-500 text-white';
        default:
            return 'bg-slate-500 text-white';
    }
};

const transactionStatusClasses = (status) => {
    switch (status) {
        case 'success':
            return 'text-green-500';
        case 'failed':
        case 'reversed':
            return 'text-red-500';
        case 'pending':
            return 'text-yellow-400';
        case 'processing':
            return 'text-blue-500';
        case 'queued':
            return 'text-indigo-500';
        case 'ongoing':
            return 'text-cyan-500';
        case 'abandoned':
            return 'text-muted-foreground';
        default:
            return 'text-muted-foreground';
    }
};

const downloadReceipt = async (orderId) => {
    try {
        downloadingReceipt.value = true;
        const response = await axios.get(route('customer.orders.download', orderId), {
            responseType: 'blob', // Important!
        });

        if (response.status === 200) {
            downloadingReceipt.value = false;
        }
        // Extract filename from headers (optional but useful)
        const disposition = response.headers['content-disposition'];
        let filename = 'receipt.pdf';
        if (disposition && disposition.includes('filename=')) {
            filename = disposition.split('filename=')[1].replace(/["']/g, '');
        }

        // Create a temporary link and trigger the download
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        link.remove();

        window.URL.revokeObjectURL(url); // Clean up
    } catch (error) {
        downloadingReceipt.value = false;
        if (error.response && error.response.status === 404) {
            toast.error('File not found.');
        } else {
            console.log(error);
            toast.error('Something went wrong while downloading.');
        }
    }
};
</script>

<template>
    <Head title="Order Details" />

    <CustomerLayout :breadcrumbs="breadcrumbs" :pageTitle="'Order Details'">
        <div>
            <h1 class="text-2xl font-bold">Order #{{ order.data.id }}</h1>

            <p class="text-muted-foreground text-sm">Date: {{ order.data.date }}</p>
            <p class="text-muted-foreground text-sm">{{ order.data.items + (order.data.items > 1 ? ' Items' : ' Item') }}</p>
            <p class="text-muted-foreground text-sm">
                Status: <Badge :class="orderStatusClasses(order.data.status)" class="capitalize">{{ order.data.status }} </Badge>
            </p>
        </div>

        <div class="grid gap-8 border-t pt-6 text-sm md:grid-cols-2">
            <!-- Shipping & Customer -->
            <div>
                <h2 class="mb-1 font-semibold">Customer Info</h2>

                <p class="text-muted-foreground text-sm">{{ order.data.customer.name }}</p>
                <p class="text-muted-foreground text-sm">{{ order.data.customer.email }}</p>
                <p class="text-muted-foreground text-sm">{{ order.data.customer.address }}</p>
            </div>

            <!-- Payment Summary -->
            <div>
                <h3 class="mb-1 font-semibold">Payment Info</h3>
                <div class="text-muted-foreground text-sm">
                    <p>
                        Status:
                        <span class="capitalize" :class="transactionStatusClasses(order.data.payment.status)">{{ order.data.payment.status }}</span>
                    </p>
                    <p>
                        Method: <span>{{ order.data.payment.method }}</span>
                    </p>
                    <p>
                        Transaction ID: <span class="text-muted-foreground">{{ order.data.payment.transaction_id }}</span>
                    </p>
                    <p>
                        Total: <span class="font-semibold">{{ order.data.payment.amount }}</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- memorials -->
        <div class="space-y-4 border-t pt-6">
            <h3 class="mb-4 font-semibold">Memorials</h3>

            <template v-for="memorial in order.data.memorials" :key="memorial.id">
                <div class="flex items-start gap-6">
                    <div class="h-16 w-16">
                        <AspectRatio :ratio="1 / 1">
                            <img :src="memorial.image" alt="memorial image" class="h-full w-full rounded-md object-cover" />
                        </AspectRatio>
                    </div>

                    <div class="flex-1 text-sm">
                        <p class="font-medium">{{ memorial.title }}</p>
                        <div class="flex items-center gap-3" v-if="memorial.quantity || memorial.sku">
                            <p class="text-muted-foreground">Qty: {{ memorial.quantity }}</p>
                            <p class="text-muted-foreground">SKU: {{ memorial.sku }}</p>
                        </div>
                        <div class="flex items-center gap-3" v-if="memorial.dimensions || memorial.material">
                            <p class="text-muted-foreground">Dimensions: {{ memorial.dimensions }}</p>
                            <p class="text-muted-foreground">Material: {{ memorial.material }}</p>
                        </div>
                        <p class="text-muted-foreground">Estimated Delivery: {{ memorial.estimated_delivery }}</p>
                    </div>

                    <p class="text-right text-sm">{{ memorial.total }}</p>
                </div>
            </template>
        </div>

        <!-- summary -->
        <div class="space-y-1 border-t pt-4 text-sm">
            <div class="flex justify-between">
                <span class="text-muted-foreground">Subtotal</span>
                <span>{{ order.data.subtotal }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-muted-foreground">Discount</span>
                <span>{{ order.data.discount }}</span>
            </div>

            <div class="flex justify-between font-semibold">
                <span>Total</span>
                <span>{{ order.data.total }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-muted-foreground border-t pt-4 text-center text-sm">
            <p>
                Need help?
                <Link href="/contact" class="text-blue-600 hover:underline dark:text-blue-400">Contact Support</Link>
            </p>
            <p class="mt-1">
                <Button variant="link" @click="downloadReceipt(order.data.id)" class="text-blue-600 hover:underline dark:text-blue-400"
                    >Download Receipt</Button
                >
            </p>
        </div>
    </CustomerLayout>
</template>
