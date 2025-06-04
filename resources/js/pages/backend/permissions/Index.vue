<script setup>
import Pagination from '@/components/Pagination.vue';
import { Input } from '@/components/ui/input';
import { Skeleton } from '@/components/ui/skeleton';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { Deferred, Head, router } from '@inertiajs/vue3';
import { Check, Search, X } from 'lucide-vue-next';
import { reactive } from 'vue';

// Your logic goes here

const props = defineProps({
    permissions: Object,
    filters: Object,
});
const breadcrumbs = [
    { title: 'Dashboard', href: '/admin/dashboard' },
    { title: 'Permissions', href: '/admin/permissions' },
];

const filters = reactive({
    search: props.filters?.search || '',
});

const applyFilters = () => {
    router.get(route('admin.permissions'), filters, { preserveState: true, preserveScroll: true, replace: true });
};

const updatePage = (page) => {
    router.get(
        route('admin.permissions'),
        { ...filters, page },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};
</script>

<template>
    <Head title="Permissions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageTitle> Permissions </template>

        <!-- search input -->
        <div class="relative flex w-full items-center md:w-64">
            <Input id="search" type="text" @input="applyFilters" placeholder="Search by name" class="w-full max-w-80 pl-8" v-model="filters.search" />

            <span class="absolute inset-y-0 start-0 flex items-center justify-center px-2">
                <Search class="text-muted-foreground size-4" />
            </span>
        </div>

        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>ID</TableHead>
                    <TableHead>Permission Name</TableHead>
                    <TableHead>Admin</TableHead>
                    <TableHead>Moderator</TableHead>
                    <TableHead>User</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <Deferred data="permissions">
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
                                    <Skeleton class="h-4 w-5" />
                                </TableCell>
                                <TableCell class="py-4">
                                    <Skeleton class="h-4 w-5" />
                                </TableCell>
                                <TableCell class="py-4">
                                    <Skeleton class="h-4 w-5" />
                                </TableCell>
                            </TableRow>
                        </template>
                    </template>

                    <TableRow v-for="permission in permissions.data" :key="permission">
                        <TableCell class="py-4">#{{ permission.id }}</TableCell>
                        <TableCell class="capitalize">{{ permission.name }}</TableCell>
                        <TableCell>
                            <template v-if="permission.roles.some((r) => r.name === 'admin')">
                                <Check class="h-6 w-6 stroke-green-500 dark:stroke-green-400" />
                            </template>

                            <template v-else>
                                <X class="h-6 w-6 stroke-red-500 dark:stroke-red-400" />
                            </template>
                        </TableCell>
                        <TableCell>
                            <template v-if="permission.roles.some((r) => r.name === 'manager')">
                                <Check class="h-6 w-6 stroke-green-500 dark:stroke-green-400" />
                            </template>

                            <template v-else>
                                <X class="h-6 w-6 stroke-red-500 dark:stroke-red-400" />
                            </template>
                        </TableCell>
                        <TableCell>
                            <template v-if="permission.roles.some((r) => r.name === 'customer')">
                                <Check class="h-6 w-6 stroke-green-500 dark:stroke-green-400" />
                            </template>

                            <template v-else>
                                <X class="h-6 w-6 stroke-red-500 dark:stroke-red-400" />
                            </template>
                        </TableCell>
                    </TableRow>

                    <TableRow v-if="permissions.data.length === 0">
                        <TableCell colspan="5" class="text-muted-foreground py-4 text-center"> No permissions found. </TableCell>
                    </TableRow>
                </Deferred>
            </TableBody>
        </Table>

        <Pagination v-if="permissions" :meta="permissions.meta" :onPageChange="updatePage" />
    </AppLayout>
</template>
