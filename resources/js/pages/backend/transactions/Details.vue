<script setup>
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { LoaderIcon, Printer, Receipt, ShoppingBag, User } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({ transaction: Object });
const breadcrumbs = [
    { title: 'Dashboard', href: route('admin.dashboard') },
    { title: 'Transactions', href: route('admin.transactions.index') },
    { title: 'Transaction Details' },
];

const transaction = props.transaction.data;
const customer = transaction.customer || {};
const order = transaction.order || {};
const orderItems = order.memorials || [];
const downloadingReceipt = ref(false);

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

const downloadReceipt = async (transactionId) => {};
</script>

<template>
    <Head title="Transaction Details" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageTitle>
            <span class="flex items-center gap-3">
                Transaction Details <Badge class="capitalize" :class="transactionStatusClasses(transaction.status)">{{ transaction.status }} </Badge>
            </span>
        </template>
        <template #pageActions>
            <Button size="icon" variant="secondary">
                <Printer />
            </Button>

            <Button @click="downloadReceipt(transaction.id)" variant="secondary" :disabled="downloadingReceipt">
                <LoaderIcon v-if="downloadingReceipt" class="animate-spin" />
                Download Receipt</Button
            >
        </template>

        <section class="@container max-w-7xl space-y-6">
            <div class="grid grid-cols-1 gap-4 @2xl:grid-cols-2 @5xl:grid-cols-3">
                <div class="flex items-start gap-3 rounded-xl border p-5">
                    <div class="rounded-md bg-blue-50 p-2 text-blue-600 dark:bg-blue-950 dark:text-blue-400">
                        <Receipt class="h-5 w-5" />
                    </div>

                    <div class="space-y-6">
                        <h5 class="font-semibold">Payment Details</h5>

                        <div class="text-muted-foreground space-y-2 text-sm">
                            <p><span class="me-2 font-medium">ID:</span>{{ transaction.transaction_id }}</p>
                            <p><span class="me-2 font-medium">Date:</span>{{ transaction.paid_at }}</p>
                            <p><span class="me-2 font-medium">Payment Method:</span>{{ transaction.method }}</p>
                            <p><span class="me-2 font-medium">Amount:</span>{{ transaction.amount }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-start gap-3 rounded-xl border p-5">
                    <div class="rounded-md bg-blue-50 p-2 text-blue-600 dark:bg-blue-950 dark:text-blue-400">
                        <ShoppingBag class="h-5 w-5" />
                    </div>

                    <div class="space-y-6">
                        <h5 class="font-semibold">Order</h5>

                        <div class="text-muted-foreground space-y-2 text-sm">
                            <p><span class="me-2 font-medium">ID:</span>#{{ order.id }}</p>
                            <p><span class="me-2 font-medium">Date:</span>{{ order.date }}</p>
                            <p><span class="me-2 font-medium">Subtotal:</span>{{ order.subtotal }}</p>
                            <p><span class="me-2 font-medium">Discount:</span>{{ order.discount }}</p>
                            <p><span class="me-2 font-medium">Total:</span>{{ order.total }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-start gap-3 rounded-xl border p-5">
                    <div class="rounded-md bg-blue-50 p-2 text-blue-600 dark:bg-blue-950 dark:text-blue-400">
                        <User class="h-5 w-5" />
                    </div>
                    <div class="space-y-6">
                        <h5 class="font-semibold">Customer</h5>

                        <div class="text-muted-foreground space-y-2 text-sm">
                            <p><span class="me-2 font-medium">Name:</span>{{ customer.name }}</p>
                            <p class="break-all"><span class="me-2 font-medium">email:</span>{{ customer.email }}</p>
                            <p><span class="me-2 font-medium">Phone:</span>{{ customer.phone }}</p>
                            <p><span class="me-2 font-medium">Address:</span>{{ customer.address }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border p-5">
                <h2 class="text-lg font-semibold">Items</h2>

                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Item</TableHead>
                            <TableHead>Unit Price</TableHead>
                            <TableHead>Discount</TableHead>
                            <TableHead>Qty</TableHead>
                            <TableHead>Total</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="item in orderItems" :key="item.id">
                            <TableCell class="py-4">{{ item.title }}</TableCell>
                            <TableCell class="py-4">{{ item.price }}</TableCell>
                            <TableCell class="py-4">{{ item.discount }}</TableCell>
                            <TableCell class="py-4">{{ item.quantity }}</TableCell>
                            <TableCell class="py-4">{{ item.total }}</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
                <!-- Totals -->
                <div class="flex flex-col items-end justify-end space-y-2 border-t p-5 text-sm">
                    <p>
                        Subtotal: <strong>{{ order.subtotal }}</strong>
                    </p>
                    <p>
                        Discount: <strong>{{ order.discount }}</strong>
                    </p>
                    <p>
                        Total: <strong>{{ order.total }}</strong>
                    </p>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
