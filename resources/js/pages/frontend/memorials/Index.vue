<script setup>
import CustomMinMaxSlider from '@/components/CustomMinMaxSlider.vue';
import MemorialCard from '@/components/MemorialCard.vue';
import NoDataFound from '@/components/NoDataFound.vue';
import PageHeaderSection from '@/components/PageHeaderSection.vue';
import Pagination from '@/components/Pagination.vue';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Skeleton } from '@/components/ui/skeleton';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Deferred, Head, router, usePage } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { ChevronDown } from 'lucide-vue-next';
import { reactive, ref } from 'vue';

// Your logic goes here
const props = defineProps({
    memorials: Object,
    memorials_pagination: Object,
    category: Object,
    categories: Object,
    filters: Object,
});

const pageSectionLinks = [
    {
        title: 'Home',
        href: route('home'),
    },
    {
        title: 'Memorials',
        href: '',
    },
];

const filters = reactive({
    style: props.filters?.style || '',
    min_price: props.filters?.min_price || 0,
    max_price: props.filters?.max_price || 100000,
});

const page = usePage();
const range = ref({
    min: filters.min_price,
    max: filters.max_price,
});

const applyFilters = () => {
    if (filters.style === 'all') filters.style = '';

    router.get(route('memorials'), filters, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};
const debouncedApplyFilters = useDebounceFn((val) => {
    filters.min_price = val.min;
    filters.max_price = val.max;
    applyFilters();
}, 1000);

const clearFilters = () => {
    filters.style = '';
    filters.min_price = 0;
    filters.max_price = 100000;
    applyFilters();
};

const updatePage = (page) => {
    router.get(
        route('memorials'),
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
    <Head title="Memorials" />

    <GuestLayout>
        <PageHeaderSection :title="category ? category.name : 'All Memorials'" :links="pageSectionLinks" />

        <section class="container mx-auto max-w-7xl space-y-6 px-4 py-16 md:px-8 xl:px-0">
            <div class="flex flex-wrap gap-4">
                <!-- filter customer by status -->
                <Select
                    v-if="!page.url.includes('category')"
                    v-model="filters.style"
                    @update:modelValue="applyFilters"
                    placeholder="Select Status"
                    class="w-40"
                >
                    <SelectTrigger>
                        <SelectValue placeholder="Filter by Style" />
                    </SelectTrigger>
                    <SelectContent class="capitalize">
                        <SelectItem value="all">All Status</SelectItem>
                        <template v-for="(category, i) in categories" :key="i">
                            <SelectItem :value="category">{{ category }}</SelectItem>
                        </template>
                    </SelectContent>
                </Select>

                <DropdownMenu>
                    <DropdownMenuTrigger
                        class="border-input bg-card pointer text-muted-foreground flex items-center gap-4 rounded-md border px-3 py-1.5 text-sm shadow-xs"
                    >
                        Price
                        <ChevronDown class="h-4 w-4" />
                    </DropdownMenuTrigger>
                    <DropdownMenuContent class="p-5">
                        <CustomMinMaxSlider
                            :min="0"
                            :max="100000"
                            :step="100"
                            v-model="range"
                            @update:modelValue="(val) => debouncedApplyFilters(val)"
                        />
                    </DropdownMenuContent>
                </DropdownMenu>

                <Button @click.prevent="clearFilters" variant="link" as="button"> Clear Filters </Button>
            </div>

            <div class="flex flex-wrap gap-4">
                <Deferred data="memorials">
                    <template #fallback>
                        <template v-for="i in 4" :key="i">
                            <div
                                class="relative w-full max-w-72 animate-pulse overflow-hidden rounded-md border shadow-xs transition-all hover:shadow-sm"
                            >
                                <!-- Image placeholder -->
                                <Skeleton class="aspect-square rounded-t-md" />

                                <!-- Content placeholder -->
                                <div class="space-y-2 p-4">
                                    <!-- Title placeholder -->
                                    <Skeleton class="h-4 w-3/4 rounded" />

                                    <!-- Description placeholders -->
                                    <div class="space-y-1">
                                        <Skeleton class="h-3 w-full rounded" />
                                        <Skeleton class="h-3 w-5/6 rounded" />
                                    </div>

                                    <!-- Price placeholder -->
                                    <div class="flex items-center gap-3">
                                        <Skeleton class="h-4 w-12 rounded" />
                                        <Skeleton class="h-4 w-12 rounded" />
                                    </div>
                                </div>
                            </div>
                        </template>
                    </template>

                    <template v-for="memorial in memorials.data" :key="memorial.id">
                        <MemorialCard :memorial="memorial" />
                    </template>

                    <template v-if="memorials.data.length < 1">
                        <section class="text-muted-foreground flex w-full flex-col items-center justify-center gap-2">
                            <NoDataFound />
                        </section>
                    </template>
                </Deferred>
            </div>

            <Pagination v-if="memorials" :meta="memorials.meta" :onPageChange="updatePage" />
        </section>
    </GuestLayout>
</template>
