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
const props = defineProps({
    transactions: Object,
    filters: Object,
});

const breadcrumbs = [
    { title: 'Dashboard', href: route('admin.dashboard') },
    { title: 'Transactions', href: route('admin.transactions.index') },
];

const filters = reactive({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
});

const applyFilters = () => {
    if (filters.status === 'all') filters.status = '';

    router.get(route('admin.transactions.index'), filters, { preserveState: true, preserveScroll: true, replace: true });
};

const updatePage = (page) => {
    router.get(
        route('admin.transactions.index'),
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
    applyFilters();
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
    <Head title="Transactions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageTitle>Transactions</template>

        <div class="flex flex-wrap items-center gap-4">
            <div class="relative w-full items-center md:w-64">
                <Input
                    id="search"
                    type="text"
                    placeholder="Search by transaction id, customer name"
                    class="w-full max-w-80 pl-8"
                    v-model="filters.search"
                    @input="applyFilters"
                />
                <span class="absolute inset-y-0 start-0 flex items-center justify-center px-2">
                    <Search class="text-muted-foreground size-6" />
                </span>
            </div>

            <Select v-model="filters.status" @update:modelValue="applyFilters">
                <SelectTrigger>
                    <SelectValue placeholder="Select Status" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="all">All Status</SelectItem>
                    <SelectItem value="pending">Pending</SelectItem>
                    <SelectItem value="success">Success</SelectItem>
                    <SelectItem value="failed">Failed</SelectItem>
                    <SelectItem value="cancelled">Cancelled</SelectItem>
                    <SelectItem value="abandoned">Abandoned</SelectItem>
                    <SelectItem value="timeout">Timeout</SelectItem>
                </SelectContent>
            </Select>

            <Button @click.prevent="clearFilters" variant="link" as="button"> Clear Filters </Button>
        </div>

        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>ID</TableHead>
                    <TableHead>Transaction ID</TableHead>
                    <TableHead>Order ID</TableHead>
                    <TableHead>Customer</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Amount</TableHead>
                    <TableHead>Paid At</TableHead>
                    <TableHead>Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <Deferred data="transactions">
                    <template #fallback>
                        <template v-for="n in 5" :key="n">
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

                    <TableRow v-for="transaction in transactions.data" :key="transaction.id">
                        <TableCell class="py-4">#{{ transaction.id }}</TableCell>
                        <TableCell>#{{ transaction.transaction_id }}</TableCell>
                        <TableCell>#{{ transaction.order_id }}</TableCell>
                        <TableCell>{{ transaction.customer }}</TableCell>
                        <TableCell>
                            <Badge :class="transactionStatusClasses(transaction.status)" class="capitalize">{{ transaction.status }} </Badge>
                        </TableCell>
                        <TableCell>{{ transaction.amount }}</TableCell>
                        <TableCell>{{ transaction.paid_at }}</TableCell>
                        <TableCell>
                            <div class="flex items-center gap-2">
                                <Button @click="router.visit(route('admin.transactions.show', transaction.id))" size="sm">
                                    <Eye class="h-4 w-4" />
                                </Button>
                                <Button variant="destructive" size="sm" @click="deleteTransaction(transaction.id)">
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>

                    <TableRow v-if="transactions.data.length === 0">
                        <TableCell colspan="8" class="text-muted-foreground py-4 text-center"> No transactions found. </TableCell>
                    </TableRow>
                </Deferred>
            </TableBody>
        </Table>

        <!-- pagination -->
        <Pagination v-if="transactions" :meta="transactions.meta" :onPageChange="updatePage" />
    </AppLayout>
</template>
