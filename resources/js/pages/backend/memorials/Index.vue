<script setup>
import Pagination from '@/components/Pagination.vue';
import { AspectRatio } from '@/components/ui/aspect-ratio';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Skeleton } from '@/components/ui/skeleton';
import AppLayout from '@/layouts/AppLayout.vue';
import { Deferred, Head, Link, router, usePage } from '@inertiajs/vue3';
import { PlusCircle, Search } from 'lucide-vue-next';
import { computed, onMounted, reactive, watch } from 'vue';
import { toast } from 'vue-sonner';

const flash = computed(() => usePage().props.flash);
const user = usePage().props.auth.user;

const props = defineProps({
    memorials: Object,
    filters: Object,
});
const breadcrumbs = [
    { title: 'Dashboard', href: '/admin/dashboard' },
    { title: 'Memorials', href: '/admin/memorials' },
];

const filters = reactive({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
});

const applyFilters = () => {
    if (filters.status === 'all') filters.status = '';

    router.get(route('admin.memorials.index'), filters, { preserveState: true, preserveScroll: true, replace: true });
};

const updatePage = (page) => {
    router.get(
        route('admin.memorials.index'),
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
    <Head title="Memorials" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageTitle> Memorials </template>

        <template #pageActions>
            <Button
                v-if="user.permissions.includes('create memorials')"
                @click="router.visit(route('admin.memorials.create'))"
                class="text-xs uppercase"
            >
                <PlusCircle class="size-4" />Add New Memorial
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
                    placeholder="Search by title, id, slug"
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
                    <SelectItem value="published">Published</SelectItem>
                    <SelectItem value="draft">Draft</SelectItem>
                </SelectContent>
            </Select>

            <!-- clear filters -->
            <Button @click.prevent="clearFilters" variant="link" as="button"> Clear filters</Button>
        </div>

        <!-- memorials -->
        <div class="flex flex-wrap items-center gap-5">
            <Deferred data="memorials">
                <template #fallback>
                    <template v-for="i in 6" :key="i">
                        <div class="w-full max-w-72 overflow-hidden rounded-md border shadow-xs">
                            <Skeleton class="aspect-[1/1] rounded-t-md" />

                            <div class="space-y-3 p-4">
                                <!-- title and badge -->
                                <div class="flex items-center justify-between">
                                    <Skeleton class="h-4 w-32 rounded" />
                                    <Skeleton class="h-5 w-16 rounded-full" />
                                </div>

                                <!-- description -->
                                <div class="space-y-2">
                                    <Skeleton class="h-3 w-full rounded" />
                                    <Skeleton class="h-3 w-5/6 rounded" />
                                </div>

                                <!-- prices -->
                                <div class="flex items-center gap-3">
                                    <Skeleton class="h-4 w-16 rounded" />
                                    <Skeleton class="h-4 w-12 rounded" />
                                </div>

                                <!-- date -->
                                <Skeleton class="h-3 w-1/2 rounded" />

                                <!-- button -->
                                <div class="pt-2">
                                    <Skeleton class="h-8 w-full rounded" />
                                </div>
                            </div>
                        </div>
                    </template>
                </template>

                <template v-for="memorial in memorials.data" :key="memorial.id">
                    <div class="group relative w-full max-w-72 overflow-hidden rounded-md border shadow-xs transition-all hover:shadow-sm">
                        <!-- Image -->
                        <AspectRatio :ratio="2 / 2" class="bg-muted">
                            <img
                                :src="memorial.image"
                                alt="memorial image"
                                class="h-full w-full rounded-t-md transition-all duration-300 group-hover:scale-105"
                            />
                        </AspectRatio>

                        <!-- Content -->
                        <div class="space-y-2 p-4">
                            <div class="flex items-center justify-between">
                                <Link :href="route('admin.memorials.show', memorial.id)">
                                    <p class="line-clamp-1 text-base font-semibold group-hover:underline">{{ memorial.title }}</p></Link
                                >

                                <Badge class="ms-1 capitalize" variant="secondary">{{ memorial.status }}</Badge>
                            </div>

                            <p class="text-muted-foreground line-clamp-2 text-sm">{{ memorial.description }}</p>

                            <div class="flex items-center gap-3">
                                <p :class="memorial.sale_price ? 'text-muted-foreground text-sm line-through' : 'text-sm font-medium'">
                                    {{ memorial.price }}
                                </p>
                                <p class="text-sm font-medium" v-if="memorial.sale_price">{{ memorial.sale_price }}</p>
                            </div>

                            <p class="text-muted-foreground text-xs">Created: {{ new Date(memorial.created_at).toLocaleDateString() }}</p>

                            <!-- actions -->
                            <div class="flex justify-between gap-2 pt-2">
                                <Button size="sm" class="w-full" @click="router.visit(route('admin.memorials.edit', memorial.id))">Edit</Button>
                            </div>
                        </div>
                    </div>
                </template>

                <div v-if="memorials.data.length === 0" class="text-muted-foreground w-full py-6 text-center">No memorials found</div>
            </Deferred>
        </div>

        <!-- pagination -->
        <Pagination v-if="memorials" :meta="memorials.meta" :onPageChange="updatePage" />
    </AppLayout>
</template>
