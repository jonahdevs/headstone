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
import { Edit, PlusCircle, Search, Trash2 } from 'lucide-vue-next';
import { computed, onMounted, reactive, watch } from 'vue';
import { toast } from 'vue-sonner';

// Your logic goes here
const props = defineProps({
    faqs: Object,
    filters: Object,
});

const breadcrumbs = [
    { title: 'Dashboard', href: route('admin.dashboard') },
    { title: 'FAQs', href: route('admin.faqs.index') },
];

const flash = computed(() => usePage().props.flash);
const user = usePage().props.auth.user;

const filters = reactive({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
});

const applyFilters = () => {
    if (filters.status === 'all') filters.status = '';

    router.get(route('admin.faqs.index'), filters, { preserveState: true, preserveScroll: true, replace: true });
};

const updatePage = (page) => {
    router.get(
        route('admin.faqs.index'),
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
            return 'bg-black dark:bg-white';
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
    <Head title="Faqs List" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageTitle>FAQs</template>
        <template #pageActions>
            <Button v-if="user.permissions.includes('create faqs')" @click="router.visit(route('admin.faqs.create'))" class="text-xs uppercase">
                <PlusCircle class="h-4 w-4" />Add New Faq
            </Button>
        </template>

        <section class="space-y-6">
            <div class="flex flex-wrap items-center gap-4">
                <div class="relative w-full items-center md:w-64">
                    <Input
                        id="search"
                        type="text"
                        placeholder="Search..."
                        class="w-full max-w-80 pl-8"
                        v-model="filters.search"
                        @input="applyFilters"
                    />
                    <span class="absolute inset-y-0 start-0 flex items-center justify-center px-2">
                        <Search class="text-muted-foreground size-4" />
                    </span>
                </div>

                <Select v-model="filters.status" @update:modelValue="applyFilters">
                    <SelectTrigger>
                        <SelectValue placeholder="Select Status" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">All Status</SelectItem>
                        <SelectItem value="draft">Draft</SelectItem>
                        <SelectItem value="published">Published</SelectItem>
                    </SelectContent>
                </Select>

                <Button @click.prevent="clearFilters" variant="link" as="button"> Clear Filters </Button>
            </div>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>ID</TableHead>
                        <TableHead>Created By</TableHead>
                        <TableHead>Question</TableHead>
                        <TableHead>Response</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Created On</TableHead>
                        <TableHead>Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <Deferred data="faqs">
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
                                </TableRow>
                            </template>
                        </template>
                        <TableRow v-for="faq in faqs.data" :key="faq.id">
                            <TableCell>#{{ faq.id }}</TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <p class="line-clamp-1 w-full">{{ faq.created_by }}</p>
                                </div>
                            </TableCell>
                            <TableCell>
                                <p class="line-clamp-3 whitespace-break-spaces">{{ faq.question ?? 'null' }}</p>
                            </TableCell>
                            <TableCell>
                                <p class="line-clamp-3 max-w-[600px] whitespace-break-spaces">{{ faq.response ?? 'null' }}</p>
                            </TableCell>
                            <TableCell>
                                <Badge :class="statusClasses(faq.status)" class="capitalize">{{ faq.status }}</Badge>
                            </TableCell>
                            <TableCell>{{ faq.date }}</TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Button
                                        v-if="user.permissions.includes('edit faqs')"
                                        @click="router.visit(route('admin.faqs.edit', faq.id))"
                                        size="sm"
                                    >
                                        <Edit class="h-4 w-4" />
                                    </Button>
                                    <Button
                                        v-if="user.permissions.includes('delete faqs')"
                                        variant="destructive"
                                        size="sm"
                                        @click="deleteFaqs(faq.id)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>

                        <TableRow v-if="faqs.data.length === 0">
                            <TableCell colspan="7" class="text-muted-foreground py-4 text-center"> No FAQs found. </TableCell>
                        </TableRow>
                    </Deferred>
                </TableBody>
            </Table>

            <!-- table pagination -->
            <Pagination v-if="faqs" :meta="faqs.meta" :onPageChange="updatePage" />
        </section>
    </AppLayout>
</template>
