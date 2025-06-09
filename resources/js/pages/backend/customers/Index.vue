<script setup>
import Pagination from '@/components/Pagination.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Skeleton } from '@/components/ui/skeleton';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { Deferred, Head, router, usePage } from '@inertiajs/vue3';
import { CircleX, Edit2, PlusCircle, Search, Trash2 } from 'lucide-vue-next';
import { computed, onMounted, reactive, watch } from 'vue';
import { toast } from 'vue-sonner';

const props = defineProps({
    customers: Object,
    filters: Object,
});

const flash = computed(() => usePage().props.flash);
const filters = reactive({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
});
const user = usePage().props.auth.user;
const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
    },
    {
        title: 'Customers',
        href: '/admin/customers',
    },
];

console.log(user);

const applyFilters = () => {
    if (filters.status === 'all') filters.status = '';

    router.get(route('admin.customers.index'), filters, { preserveState: true, preserveScroll: true, replace: true });
};

const updatePage = (page) => {
    router.get(
        route('admin.customers.index'),
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

const statusClasses = (status) => {
    switch (status) {
        case 'active':
            return 'bg-green-500 dark:bg-green-400';
        case 'inactive':
        case 'banned':
            return 'bg-red-500 dark:bg-red-400';
        default:
            return '';
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
    <Head title="Customers List" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageTitle> Customers List </template>

        <template #pageActions>
            <Button
                v-if="user.permissions.includes('create customers')"
                @click="router.visit(route('admin.customers.create'))"
                size="sm"
                class="text-xs uppercase"
            >
                <PlusCircle class="size-4" /> Add New Customer
            </Button>
        </template>

        <!-- page filters and search -->
        <div class="flex flex-wrap items-center gap-4">
            <!-- search input -->
            <div class="relative flex w-full items-center md:w-64">
                <Input
                    id="search"
                    type="text"
                    @input="applyFilters"
                    placeholder="Search by user, name, email or id"
                    class="w-full max-w-80 pl-8"
                    v-model="filters.search"
                />

                <span class="absolute inset-y-0 start-0 flex items-center justify-center px-2">
                    <Search class="text-muted-foreground size-4" />
                </span>
            </div>

            <!-- filter by user status -->
            <Select v-model="filters.status" @update:modelValue="applyFilters">
                <SelectTrigger>
                    <SelectValue placeholder="Select Status.." />
                </SelectTrigger>

                <SelectContent class="capitalize">
                    <SelectItem value="all">All Status</SelectItem>
                    <SelectItem value="active">Active</SelectItem>
                    <SelectItem value="inactive">In Active</SelectItem>
                    <SelectItem value="banned">Banned</SelectItem>
                </SelectContent>
            </Select>

            <!-- clear filters -->
            <Button @click.prevent="clearFilters" variant="link" as="button"> Clear filters</Button>
        </div>

        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>ID</TableHead>
                    <TableHead>Customer</TableHead>
                    <TableHead>Email</TableHead>
                    <TableHead>Phone</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Joined On</TableHead>
                    <TableHead>Actions</TableHead>
                </TableRow>
            </TableHeader>

            <TableBody>
                <Deferred data="customers">
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
                            </TableRow>
                        </template>
                    </template>
                    <TableRow v-for="customer in customers.data" :key="customer.id">
                        <!-- customer id -->
                        <TableCell>#{{ customer.id }}</TableCell>

                        <!-- customer avatar -->
                        <TableCell>
                            <div class="flex items-center gap-2">
                                <Avatar>
                                    <AvatarImage :src="customer.avatar" alt="customer avatar" />
                                    <AvatarFallback>{{ customer.name.charAt(0) }}</AvatarFallback>
                                </Avatar>
                                <p>{{ customer.name }}</p>
                            </div>
                        </TableCell>

                        <!-- customer email -->
                        <TableCell>{{ customer.email }}</TableCell>

                        <!-- customer phone -->
                        <TableCell>{{ customer.phone }}</TableCell>

                        <!-- customer status -->
                        <TableCell>
                            <Badge class="capitalize" :class="statusClasses(customer.status)">
                                <CircleX class="size-4" v-if="customer.status == 'banned'" />
                                {{ customer.status }}
                            </Badge>
                        </TableCell>

                        <!-- date of joining -->
                        <TableCell>{{ customer.joined_on }}</TableCell>

                        <!-- actions read delete and view -->
                        <TableCell>
                            <div class="flex items-center gap-3">
                                <Button
                                    v-if="user.permissions.includes('edit customers')"
                                    size="sm"
                                    @click="router.visit(route('admin.customers.edit', customer.id))"
                                >
                                    <Edit2 class="h-4 w-4" />
                                </Button>

                                <Button
                                    v-if="user.permissions.includes('delete customers')"
                                    size="sm"
                                    @click="deleteCustomer(customer.id)"
                                    variant="destructive"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>

                    <TableRow v-if="customers.data.length === 0">
                        <TableCell colspan="7" class="text-muted-foreground py-4 text-center"> No customers found. </TableCell>
                    </TableRow>
                </Deferred>
            </TableBody>
        </Table>

        <!-- pagination -->
        <Pagination v-if="customers" :meta="customers.meta" :onPageChange="updatePage" />
    </AppLayout>
</template>
