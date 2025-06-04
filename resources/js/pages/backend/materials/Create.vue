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
const flash = computed(() => usePage().props.flash);

const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Materials',
        href: '/admin/materials',
    },
    {
        title: 'Create Material',
        href: '/admin/materials/create',
    },
];

const form = useForm({
    name: '',
    slug: '',
    sku: '',
    description: '',
    status: '',
});

const createNewMaterial = (status) => {
    form.status = status;

    form.post(route('admin.materials.store'), {
        onError: () => {
            toast.error('Failed to create material. Please check the form for errors.');
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
    <Head title="Create Material" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageTitle> Create Material </template>

        <template #pageActions>
            <Button @click="createNewMaterial('draft')" variant="secondary" :disabled="!form.isDirty || form.processing">Save</Button>
            <Button @click="createNewMaterial('published')" :disabled="!form.isDirty || form.processing">Publish</Button>
        </template>

        <form @submit.prevent="createNewMaterial" class="@container max-w-5xl space-y-5">
            <div class="grid gap-x-5 gap-y-5 @2xl:grid-cols-2">
                <!-- material name -->
                <div class="space-y-2">
                    <Label for="name">Full Name <span class="text-red-500">*</span></Label>
                    <Input id="name" v-model="form.name" type="text" />
                    <p class="text-muted-foreground text-sm">This is the display name of the material. Keep it clear and descriptive.</p>
                    <InputError :message="form.errors.name" />
                </div>

                <!-- material slug -->
                <div class="space-y-2">
                    <Label for="slug">Slug </Label>
                    <Input id="slug" v-model="form.slug" type="text" />
                    <p class="text-muted-foreground text-sm">Optional. Used in URLs. If left blank, it will be generated automatically.</p>
                    <InputError :message="form.errors.slug" />
                </div>

                <!-- material sku -->
                <div class="space-y-2">
                    <Label for="sku">SKU </Label>
                    <Input id="sku" v-model="form.sku" type="text" />
                    <p class="text-muted-foreground text-sm">Optional. Stock Keeping Unit (SKU) for inventory tracking or internal reference.</p>
                    <InputError :message="form.errors.sku" />
                </div>
            </div>

            <!-- material description -->
            <div class="space-y-2">
                <Label for="description">Description </Label>
                <Textarea id="description" v-model="form.description" class="min-h-32" />
                <p class="text-muted-foreground text-sm">Optional. Describe the appearance, use cases, or special characteristics of the material.</p>
                <InputError :message="form.errors.description" />
            </div>
        </form>
    </AppLayout>
</template>
