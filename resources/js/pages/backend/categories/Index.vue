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
import { Edit2, PlusCircle, Search, Trash2 } from 'lucide-vue-next';
import { computed, onMounted, reactive, watch } from 'vue';
import { toast } from 'vue-sonner';

const flash = computed(() => usePage().props.flash);
const user = usePage().props.auth.user;
const props = defineProps({
    categories: Object,
    filters: Object,
});
const filters = reactive({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
});

const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
    },
    {
        title: 'Categories',
        href: '/admin/categories',
    },
];

const applyFilters = () => {
    if (filters.status === 'all') filters.status = '';

    router.get(route('admin.categories.index'), filters, { preserveState: true, preserveScroll: true, replace: true });
};

const updatePage = (page) => {
    router.get(
        route('admin.categories.index'),
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
        case 'published':
            return 'bg-green-500 dark:bg-green-400';
        case 'draft':
            return 'bg-slate-500 dark:bg-slate-400';
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
    <Head title="Categories" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageTitle> Categories List </template>

        <template #pageActions>
            <Button
                v-if="user.permissions.includes('create categories')"
                @click="router.visit(route('admin.categories.create'))"
                class="text-xs uppercase"
            >
                <PlusCircle class="size-4" /> Add New Category
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
                    placeholder="Search by name or id"
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
                    <SelectItem value="draft">draft</SelectItem>
                    <SelectItem value="published">Published</SelectItem>
                </SelectContent>
            </Select>

            <!-- clear filters -->
            <Button @click.prevent="clearFilters" variant="link" as="button"> Clear filters</Button>
        </div>

        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>ID</TableHead>
                    <TableHead>Category</TableHead>
                    <TableHead>Slug</TableHead>
                    <TableHead>Created By</TableHead>
                    <TableHead>Items</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Date</TableHead>
                    <TableHead>Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <Deferred data="categories">
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

                    <TableRow v-for="category in categories.data" :key="category.id">
                        <!-- category id -->
                        <TableCell>#{{ category.id }}</TableCell>

                        <!-- category name -->
                        <TableCell>{{ category.name }}</TableCell>

                        <!-- category slug -->
                        <TableCell>{{ category.slug }}</TableCell>

                        <!-- categories created by -->
                        <TableCell>{{ category.created_by }}</TableCell>

                        <!-- categories items -->
                        <TableCell>{{ category.items }}</TableCell>

                        <!-- category status -->
                        <TableCell>
                            <Badge class="capitalize" :class="statusClasses(category.status)">
                                {{ category.status }}
                            </Badge>
                        </TableCell>

                        <!-- Date Created -->
                        <TableCell>{{ category.date }}</TableCell>

                        <!-- actions read delete and view -->
                        <TableCell>
                            <div class="flex items-center gap-3">
                                <Button
                                    v-if="user.permissions.includes('update categories')"
                                    size="sm"
                                    @click="router.visit(route('admin.categories.edit', category.id))"
                                >
                                    <Edit2 class="h-4 w-4" />
                                </Button>

                                <Button
                                    v-if="user.permissions.includes('delete categories')"
                                    size="sm"
                                    @click="deleteCustomer(category.id)"
                                    variant="destructive"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>

                    <TableRow v-if="categories.data.length === 0">
                        <TableCell colspan="8" class="text-muted-foreground py-6 text-center"> No categories found. </TableCell>
                    </TableRow>
                </Deferred>
            </TableBody>
        </Table>

        <!-- pagination -->
        <Pagination v-if="categories" :meta="categories.meta" :onPageChange="updatePage" />
    </AppLayout>
</template>
