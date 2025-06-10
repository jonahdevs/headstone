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

const { category } = defineProps({
    category: Object,
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
        title: 'Categories',
        href: '/admin/categories',
    },
    {
        title: 'Edit Category',
        href: `/admin/categories/${category.data.id}/edit`,
    },
];

const form = useForm({
    name: category.data.name,
    slug: category.data.slug,
    description: category.data.description,
    status: category.data.status,
});

const updateCategory = (status) => {
    form.transform((data) => ({
        ...data,
        status: status,
    })).put(route('admin.categories.update', category.data.id), {
        onError: () => {
            toast.error('Failed to update category. Please check the form for errors.');
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
    <Head title="Edit Category" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageTitle> Edit Category </template>

        <template #pageActions>
            <Button
                @click="
                    updateCategory(form.status);
                    saving = true;
                "
                variant="secondary"
                :disabled="!form.isDirty || form.processing"
            >
                <LoaderCircle v-if="saving && form.processing" class="h-4 w-4 animate-spin" />
                <span>{{ saving && form.processing ? 'Processing...' : 'Save Changes' }}</span>
            </Button>

            <Button
                @click="
                    updateCategory('published');
                    publishing = true;
                "
                v-if="form.status === 'draft'"
                :disabled="form.processing"
            >
                <LoaderCircle v-if="publishing && form.processing" class="h-4 w-4 animate-spin" />
                <span>{{ publishing && form.processing ? 'Processing...' : 'Publish' }}</span>
            </Button>

            <Button
                @click="
                    updateCategory('draft');
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

        <form @submit.prevent="updateCategory" class="@container max-w-5xl">
            <div class="grid gap-x-5 gap-y-5 @2xl:grid-cols-2">
                <!-- category name -->
                <div class="space-y-2">
                    <Label for="name">Full Name <span class="text-red-500">*</span></Label>
                    <Input id="name" v-model="form.name" type="text" />
                    <p class="text-muted-foreground text-sm">This is the display name for the category.</p>
                    <InputError :message="form.errors.name" />
                </div>

                <!-- category slug -->
                <div class="space-y-2">
                    <Label for="slug">Slug </Label>
                    <Input id="slug" v-model="form.slug" type="text" />
                    <p class="text-muted-foreground text-sm">Optional. If left blank, a URL-friendly slug will be generated automatically.</p>
                    <InputError :message="form.errors.slug" />
                </div>
            </div>

            <!-- category description -->
            <div class="mt-5 space-y-2">
                <Label for="description">Description </Label>
                <Textarea id="description" v-model="form.description" class="min-h-32" />
                <p class="text-muted-foreground text-sm">Optional. Helpful for internal management or SEO descriptions.</p>
                <InputError :message="form.errors.description" />
            </div>
        </form>
    </AppLayout>
</template>
