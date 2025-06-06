<script setup>
import NoDataFound from '@/components/NoDataFound.vue';
import Pagination from '@/components/Pagination.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import CustomerLayout from '@/layouts/CustomerLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import { reactive } from 'vue';

// Your logic goes here
const props = defineProps({
    orders: Object,
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
];

const filters = reactive({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
    payment_status: props.filters?.payment_status || '',
});

const applyFilters = () => {
    if (filters.status === 'all') filters.status = '';
    if (filters.payment_status === 'all') filters.payment_status = '';

    router.get(route('customer.orders'), filters, { preserveState: true, preserveScroll: true, replace: true });
};

const updatePage = (page) => {
    router.get(
        route('customer.orders'),
        { ...filters, page },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

const clearFilters = () => {
    filters.search = '';
    filters.status = '';
    filters.payment_status = '';
    applyFilters();
};

const orderStatusClasses = (status) => {
    switch (status) {
        case 'completed':
            return 'bg-emerald-600 text-white dark:bg-emerald-400 dark:text-black';
        case 'processing':
            return 'bg-blue-600 text-white dark:bg-blue-400 dark:text-black';
        case 'pending':
            return 'bg-yellow-400 text-black dark:bg-yellow-300 dark:text-black';
        case 'cancelled':
            return 'bg-rose-600 text-white dark:bg-rose-400 dark:text-black';
        default:
            return 'bg-slate-500 text-white dark:bg-slate-400 dark:text-black';
    }
};

const transactionStatusClasses = (status) => {
    switch (status) {
        case 'success':
            return 'bg-green-600 text-white dark:bg-green-400 dark:text-black';
        case 'failed':
        case 'reversed':
            return 'bg-red-600 text-white dark:bg-red-400 dark:text-black';
        case 'pending':
            return 'bg-yellow-400 text-black dark:bg-yellow-300 dark:text-black';
        case 'processing':
            return 'bg-blue-600 text-white dark:bg-blue-400 dark:text-black';
        case 'queued':
            return 'bg-indigo-600 text-white dark:bg-indigo-400 dark:text-black';
        case 'ongoing':
            return 'bg-cyan-600 text-white dark:bg-cyan-300 dark:text-black';
        case 'abandoned':
            return 'bg-gray-600 text-white dark:bg-gray-400 dark:text-black';
        default:
            return 'bg-slate-500 text-white dark:bg-slate-400 dark:text-black';
    }
};
</script>

<template>
    <Head title="Orders" />

    <CustomerLayout :breadcrumbs="breadcrumbs" :pageTitle="'My Orders'">
        <section class="space-y-5">
            <div class="flex flex-wrap items-center gap-4">
                <div class="relative w-full items-center md:w-64">
                    <Input id="search" type="text" placeholder="Search by order id" class="pl-10" v-model="filters.search" @input="applyFilters" />
                    <span class="absolute inset-y-0 start-0 flex items-center justify-center px-2">
                        <Search class="text-muted-foreground size-6" />
                    </span>
                </div>

                <Select v-model="filters.status" @update:modelValue="applyFilters" class="w-40">
                    <SelectTrigger>
                        <SelectValue placeholder="Select Status" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">All Status</SelectItem>
                        <SelectItem value="pending">Pending</SelectItem>
                        <SelectItem value="processing">Processing</SelectItem>
                        <SelectItem value="completed">Completed</SelectItem>
                        <SelectItem value="cancelled">Cancelled</SelectItem>
                    </SelectContent>
                </Select>

                <Select v-model="filters.payment_status" @update:modelValue="applyFilters" class="w-40">
                    <SelectTrigger>
                        <SelectValue placeholder="Select Payment Status" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">All Status</SelectItem>
                        <SelectItem value="success">Success</SelectItem>
                        <SelectItem value="failed">Failed</SelectItem>
                        <SelectItem value="cancelled">Cancelled</SelectItem>
                        <SelectItem value="pending">Pending</SelectItem>
                        <SelectItem value="abandoned">Abandoned</SelectItem>
                        <SelectItem value="timeout">Timeout</SelectItem>
                    </SelectContent>
                </Select>

                <Button @click.prevent="clearFilters" variant="link" as="button"> Clear Filters </Button>
            </div>

            <template v-for="order in orders.data" :key="order.id">
                <div class="space-y-4 rounded-xl border p-5 shadow">
                    <!-- header -->
                    <div class="flex items-center gap-3">
                        <div>
                            <h2 class="text-sm font-semibold">Order #{{ order.id }}</h2>
                            <p class="text-muted-foreground text-xs">Placed on {{ order.date }}</p>
                        </div>
                        <div class="flex-1"></div>

                        <div class="ms-auto flex w-auto flex-wrap items-center gap-2 justify-self-end">
                            <div class="group relative">
                                <Badge :class="orderStatusClasses(order.status)" class="capitalize">{{ order.status }} </Badge>

                                <div
                                    class="bg-primary text-primary-foreground absolute bottom-full left-1/2 z-20 mb-1 -translate-x-1/2 rounded-sm px-2 py-1 text-xs whitespace-nowrap opacity-0 transition-opacity group-hover:opacity-100"
                                >
                                    <div class="bg-primary absolute top-full left-1/2 z-10 -mt-1 h-2 w-2 -translate-x-1/2 rotate-45"></div>
                                    <span>Order</span>
                                </div>
                            </div>

                            <div class="group relative">
                                <Badge :class="transactionStatusClasses(order.payment_status)" class="capitalize">{{ order.payment_status }} </Badge>
                                <div
                                    class="bg-primary text-primary-foreground absolute bottom-full left-1/2 z-20 mb-1 -translate-x-1/2 rounded-sm px-2 py-1 text-xs whitespace-nowrap opacity-0 transition-opacity group-hover:opacity-100"
                                >
                                    <div class="bg-primary absolute top-full left-1/2 z-10 -mt-1 h-2 w-2 -translate-x-1/2 rotate-45"></div>
                                    <span>Payment</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- memorials -->
                    <div>
                        <p class="mb-1 text-sm font-semibold">Memorials Ordered:</p>
                        <ul
                            class="text-muted-foreground list-inside list-disc space-y-1 text-sm"
                            v-for="memorial in order.memorials"
                            :key="memorial.id"
                        >
                            <li>{{ memorial.title }}</li>
                        </ul>
                    </div>

                    <!-- order Summary -->
                    <div class="flex items-center justify-between border-t pt-3">
                        <p class="text-sm font-medium">
                            Total: <span class="font-bold">{{ order.total }}</span>
                        </p>
                        <Link
                            :href="route('customer.orders.show', order.id)"
                            class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-400"
                        >
                            View Details
                        </Link>
                    </div>
                </div>
            </template>

            <template v-if="orders.data.length < 1">
                <section class="text-muted-foreground flex w-full flex-col items-center justify-center gap-2">
                    <NoDataFound />
                </section>
            </template>

            <Pagination :meta="orders.meta" :onPageChange="updatePage" />
        </section>
    </CustomerLayout>
</template>

<style scoped>
/* Your styles go here */
</style>
