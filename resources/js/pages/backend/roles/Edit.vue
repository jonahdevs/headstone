<script setup>
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { computed, onMounted, watch } from 'vue';
import { toast } from 'vue-sonner';

const { role, groupedPermissions } = defineProps({
    role: Object,
    groupedPermissions: Object,
});
const flash = computed(() => usePage().props.flash);

// Prepare form with role's existing permissions (just names)
const form = useForm({
    permissions: [],
});

// On mount, fill in permissions from role
onMounted(() => {
    form.permissions = role.data.permissions.map((p) => p.name);
});

// Submit updated permissions
const handleSubmit = () => {
    form.put(route('admin.roles.update', role.data.id), {
        preserveScroll: true,
    });
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/admin/dashboard' },
    { title: 'Roles', href: '/admin/roles' },
    { title: 'Edit Role', href: `/admin/roles/${role.data.id}/edit` },
];

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
    <Head title="Edit Role" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageTitle>
            Edit <span class="capitalize">{{ role.data.name }}</span>
        </template>
        <form @submit.prevent="handleSubmit" class="max-w-5xl space-y-6">
            <section class="@container divide-y">
                <div v-for="(actions, resource) in groupedPermissions" :key="resource" class="grid py-3 @md:grid-cols-3">
                    <h2 class="mb-2 capitalize @md:col-span-1">{{ resource }}</h2>

                    <div class="grid gap-3 @md:col-span-2 @md:grid-cols-2 @xl:grid-cols-5">
                        <div v-for="(action, i) in actions" :key="i" class="flex items-center space-x-2">
                            <div class="flex h-6 shrink-0 items-center">
                                <div class="group grid size-4 grid-cols-1">
                                    <input
                                        :id="`${action}_${resource}`"
                                        :value="`${action} ${resource}`"
                                        v-model="form.permissions"
                                        aria-describedby="comments-description"
                                        type="checkbox"
                                        class="checked:border-primary checked:bg-primary indeterminate:border-primary indeterminate:bg-primary focus-visible:outline-primary col-start-1 row-start-1 appearance-none rounded-sm border focus-visible:outline-2 focus-visible:outline-offset-2 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto"
                                    />
                                    <svg
                                        class="stroke-primary-foreground pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center group-has-disabled:stroke-gray-950/25"
                                        viewBox="0 0 14 14"
                                        fill="none"
                                    >
                                        <path
                                            class="opacity-0 group-has-checked:opacity-100"
                                            d="M3 8L6 11L11 3.5"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                        <path
                                            class="opacity-0 group-has-indeterminate:opacity-100"
                                            d="M3 7H11"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </div>
                            </div>
                            <label :for="`${action}_${resource}`" class="text-muted-foreground text-sm capitalize">
                                {{ action }}
                            </label>
                        </div>
                    </div>
                </div>
            </section>

            <Button type="submit" :disabled="!form.isDirty || form.processing" class="min-w-44">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                <span> {{ form.processing ? 'Submitting...' : 'Update Role' }}</span>
            </Button>
        </form>
    </AppLayout>
</template>
