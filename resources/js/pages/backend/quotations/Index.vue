<script setup>
import Pagination from '@/components/Pagination.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Skeleton } from '@/components/ui/skeleton';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { Deferred, Head, Link, router, usePage } from '@inertiajs/vue3';
import { PlusCircle, Search } from 'lucide-vue-next';
import { computed, onMounted, reactive, watch } from 'vue';
import { toast } from 'vue-sonner';

const flash = computed(() => usePage().props.flash);
const user = usePage().props.auth.user;
const props = defineProps({
    quotations: Object,
    filters: Object,
});

const breadcrumb = [
    { name: 'Dashboard', href: route('admin.dashboard') },
    { name: 'Quotations', href: route('admin.quotations.index') },
];

const filters = reactive({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
    urgency: props.filters?.urgency || '',
});

const applyFilters = () => {
    if (filters.status === 'all') filters.status = '';
    if (filters.urgency === 'all') filters.urgency = '';

    router.get(route('admin.quotations.index'), filters, { preserveState: true, preserveScroll: true, replace: true });
};

const updatePage = (page) => {
    router.get(
        route('admin.quotations.index'),
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
    filters.urgency = '';
    applyFilters();
};
const statusColorClasses = (status) => {
    switch (status) {
        case 'pending':
            return 'bg-slate-500 dark:bg-slate-400';
        case 'viewed':
            return 'bg-blue-500 dark:bg-blue-400';
        case 'responded':
            return 'bg-green-500 dark:bg-green-400';
        default:
            return 'bg-black';
    }
};

const responseStatusColorClasses = (status) => {
    switch (status) {
        case 'pending':
            return 'bg-slate-500 dark:bg-slate-400';
        case 'responded':
            return 'bg-green-500 dark:bg-green-400';
        default:
            return 'bg-black';
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
    <Head title="Quotations List" />

    <AppLayout :breadcrumb="breadcrumb">
        <template #pageTitle> Quotations </template>

        <template #pageActions>
            <Button v-if="user.permissions.includes('create quotations')" class="text-xs uppercase">
                <PlusCircle class="h-4 w-4" />Add New Quotation
            </Button>
        </template>

        <div class="flex flex-wrap items-center gap-4">
            <div class="relative w-full items-center md:w-64">
                <Input
                    id="search"
                    type="text"
                    placeholder="Search by id or customer email"
                    class="w-full max-w-80 pl-8"
                    v-model="filters.search"
                    @input="applyFilters"
                />
                <span class="absolute inset-y-0 start-0 flex items-center justify-center px-2">
                    <Search class="text-muted-foreground size-6" />
                </span>
            </div>

            <Select v-model="filters.status" @update:modelValue="applyFilters" placeholder="Select Status">
                <SelectTrigger>
                    <SelectValue placeholder="Select Status" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="all">All Status</SelectItem>
                    <SelectItem value="pending">Pending</SelectItem>
                    <SelectItem value="viewed">Viewed</SelectItem>
                    <SelectItem value="responded">Responded</SelectItem>
                </SelectContent>
            </Select>

            <Button @click.prevent="clearFilters" variant="link" as="button"> Clear Filters </Button>
        </div>

        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>Quote #</TableHead>
                    <TableHead>Customer</TableHead>
                    <TableHead>Memorial</TableHead>
                    <TableHead>Material</TableHead>
                    <TableHead>Budget</TableHead>
                    <TableHead>Quoted</TableHead>
                    <TableHead>Response</TableHead>
                    <TableHead>Date</TableHead>
                    <TableHead>Deadline</TableHead>
                    <TableHead>Actions</TableHead>
                </TableRow>
            </TableHeader>

            <TableBody>
                <Deferred data="quotations">
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
                                <TableCell class="py-4">
                                    <Skeleton class="h-4 w-28" />
                                </TableCell>
                                <TableCell class="py-4">
                                    <Skeleton class="h-4 w-24" />
                                </TableCell>
                                <TableCell class="py-4">
                                    <Skeleton class="h-4 w-16" />
                                </TableCell>
                            </TableRow>
                        </template>
                    </template>
                    <TableRow v-for="quote in quotations.data" :key="quote.id">
                        <TableCell class="py-4">#{{ quote.id }}</TableCell>
                        <TableCell class="py-4">{{ quote.customer }}</TableCell>
                        <TableCell class="py-4">{{ quote.memorial }}</TableCell>
                        <TableCell class="py-4">{{ quote.material }}</TableCell>
                        <TableCell class="py-4 text-green-700 dark:text-green-400">{{ quote.budget }}</TableCell>
                        <TableCell class="py-4 text-blue-700 dark:text-blue-400">
                            <span v-if="quote.quoted_price">{{ quote.quoted_price }}</span>
                            <span v-else class="text-slate-400">--</span>
                        </TableCell>
                        <TableCell class="py-4">
                            <Badge :class="responseStatusColorClasses(quote.status)" class="capitalize">{{ quote.status }}</Badge>
                        </TableCell>
                        <TableCell class="py-4">{{ quote.created_at }}</TableCell>
                        <TableCell class="py-4">{{ quote.deadline }}</TableCell>
                        <TableCell class="py-4">
                            <Link
                                :href="route('admin.quotations.show', quote.id)"
                                class="text-blue-700 hover:underline dark:text-blue-400"
                                v-if="user.permissions.includes('update quotations')"
                            >
                                View
                            </Link>
                        </TableCell>
                    </TableRow>
                    <TableRow v-if="quotations.data.length === 0">
                        <TableCell colspan="10" class="text-muted-foreground py-4 text-center"> No quotations found. </TableCell>
                    </TableRow>
                </Deferred>
            </TableBody>
        </Table>

        <Pagination v-if="quotations" :meta="quotations.meta" :onPageChange="updatePage" />
    </AppLayout>
</template>
