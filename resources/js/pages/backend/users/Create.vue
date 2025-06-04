<script setup>
import avatarPlaceholder from '@/assets/images/avatar.webp';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Combobox,
    ComboboxAnchor,
    ComboboxEmpty,
    ComboboxGroup,
    ComboboxInput,
    ComboboxItem,
    ComboboxItemIndicator,
    ComboboxList,
    ComboboxTrigger,
} from '@/components/ui/combobox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import { cn } from '@/lib/utils';
import { Deferred, Head, useForm, usePage } from '@inertiajs/vue3';
import { Check, ChevronsUpDown, Link, LoaderCircle, Search } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

const avatar = ref(avatarPlaceholder);
const flash = computed(() => usePage().props.flash);

defineProps({
    roles: Object,
    permissions: Object,
});

const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
    },
    {
        title: 'Users',
        href: '/admin/users',
    },
    {
        title: 'Create User',
        href: '/admin/users/create',
    },
];

const form = useForm({
    name: '',
    email: '',
    phone: '',
    avatar: null,
    status: 'active',
    roles: '',
    permissions: '',
});

const avatarPreview = (event) => {
    let file = event.target.files[0];

    if (file) {
        form.avatar = file;
        avatar.value = URL.createObjectURL(file);
    }
};

const clearAvatar = () => {
    avatar.value = avatarPlaceholder;
    form.avatar = null;
};

