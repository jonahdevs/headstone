<script setup lang="ts">
import { EllipsisVertical, FileText, Rocket, ShoppingBag, TrendingDown, TrendingUp, Users } from 'lucide-vue-next';

const props = defineProps<{
    icon: 'Rocket' | 'ShoppingBag' | 'FileText' | 'Users';
    title: string;
    value: string | number;
    trend: string;
    trendDirection: 'up' | 'down';
}>();

const iconMap = {
    Rocket,
    ShoppingBag,
    FileText,
    Users,
};
</script>

<template>
    <div class="space-y-4 rounded-xl border p-5">
        <div class="flex items-start justify-between">
            <p class="text-muted-foreground text-sm font-medium tracking-wider uppercase">{{ title }}</p>
            <EllipsisVertical class="text-muted-foreground h-5 w-5" />
        </div>
        <div class="flex items-start gap-3">
            <div class="rounded-md bg-blue-50 p-2 text-blue-600 dark:bg-blue-950 dark:text-blue-500">
                <component :is="iconMap[icon]" class="h-5 w-5" />
            </div>
            <div class="space-y-1">
                <p class="text-2xl font-bold">{{ value }}</p>
                <div class="text-muted-foreground flex items-center gap-3 text-xs">
                    <div :class="['flex items-center gap-2', trendDirection === 'up' ? 'text-green-500' : 'text-red-500']">
                        <component :is="trendDirection === 'up' ? TrendingUp : TrendingDown" class="h-4 w-4" />
                        {{ trend }}
                    </div>
                    <span>from last week</span>
                </div>
            </div>
        </div>
    </div>
</template>
