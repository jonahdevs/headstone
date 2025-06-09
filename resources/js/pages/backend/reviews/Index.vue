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
import { Search, Star } from 'lucide-vue-next';
import { computed, onMounted, reactive, watch } from 'vue';
import { toast } from 'vue-sonner';

// Your logic goes here

const flash = computed(() => usePage().props.flash);
const user = usePage().props.auth.user;
const props = defineProps({
    reviews: Object,
    filters: Object,
});

const breadcrumbs = [
    { title: 'Dashboard', href: route('admin.dashboard') },
    { title: 'Reviews', href: route('admin.reviews.index') },
];

const filters = reactive({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
});

const applyFilters = () => {
    if (filters.status === 'all') filters.status = '';

    router.get(route('admin.reviews.index'), filters, { preserveState: true, preserveScroll: true, replace: true });
};

const updatePage = (page) => {
    router.get(
        route('admin.reviews.index'),
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
        case 'rejected':
            return 'bg-red-500 dark:bg-red-400';
        case 'pending':
            return 'bg-yellow-500 dark:bg-yellow-400';
        default:
            return 'bg-back';
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
    <Head title="Reviews" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageTitle>Reviews</template>

        <div class="flex flex-wrap items-center gap-4">
            <div class="relative w-full items-center md:w-64">
                <Input
                    id="search"
                    type="text"
                    placeholder="Search by customer name, product title"
                    class="w-full max-w-80 pl-10"
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
                    <SelectItem value="published">Published</SelectItem>
                    <SelectItem value="rejected">Rejected</SelectItem>
                    <SelectItem value="pending">Pending</SelectItem>
                </SelectContent>
            </Select>

            <Button @click.prevent="clearFilters" variant="link" as="button"> Clear Filters </Button>
        </div>

        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>ID</TableHead>
                    <TableHead>Customer</TableHead>
                    <TableHead>Memorial</TableHead>
                    <TableHead>Rating</TableHead>
                    <TableHead>Review</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Date</TableHead>
                    <TableHead>Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <Deferred data="reviews">
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
                                    <Skeleton class="h-4 w-20" />
                                </TableCell>
                            </TableRow>
                        </template>
                    </template>

                    <TableRow v-for="review in reviews.data" :key="review.id">
                        <TableCell>#{{ review.id }}</TableCell>
                        <TableCell>
                            {{ review.customer }}
                        </TableCell>
                        <TableCell>
                            {{ review.memorial }}
                        </TableCell>
                        <TableCell>
                            <div class="flex items-center gap-1">
                                <template v-for="i in 5" :key="i">
                                    <Star
                                        class="h-4 w-4"
                                        :class="
                                            i <= review.rating
                                                ? 'fill-yellow-500 stroke-yellow-500'
                                                : 'fill-stone-200 stroke-stone-200 dark:fill-stone-800 dark:stroke-stone-800'
                                        "
                                    />
                                </template>
                                <span class="text-muted-foreground ms-2 text-xs font-medium tracking-wide">({{ review.rating }})</span>
                            </div>
                        </TableCell>
                        <TableCell>
                            <p class="line-clamp-3 max-w-96 text-wrap">{{ review.review ?? 'null' }}</p>
                        </TableCell>
                        <TableCell>
                            <Badge :class="statusClasses(review.status)" class="capitalize">
                                {{ review.status }}
                            </Badge>
                        </TableCell>
                        <TableCell>
                            {{ review.created_at }}
                        </TableCell>
                        <TableCell>
                            <Link
                                :href="route('admin.reviews.show', review.id)"
                                v-if="user.permissions.includes('edit reviews')"
                                class="text-blue-700 hover:underline dark:text-blue-400"
                            >
                                View
                            </Link>
                        </TableCell>
                    </TableRow>

                    <TableRow v-if="reviews.data.length === 0">
                        <TableCell colspan="8" class="text-muted-foreground py-4 text-center"> No reviews found. </TableCell>
                    </TableRow>
                </Deferred>
            </TableBody>
        </Table>

        <!-- table pagination -->
        <Pagination v-if="reviews" :meta="reviews.meta" :onPageChange="updatePage" />
    </AppLayout>
</template>
