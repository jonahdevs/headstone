<script setup>
import { Skeleton } from './ui/skeleton';
</script>

<template>
    <div class="space-y-4 rounded-xl border p-5">
        <div class="flex items-start justify-between">
            <Skeleton class="h-4 w-20" />
            <Skeleton class="h-6 w-1" />
        </div>
        <div class="flex items-start gap-3">
            <Skeleton class="size-9 rounded-md" />

            <div class="space-y-2">
                <Skeleton class="h-6 w-20" />
                <Skeleton class="h-3 w-28" />
            </div>
        </div>
    </div>
</template>