const handleSubmit = async () => {
    form.post(route('admin.users.store'), {
        onError: () => {
            toast.error('Failed to create user. Please check the form for errors.');
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
    <Head title="Create User" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageTitle> Create User </template>

        <form @submit.prevent="handleSubmit" class="@container max-w-5xl">
            <!-- Avatar -->
            <div class="space-y-2">
                <div class="flex items-center gap-5">
                    <img :src="avatar" alt="avatar" class="size-16 rounded-full" />

                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
                            <Button variant="link" as="label" for="avatar"><Link class="size-4" /> Upload Avatar</Button>
                            <Button v-if="form.avatar" @click="clearAvatar" type="button" variant="link" class="text-sm">Clear selected image</Button>
                        </div>

                        <p class="text-muted-foreground text-sm">
                            Please upload a .jpg or .png file with a minimum dimension of 400w x 400h not exceeding 5MB
                        </p>
                    </div>

                    <input id="avatar" type="file" class="hidden" @input="avatarPreview($event)" />
                </div>
                <InputError :message="form.errors.avatar" />
            </div>

            <!-- personal Information -->
            <p class="text-muted-foreground mt-7 mb-3 text-sm">Personal Information</p>

            <div class="grid gap-x-5 gap-y-5 @2xl:grid-cols-2">
                <!-- full name -->
                <div class="space-y-2">
                    <Label for="name">Full Name <span class="text-red-500">*</span></Label>
                    <Input id="name" v-model="form.name" type="text" placeholder="e.g. John Doe" />
                    <InputError :message="form.errors.name" />
                </div>

                <!-- email address -->
                <div class="space-y-2">
                    <Label for="email">Email Address <span class="text-red-500">*</span></Label>
                    <Input id="email" v-model="form.email" type="email" placeholder="e.g. johndoe@example.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <!-- phone Number -->
                <div class="space-y-2">
                    <Label for="phone">Phone Number <span class="text-red-500">*</span></Label>
                    <Input id="phone" v-model="form.phone" type="text" placeholder="e.g. +254 7xxxxxxxx" />
                    <InputError :message="form.errors.phone" />
                </div>

                <!-- Status -->
                <div class="space-y-2">
                    <Label for="status">Status </Label>
                    <Select v-model="form.status" id="status">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select a status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="active">Active</SelectItem>
                            <SelectItem value="inactive">Inactive</SelectItem>
                            <SelectItem value="banned">Banned</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.status" />
                </div>
            </div>

            <!-- roles and permissions -->
            <p class="text-muted-foreground mt-7 mb-3 text-sm">Roles and Permissions</p>

            <div class="grid gap-x-5 gap-y-5 @2xl:grid-cols-2">
                <!-- roles -->
                <div class="grid gap-2">
                    <Label for="role">Role <span class="text-red-500">*</span></Label>
                    <Combobox v-model="form.roles" by="name" multiple>
                        <ComboboxAnchor as-child>
                            <ComboboxTrigger as-child class="h-fit">
                                <Button variant="outline" class="flex w-full flex-wrap items-center gap-2">
                                    <div v-if="form.roles && form.roles.length" class="flex flex-wrap gap-1">
                                        <Badge v-for="r in form.roles" :key="r.id" class="capitalize">{{ r.name }} </Badge>
                                    </div>
                                    <p v-else>Select role</p>

                                    <ChevronsUpDown class="ms-auto h-4 w-4 shrink-0 opacity-50" />
                                </Button>
                            </ComboboxTrigger>
                        </ComboboxAnchor>

                        <ComboboxList class="w-full">
                            <div class="relative w-full max-w-sm items-center">
                                <ComboboxInput class="h-10 rounded-none border-0 border-b pl-1 focus-visible:ring-0" placeholder="Select role..." />
                                <span class="absolute inset-y-0 start-0 flex items-center justify-center px-3">
                                    <Search class="text-muted-foreground h-4 w-4" />
                                </span>
                            </div>

                            <ComboboxEmpty> No roles found. </ComboboxEmpty>

                            <ComboboxGroup>
                                <Deferred data="roles">
                                    <template #fallback>
                                        <div>Loading...</div>
                                    </template>
                                    <ComboboxItem v-for="role in roles" :key="role.id" :value="role" class="capitalize">
                                        {{ role.name }}

                                        <ComboboxItemIndicator>
                                            <Check :class="cn('ml-auto h-4 w-4')" />
                                        </ComboboxItemIndicator>
                                    </ComboboxItem>
                                </Deferred>
                            </ComboboxGroup>
                        </ComboboxList>
                    </Combobox>
                    <InputError :message="form.errors.roles" />
                </div>

                <!-- permissions -->
                <div class="space-y-2">
                    <Label for="permissions">Permissions </Label>
                    <Combobox v-model="form.permissions" by="name" multiple>
                        <ComboboxAnchor as-child>
                            <ComboboxTrigger as-child class="h-fit">
                                <Button variant="outline" class="flex w-full flex-wrap items-center gap-2">
                                    <div v-if="form.permissions && form.permissions.length" class="flex flex-wrap gap-1">
                                        <Badge v-for="p in form.permissions" :key="p.id" class="capitalize">{{ p.name }} </Badge>
                                    </div>
                                    <p v-else>Select permissions</p>

                                    <ChevronsUpDown class="ms-auto h-4 w-4 shrink-0 opacity-50" />
                                </Button>
                            </ComboboxTrigger>
                        </ComboboxAnchor>

                        <ComboboxList class="w-full">
                            <div class="relative w-full max-w-sm items-center">
                                <ComboboxInput
                                    class="h-10 rounded-none border-0 border-b pl-1 focus-visible:ring-0"
                                    placeholder="Select permission..."
                                />
                                <span class="absolute inset-y-0 start-0 flex items-center justify-center px-3">
                                    <Search class="text-muted-foreground h-4 w-4" />
                                </span>
                            </div>

                            <ComboboxEmpty> No permission found. </ComboboxEmpty>

                            <ComboboxGroup class="max-h-72 overflow-y-auto">
                                <Deferred data="roles">
                                    <template #fallback>
                                        <div>Loading...</div>
                                    </template>
                                    <ComboboxItem v-for="permission in permissions" :key="permission.id" :value="permission" class="capitalize">
                                        {{ permission.name }}
                                        <ComboboxItemIndicator>
                                            <Check :class="cn('ml-auto h-4 w-4')" />
                                        </ComboboxItemIndicator>
                                    </ComboboxItem>
                                </Deferred>
                            </ComboboxGroup>
                        </ComboboxList>
                    </Combobox>
                    <InputError :message="form.errors.permissions" />
                </div>
            </div>

            <div class="mt-5 flex items-center gap-5">
                <Button type="submit" :disabled="!form.isDirty || form.processing" class="min-w-40">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    <span v-if="!form.processing"> {{ form.processing ? 'Submitting...' : 'Create New User' }} </span>
                </Button>
            </div>
        </form></AppLayout
    >
</template>
