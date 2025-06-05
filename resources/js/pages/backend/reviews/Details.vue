<script setup>
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Star } from 'lucide-vue-next';

const { review } = defineProps({ review: Object });
const breadcrumbs = [
    { title: 'Dashboard', href: route('admin.dashboard') },
    { title: 'Reviews', href: route('admin.reviews.index') },
    { title: 'Reviews Details' },
];

const form = useForm({
    status: review.data.status,
});

const updateReviewStatus = (val) => {
    form.status = val;
    form.put(route('admin.reviews.update', review.data.id), {
        onSuccess: () => {
            router.reload({ only: ['order'] });
        },
    });
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
</script>

<template>
    <Head title="Review Details" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageTitle
            >Review Details <Badge class="ms-2 capitalize" :class="statusClasses(review.data.status)">{{ review.data.status }}</Badge></template
        >

        <template #pageActions>
            <LoaderCircle v-if="form.processing" class="size-5 animate-spin" />

            <Select v-model="form.status" @update:modelValue="(val) => updateReviewStatus(val)">
                <SelectTrigger>
                    <SelectValue placeholder="Change Status" class="capitalize" />
                </SelectTrigger>
                <SelectContent class="capitalize">
                    <template v-for="(status, i) in ['pending', 'published', 'rejected']" :key="i">
                        <SelectItem :value="status">{{ status }}</SelectItem>
                    </template>
                </SelectContent>
            </Select>
        </template>

        <section class="@container space-y-5">
            <div class="grid grid-cols-1 @4xl:grid-cols-2">
                <div class="space-y-2">
                    <p class="font-medium">Reviewer</p>

                    <p class="text-muted-foreground mt-4 text-sm"><strong>Name: </strong> {{ review.data.customer.name }}</p>
                    <p class="text-muted-foreground text-sm"><strong>Email: </strong> {{ review.data.customer.email }}</p>
                    <p class="text-muted-foreground text-sm"><strong>Phone: </strong> {{ review.data.customer.phone }}</p>
                </div>

                <div class="space-y-2">
                    <p class="font-medium">Memorial</p>

                    <p class="text-muted-foreground mt-4 text-sm"><strong>Title: </strong>{{ review.data.memorial }}</p>
                </div>
            </div>

            <div class="space-y-2">
                <p class="font-medium">Rating</p>
                <div class="flex items-center gap-1">
                    <template v-for="i in 5" :key="i">
                        <Star
                            class="h-4 w-4"
                            :class="
                                i <= review.data.rating
                                    ? 'fill-yellow-500 stroke-yellow-500'
                                    : 'fill-stone-200 stroke-stone-200 dark:fill-stone-800 dark:stroke-stone-800'
                            "
                        />
                    </template>
                    <span class="text-muted-foreground ms-2 text-xs font-medium tracking-wide">({{ review.data.rating }})</span>
                </div>
            </div>

            <div class="space-y-2">
                <p class="font-medium">Review</p>
                <p class="text-muted-foreground">{{ review.data.review }}</p>
            </div>
        </section>
    </AppLayout>
</template>
