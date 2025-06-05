<script setup>
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { LoaderCircle, LoaderIcon, Printer, Receipt, ShoppingBag, User } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const order = computed(() => usePage().props.order.data);
const customer = order.value.customer;
const transaction = order.value.transaction;
const orderItems = order.value.memorials;
const downloadingReceipt = ref(false);

const form = useForm({
    status: order.value.status,
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/admin/dashboard' },
    { title: 'Orders', href: '/admin/orders' },
    { title: 'Order Details', href: `admin/orders/${order.value.id}/show` },
];
const downloadReceipt = async () => {};

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
            return 'text-green-600 dark:text-green-400';
        case 'failed':
        case 'reversed':
            return 'text-red-600 dark:text-red-400';
        case 'pending':
            return 'text-yellow-400 text-black dark:text-yellow-300';
        case 'processing':
            return 'text-blue-600 dark:text-blue-400';
        case 'queued':
            return 'text-indigo-600 dark:text-indigo-400';
        case 'ongoing':
            return 'text-cyan-600 dark:text-cyan-300';
        case 'abandoned':
            return 'text-gray-600 dark:text-gray-400';
        default:
            return 'text-slate-500 dark:text-slate-400';
    }
};

const orderStatus = ['pending', 'processing', 'completed', 'cancelled'];

const updateOrderStatus = (val) => {
    form.put(route('admin.orders.update', order.value.id), {
        onSuccess: () => {
            router.reload({ only: ['order'] });
        },
    });
};
</script>

<template>
    <Head title="Order Details" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageTitle>
            <span class="flex items-center gap-3">
                Order Details <Badge class="capitalize" :class="orderStatusClasses(order.status)"> {{ order.status }}</Badge>
            </span>
        </template>

        <template #pageActions>
            <LoaderCircle v-if="form.processing" class="size-5 animate-spin" />

            <Select v-model="form.status" @update:modelValue="(val) => updateOrderStatus(val)">
                <SelectTrigger>
                    <SelectValue placeholder="Change Status" class="capitalize" />
                </SelectTrigger>
                <SelectContent class="capitalize">
                    <template v-for="(status, i) in orderStatus" :key="i">
                        <SelectItem :value="status">{{ status }}</SelectItem>
                    </template>
                </SelectContent>
            </Select>

            <Button size="icon" variant="secondary">
                <Printer />
            </Button>

            <Button @click="downloadReceipt(order.id)" variant="secondary" :disabled="downloadingReceipt">
                <LoaderIcon v-if="downloadingReceipt" class="animate-spin" />
                Download Receipt</Button
            >
        </template>

        <section class="@container max-w-7xl space-y-6">
            <div class="grid grid-cols-1 gap-4 @2xl:grid-cols-2 @5xl:grid-cols-3">
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
                        <Receipt class="h-5 w-5" />
                    </div>

                    <div class="space-y-6">
                        <h5 class="font-semibold">Payment Details</h5>

                        <div class="text-muted-foreground space-y-2 text-sm">
                            <p><span class="me-2 font-medium">ID:</span>{{ transaction.transaction_id }}</p>
                            <p><span class="me-2 font-medium">Date:</span>{{ transaction.paid_at }}</p>
                            <p><span class="me-2 font-medium">Payment Method:</span>{{ transaction.payment_method }}</p>
                            <p><span class="me-2 font-medium">Amount:</span>{{ transaction.amount }}</p>
                            <p>
                                <span class="me-2 font-medium">Status:</span>
                                <span class="capitalize" :class="transactionStatusClasses(transaction.status)"> {{ transaction.status }}</span>
                            </p>
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
                <h2 class="text-lg font-semibold">Order Items</h2>

                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Item</TableHead>
                            <TableHead>Unit Price</TableHead>
                            <TableHead>Discount</TableHead>
                            <TableHead>Qty</TableHead>
                            <TableHead>Total</TableHead>
                            <TableHead>Action</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="item in orderItems" :key="item.id">
                            <TableCell class="py-4">{{ item.title }}</TableCell>
                            <TableCell class="py-4">{{ item.unit_price }}</TableCell>
                            <TableCell class="py-4">{{ item.discount }}</TableCell>
                            <TableCell class="py-4">{{ item.quantity }}</TableCell>
                            <TableCell class="py-4">{{ item.total }}</TableCell>
                            <TableCell class="py-4">
                                <Link href="#" class="text-sm text-blue-600 hover:underline dark:text-blue-400"> View </Link>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
                <!-- Totals -->
                <div class="flex flex-col items-end justify-end space-y-3 border-t p-5 text-sm">
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
