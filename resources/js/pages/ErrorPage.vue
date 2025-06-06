<script setup>
import { Button } from '@/components/ui/button';
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({ status: Number });

const title = computed(() => {
    return (
        {
            503: 'Service Unavailable',
            500: 'Server Error',
            404: 'Page Not Found',
            403: 'Forbidden',
        }[props.status] || 'Unknown Error'
    );
});

const description = computed(() => {
    return {
        503: 'Sorry, we are doing some maintenance. Please check back soon.',
        500: 'Whoops, something went wrong on our servers.',
        404: 'Sorry, the page you are looking for could not be found.',
        403: 'Sorry, you are forbidden from accessing this page.',
    }[props.status];
});
</script>

<template>
    <Head :title="status" />

    <div class="bg-primary-foreground flex min-h-screen flex-col items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8 text-center">
            <div class="mb-8">
                <h2 class="mt-6 text-6xl font-extrabold">{{ status }}</h2>
                <p class="mt-2 text-3xl font-bold">{{ title }}</p>
                <p class="text-muted-foreground mt-2 text-sm">{{ description }}</p>
            </div>
            <div class="mt-8">
                <Button @click="router.visit(route('home'))" class="size-lg">Go Back Home</Button>
            </div>
        </div>
        <div class="mt-16 w-full max-w-2xl">
            <div class="relative">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="text-muted-foreground bg-muted px-2 text-sm"> If you think this is a mistake, please contact support </span>
                </div>
            </div>
        </div>
    </div>
</template>
