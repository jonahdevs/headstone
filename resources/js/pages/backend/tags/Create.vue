<script setup>
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import { toast } from 'vue-sonner';

// Your logic goes here
const { tag } = defineProps({
    tag: Object,
});
const flash = computed(() => usePage().props.flash);
const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
    },
    {
        title: 'Tags',
        href: '/admin/tags',
    },
    {
        title: 'Create Tag',
        href: '/admin/tags/create',
    },
];

const form = useForm({
    name: '',
    slug: '',
    description: '',
    status: '',
});

const createNewTag = (status) => {
    form.status = status;

    form.post(route('admin.tags.store'), {
        onError: () => {
            toast.error('Failed to create tag. Please check the form for errors.');
        },
    });
};

watch(
    () => flash.value.message,
    (msg) => {
        if (msg && msg.type === 'success') {
            toast.success(msg.body);
        } else if (msg && msg.type === 'error') {
            toast.error(msg.body);
        }
    },
);
</script>

<template>
    <Head title="Create Tag" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageTitle> Create Tag </template>

        <template #pageActions>
            <Button @click="createNewTag('draft')" variant="secondary" :disabled="!form.isDirty || form.processing">Save</Button>
            <Button @click="createNewTag('published')" :disabled="!form.isDirty || form.processing">Publish</Button>
        </template>

        <form @submit.prevent="createNewTag" class="@container max-w-5xl space-y-5">
            <div class="grid gap-x-5 gap-y-5 @2xl:grid-cols-2">
                <!-- tag name -->
                <div class="space-y-2">
                    <Label for="name">Full Name <span class="text-red-500">*</span></Label>
                    <Input id="name" v-model="form.name" type="text" />
                    <p class="text-muted-foreground text-sm">This is the display name for the tag.</p>
                    <InputError :message="form.errors.name" />
                </div>

                <!-- tag slug -->
                <div class="space-y-2">
                    <Label for="slug">Slug </Label>
                    <Input id="slug" v-model="form.slug" type="text" />
                    <p class="text-muted-foreground text-sm">Optional. If left blank, a URL-friendly slug will be generated automatically.</p>
                    <InputError :message="form.errors.slug" />
                </div>
            </div>

            <!-- tag description -->
            <div class="space-y-2">
                <Label for="description">Description </Label>
                <Textarea id="description" v-model="form.description" class="min-h-32" />
                <p class="text-muted-foreground text-sm">Optional. Helpful for internal management or SEO descriptions.</p>
                <InputError :message="form.errors.description" />
            </div>
        </form>
    </AppLayout>
</template>
