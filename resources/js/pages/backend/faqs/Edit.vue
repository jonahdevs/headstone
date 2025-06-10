<script setup>
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

// Your logic goes here
const props = defineProps({
    faq: Object,
});
const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'FAQs',
        href: '/admin/faqs',
    },
    {
        title: 'Edit FAQ',
    },
];

const form = useForm({
    question: props.faq.question,
    response: props.faq.response,
});

const handleSubmit = () => {
    form.put(route('admin.faqs.update', props.faq.id));
};
</script>

<template>
    <Head title="Create FAQs" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageTitle> Edit FAQ </template>

        <form @submit.prevent="handleSubmit" class="@container max-w-5xl space-y-4">
            <div class="space-y-2">
                <Label for="question">Frequest asked question <span class="text-red-600 dark:text-red-400">*</span></Label>
                <Textarea id="question" name="question" v-model="form.question" />
                <p class="text-muted-foreground text-sm">Enter a clear and concise question that customers commonly ask.</p>
                <InputError :message="form.errors.question" />
            </div>

            <div class="space-y-2">
                <Label for="response">Response <span class="text-red-600 dark:text-red-400">*</span></Label>
                <Textarea id="response" name="response" v-model="form.response" />
                <p class="text-muted-foreground text-sm">Provide a helpful and informative answer to the question above.</p>
                <InputError :message="form.errors.response" />
            </div>

            <div>
                <Button type="submit" :disabled="!form.isDirty || form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    {{ form.processing ? 'Processing...' : 'Save Changes' }}
                </Button>
            </div>
        </form>
    </AppLayout>
</template>
