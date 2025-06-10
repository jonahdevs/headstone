<script setup>
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

// Your logic goes here
const { material } = defineProps({
    material: Object,
});
const flash = computed(() => usePage().props.flash);
const saving = ref(false);
const publishing = ref(false);
const unPublishing = ref(false);

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
        title: 'Edit Material',
        href: `/admin/materials/${material.data.id}/edit`,
    },
];

const form = useForm({
    name: material.data.name,
    slug: material.data.slug,
    sku: material.data.sku,
    description: material.data.description,
    status: material.data.status,
});

const updateMaterial = (status) => {
    form.transform((data) => ({
        ...data,
        status: status,
    })).put(route('admin.materials.update', material.data.id), {
        onError: () => {
            toast.error('Failed to update material. Please check the form for errors.');
        },
        onFinish: () => {
            saving.value = false;
            publishing.value = false;
            unPublishing.value = false;
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
    <Head title="Edit Material" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageTitle> Edit Material </template>

        <template #pageActions>
            <Button
                @click="
                    updateMaterial(form.status);
                    saving = true;
                "
                variant="secondary"
                :disabled="!form.isDirty || form.processing"
            >
                <LoaderCircle v-if="saving && form.processing" class="h-4 w-4 animate-spin" />
                <span>{{ saving && form.processing ? 'Processing...' : 'Save Changes' }}</span></Button
            >

            <Button
                @click="
                    updateMaterial('published');
                    publishing = true;
                "
                v-if="form.status === 'draft'"
                :disabled="form.processing"
            >
                <LoaderCircle v-if="publishing && form.processing" class="h-4 w-4 animate-spin" />
                <span>{{ publishing && form.processing ? 'Processing...' : 'Publish' }}</span></Button
            >

            <Button
                @click="
                    updateMaterial('draft');
                    unPublishing = true;
                "
                v-else
                :disabled="form.processing"
                variant="destructive"
            >
                <LoaderCircle v-if="unPublishing && form.processing" class="h-4 w-4 animate-spin" />
                <span>{{ unPublishing && form.processing ? 'Processing...' : 'Un Publish' }}</span>
            </Button>
        </template>

        <form @submit.prevent="updateMaterial" class="@container max-w-5xl space-y-5">
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
