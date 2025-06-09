v
<script setup>
import { Link } from '@inertiajs/vue3';
import { AspectRatio } from './ui/aspect-ratio';

// Your logic goes here
defineProps({
    memorial: Object,
});
</script>

<template>
    <div class="group relative w-full overflow-hidden rounded-md border shadow-xs transition-all hover:shadow-sm">
        <!-- Image -->
        <AspectRatio :ratio="1 / 1" class="overflow-hidden">
            <img
                :src="memorial.image"
                alt="memorial image"
                class="h-full w-full rounded-t-md object-cover transition-all duration-300 group-hover:scale-105"
            />
        </AspectRatio>

        <!-- Content -->
        <div class="space-y-2 p-4">
            <div class="flex items-center justify-between">
                <Link :href="route('memorials.show', memorial.id)" class="text-base font-semibold group-hover:underline">
                    {{ memorial.title }}
                </Link>
            </div>

            <p class="text-muted-foreground line-clamp-2 text-sm">{{ memorial.description }}</p>

            <div class="flex items-center gap-3">
                <p :class="memorial.sale_price ? 'text-muted-foreground text-sm line-through' : 'text-sm font-medium'">
                    {{ memorial.price }}
                </p>
                <p class="text-sm font-medium text-green-600" v-if="memorial.sale_price">{{ memorial.sale_price }}</p>
            </div>
        </div>
    </div>
</template>
