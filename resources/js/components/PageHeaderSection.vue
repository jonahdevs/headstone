<script setup>
import graveStoneImg from '@/assets/images/gravestone.jpg';
import { Link, usePage } from '@inertiajs/vue3';

// Your logic goes here
const props = defineProps({
    title: String,
    links: Array,
});

const page = usePage();
</script>

<template>
    <section class="relative h-[25svh] w-full md:h-[20svh]">
        <img :src="graveStoneImg" alt="grave stone" class="h-full w-full object-cover object-right" />

        <div class="absolute inset-0 flex flex-col justify-center bg-linear-to-r from-stone-800 to-transparent to-100%">
            <div class="container mx-auto max-w-7xl space-y-2 px-4 md:px-8 xl:px-0">
                <div class="flex w-fit items-center overflow-hidden rounded-md">
                    <template v-for="(link, i) in links" :key="i">
                        <Link
                            :href="page.url !== link.href ? link.href : ''"
                            class="bg-stone-600 px-5 py-2 text-xs text-white uppercase transition-transform hover:underline"
                        >
                            {{ link.title }}
                        </Link>
                    </template>
                </div>

                <h2 class="text-4xl font-bold text-white">{{ title }}</h2>
            </div>
        </div>
    </section>
</template>
