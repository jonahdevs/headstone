<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Toaster } from '@/components/ui/sonner';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import 'vue-sonner/style.css';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppLayout>
        <section class="min-h-[90svh] space-y-8 px-4 py-12 md:px-8">
            <div class="mb-8 flex flex-wrap items-center gap-4">
                <!-- page title and breadcrumbs -->
                <div class="space-y-2">
                    <h3 class="text-2xl font-semibold"><slot name="pageTitle" /></h3>
                    <Breadcrumbs v-if="breadcrumbs" :breadcrumbs="breadcrumbs" />
                </div>

                <!-- page actions -->
                <div class="ms-auto flex flex-wrap items-center gap-3">
                    <slot name="pageActions" />
                </div>
            </div>

            <slot />
        </section>

        <Toaster richColors closeButton />
    </AppLayout>
</template>
