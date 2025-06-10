<script setup>
import Pagination from '@/components/Pagination.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Skeleton } from '@/components/ui/skeleton';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { Deferred, Head, router, usePage } from '@inertiajs/vue3';
import { Eye, Search, Trash2 } from 'lucide-vue-next';
import { computed, onMounted, reactive, watch } from 'vue';
import { toast } from 'vue-sonner';

const flash = computed(() => usePage().props.flash);
const user = usePage().props.auth.user;

const props = defineProps({
    orders: Object,
    filters: Object,
});

const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
    },
    {
        title: 'Orders',
        href: '/admin/orders',
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

    router.get(route('admin.orders.index'), filters, { preserveState: true, preserveScroll: true, replace: true });
};

const updatePage = (page) => {
    router.get(
        route('admin.orders.index'),
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

watch(
    () => flash.value.message,
    (msg) => {
        if (msg && msg.type === 'success') {
            toast.success(msg.body);
        } else if (msg && msg.type === 'error') {
            toast.error(msg.body);
        }
    },
);

onMounted(() => {
    let msg = flash.value.message;

    if (msg && msg.type === 'success') {
        toast.success(msg.body);
    } else if (msg && msg.type === 'error') {
        toast.error(msg.body);
    }
});
</script>

<template>
    <Head title="Orders" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageTitle> Orders List </template>

        <!-- page filters and search -->
        <div class="flex flex-wrap items-center gap-4">
            <!-- search input -->
            <div class="relative flex w-full items-center md:w-64">
                <Input
                    id="search"
                    type="text"
                    @input="applyFilters"
                    placeholder="Search by id or customer email"
                    class="w-full max-w-80 pl-8"
                    v-model="filters.search"
                />

                <span class="absolute inset-y-0 start-0 flex items-center justify-center px-2">
                    <Search class="text-muted-foreground size-4" />
                </span>
            </div>

            <!-- filter by status -->
            <Select v-model="filters.status" @update:modelValue="applyFilters">
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

            <!-- filter by payment status -->
            <Select v-model="filters.payment_status" @update:modelValue="applyFilters">
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

            <!-- clear filters -->
            <Button @click.prevent="clearFilters" variant="link" as="button"> Clear filters</Button>
        </div>

        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>Order ID</TableHead>
                    <TableHead>Customer</TableHead>
                    <TableHead>Fullfillment Status</TableHead>
                    <TableHead>Payment Status</TableHead>
                    <TableHead>Date</TableHead>
                    <TableHead>Items</TableHead>
                    <TableHead>Total</TableHead>
                    <TableHead>Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <Deferred data="orders">
                    <template #fallback>
                        <template v-for="n in 10" :key="n">
                            <TableRow>
                                <TableCell class="py-4">
                                    <Skeleton class="h-4 w-16" />
                                </TableCell>
                                <TableCell class="py-4">
                                    <Skeleton class="h-4 w-32" />
                                </TableCell>
                                <TableCell class="py-4">
                                    <Skeleton class="h-4 w-28" />
                                </TableCell>
                                <TableCell class="py-4">
                                    <Skeleton class="h-4 w-24" />
                                </TableCell>
                                <TableCell class="py-4">
                                    <Skeleton class="h-4 w-20" />
                                </TableCell>
                                <TableCell class="py-4">
                                    <Skeleton class="h-4 w-20" />
                                </TableCell>
                                <TableCell class="py-4">
                                    <Skeleton class="h-4 w-20" />
                                </TableCell>
                                <TableCell class="py-4">
                                    <Skeleton class="h-4 w-24" />
                                </TableCell>
                            </TableRow>
                        </template>
                    </template>

                    <TableRow v-for="order in orders.data" :key="order.id">
                        <TableCell>#{{ order.id }}</TableCell>
                        <TableCell>
                            <div class="flex items-center gap-2">
                                <p class="line-clamp-1 w-full">{{ order.customer }}</p>
                            </div>
                        </TableCell>
                        <TableCell>
                            <Badge :class="orderStatusClasses(order.status)" class="capitalize">{{ order.status }} </Badge>
                        </TableCell>
                        <TableCell>
                            <Badge :class="transactionStatusClasses(order.payment_status)" class="capitalize">{{ order.payment_status }} </Badge>
                        </TableCell>
                        <TableCell>{{ order.placed_on }}</TableCell>
                        <TableCell>{{ order.items }}</TableCell>
                        <TableCell>{{ order.total }}</TableCell>
                        <TableCell>
                            <div class="flex items-center gap-2">
                                <Button
                                    v-if="user.permissions.includes('update orders')"
                                    @click="router.visit(route('admin.orders.show', order.id))"
                                    size="sm"
                                >
                                    <Eye class="h-4 w-4" />
                                </Button>
                                <Button
                                    v-if="user.permissions.includes('delete orders')"
                                    variant="destructive"
                                    size="sm"
                                    @click="deleteOrder(order.id)"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>

                    <TableRow v-if="orders.data.length === 0">
                        <TableCell colspan="8" class="text-muted-foreground py-4 text-center"> No orders found. </TableCell>
                    </TableRow>
                </Deferred>
            </TableBody>
        </Table>

        <!-- table pagination -->
        <Pagination v-if="orders" :meta="orders.meta" :onPageChange="updatePage" />
    </AppLayout>
</template>
