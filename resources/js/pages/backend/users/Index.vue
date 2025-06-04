<script setup>
import Pagination from '@/components/Pagination.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Skeleton } from '@/components/ui/skeleton';
import AppLayout from '@/layouts/AppLayout.vue';
import { Deferred, Head, Link, router, usePage } from '@inertiajs/vue3';
import { Edit2, Mail, PlusCircle, Search } from 'lucide-vue-next';
import { computed, onMounted, reactive, watch } from 'vue';
import { toast } from 'vue-sonner';

const props = defineProps({
    users: Object,
    filters: Object,
    roles: Object,
});

const flash = computed(() => usePage().props.flash);
const filters = reactive({
    search: props.filters.search || '',
    status: props.filters.status || '',
    role: props.filters.role || '',
});
const breadcrumbs = [
    {
        title: 'Dashboard',
        href: route('admin.dashboard'),
    },
    {
        title: 'Users',
        href: route('admin.users.index'),
    },
];

const applyFilters = () => {
    if (filters.role === 'all') filters.role = '';
    if (filters.status === 'all') filters.status = '';

    router.get(route('admin.users.index'), filters, { preserveState: true, preserveScroll: true, replace: true });
};

const clearFilters = () => {
    filters.search = '';
    filters.status = '';
    filters.role = '';
    applyFilters();
};

const updatePage = (page) => {
    router.get(
        route('admin.users.index'),
        { ...filters, page },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

const roleClasses = (status) => {
    switch (status) {
        case 'admin':
            return 'bg-red-500 dark:bg-red-400';
        case 'manager':
            return 'bg-blue-500 dark:bg-blue-400';
        default:
            return 'bg-gray-200 dark:bg-gray-700';
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
    <Head title="Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageTitle>All Users</template>

        <template #pageActions>
            <Button @click="router.visit(route('admin.users.create'))" class="text-sm uppercase">
                <PlusCircle class="h-4 w-4" stroke="currentColor" />
                Add New USer
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

            <!-- filter by roles -->
            <Select v-model="filters.role" @update:modelValue="applyFilters">
                <SelectTrigger>
                    <SelectValue placeholder="Select Role.." />
                </SelectTrigger>

                <SelectContent class="capitalize">
                    <SelectItem value="all">All Status</SelectItem>
                    <template v-for="(role, i) in roles" :key="i">
                        <SelectItem :value="role">{{ role }}</SelectItem>
                    </template>
                </SelectContent>
            </Select>

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

        <div class="flex flex-wrap items-center gap-5">
            <Deferred data="users">
                <template #fallback>
                    <template v-for="i in 5" :key="i">
                        <div class="w-full max-w-64 overflow-hidden rounded-xl border">
                            <div class="flex flex-col items-center justify-center space-y-3 p-5">
                                <Skeleton class="h-24 w-24 rounded-full" />

                                <!-- name and email -->
                                <Skeleton class="h-4 w-32 rounded" />
                                <Skeleton class="h-4 w-24 rounded" />

                                <!-- roles -->
                                <div class="mt-2 flex flex-wrap justify-center gap-2">
                                    <Skeleton class="h-5 w-12 rounded-md" />
                                    <Skeleton class="h-5 w-16 rounded-md" />
                                </div>
                            </div>

                            <!-- footer buttons -->
                            <div class="flex divide-x border-t">
                                <div class="h-10 w-1/2"></div>
                                <div class="h-10 w-1/2"></div>
                            </div>
                        </div>
                    </template>
                </template>

                <template v-for="user in users.data" :key="user.id">
                    <div class="w-full max-w-64 overflow-hidden rounded-xl border">
                        <!-- card body -->
                        <Link :href="route('admin.users.show', user.id)">
                            <div class="flex flex-col items-center justify-center p-5">
                                <!-- user avatar -->
                                <Avatar class="h-24 w-24">
                                    <AvatarImage :src="user.avatar" alt="user avatar" />
                                    <AvatarFallback>{{ user.name.charAt(0) }}</AvatarFallback>
                                </Avatar>

                                <!-- name and email -->
                                <p class="mt-4 text-base font-semibold">{{ user.name }}</p>
                                <p class="text-muted-foreground line-clamp-1 text-sm">{{ user.email }}</p>

                                <!-- roles -->
                                <div class="mt-3 flex flex-wrap justify-center gap-2">
                                    <template v-for="role in user.roles" :key="role.id">
                                        <Badge :class="roleClasses(role.name)" class="capitalize">{{ role.name }}</Badge>
                                    </template>
                                </div>
                            </div>
                        </Link>

                        <!-- actions -->
                        <div class="text-muted-foreground flex divide-x border-t text-sm">
                            <a
                                :href="`mailto:${user.email}`"
                                type="button"
                                class="flex w-1/2 items-center justify-center gap-2 py-3 transition-colors hover:bg-stone-50 dark:hover:bg-stone-900"
                            >
                                <Mail class="h-5 w-5" stroke="currentColor" />
                                <span>Email</span>
                            </a>

                            <Link
                                :href="route('admin.users.edit', user.id)"
                                class="flex w-1/2 items-center justify-center gap-2 py-3 transition-colors hover:bg-stone-50 dark:hover:bg-stone-900"
                            >
                                <Edit2 class="h-5 w-5" strone="currentColor" />
                                <span>Edit</span>
                            </Link>
                        </div>
                    </div>
                </template>

                <div v-if="users.data.length === 0" class="text-muted-foreground py-4 text-center">No Users Found</div>
            </Deferred>
        </div>

        <Pagination v-if="users" :meta="users.meta" :onPageChange="updatePage" />
    </AppLayout>
</template>
