<script setup>
import PageHeaderSection from '@/components/PageHeaderSection.vue';
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { Skeleton } from '@/components/ui/skeleton';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Deferred, Head } from '@inertiajs/vue3';

// Your logic goes here
defineProps({
    faqs: Object,
});

const pageSectionLinks = [
    {
        title: 'Home',
        href: route('home'),
    },
    {
        title: 'Faqs',
        href: '',
    },
];

const defaultValue = 'item-1';
</script>

<template>
    <Head title="FAQs" />

    <GuestLayout>
        <PageHeaderSection :title="'FAQs'" :links="pageSectionLinks" />

        <section class="container mx-auto max-w-7xl space-y-8 px-4 py-10 md:px-8 lg:py-16 xl:px-0">
            <div class="flex flex-col gap-3">
                <Accordion type="single" class="w-full" collapsible :default-value="defaultValue">
                    <Deferred data="faqs">
                        <template #fallback>
                            <div class="space-y-4">
                                <div v-for="i in 4" :key="i" class="space-y-2 rounded-lg border p-4">
                                    <Skeleton class="h-4 w-3/4 rounded" />
                                    <Skeleton class="h-3 w-full rounded" />
                                    <Skeleton class="h-3 w-5/6 rounded" />
                                </div>
                            </div>
                        </template>

                        <AccordionItem v-for="faq in faqs.data" :key="faq.value" :value="faq.value">
                            <AccordionTrigger class="text-base font-medium">{{ faq.question }}</AccordionTrigger>
                            <AccordionContent class="text-muted-foreground">
                                {{ faq.response }}
                            </AccordionContent>
                        </AccordionItem>
                    </Deferred>
                </Accordion>
            </div>
        </section>
    </GuestLayout>
</template>
