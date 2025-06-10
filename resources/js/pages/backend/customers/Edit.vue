<script setup>
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Link, LoaderCircle } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

const { customer } = defineProps({
    customer: Object,
});
const flash = computed(() => usePage().props.flash);
const avatar = ref(customer.data.avatar);

const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
    },
    {
        title: 'Customers',
        href: '/admin/customers',
    },
    {
        title: 'Edit Customer',
        href: `/admin/customers/${customer.data.id}/edit`,
    },
];

const form = useForm({
    name: customer.data.name,
    email: customer.data.email,
    phone: customer.data.phone,
    avatar: null,
    status: customer.data.status,
    country: customer.data.country,
    city: customer.data.city,
    address: customer.data.address,
});

const avatarPreview = (event) => {
    let file = event.target.files[0];

    if (file) {
        form.avatar = file;
        avatar.value = URL.createObjectURL(file);
    }
};

const clearAvatar = () => {
    avatar.value = customer.data.avatar;
    form.avatar = null;
};

const handleSubmit = async () => {
    form.transform((data) => ({
        ...data,
        _method: 'put',
    })).post(route('admin.customers.update', customer.data.id), {
        onError: () => {
            toast.error('Failed to update customer. Please check the form for errors.');
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
    <Head title="Update Customer" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageTitle> Update Customer </template>

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

            <!-- Address -->
            <p class="text-muted-foreground mt-7 mb-3 text-sm">Address</p>

            <div class="grid gap-x-5 gap-y-5 @2xl:grid-cols-2">
                <!-- Country -->
                <div class="space-y-2">
                    <Label for="country">Country</Label>
                    <Input id="country" v-model="form.country" type="text" placeholder="e.g. Kenya" />
                    <InputError :message="form.errors.country" />
                </div>

                <!-- City or State -->
                <div class="space-y-2">
                    <Label for="city">City/State</Label>
                    <Input id="city" v-model="form.city" type="text" placeholder="e.g. Thika" />
                    <InputError :message="form.errors.city" />
                </div>

                <!-- Address -->
                <div class="space-y-2">
                    <Label for="address">Address</Label>
                    <Input id="address" v-model="form.address" type="text" placeholder="e.g. Makongeni, Thika" />
                    <InputError :message="form.errors.address" />
                </div>
            </div>

            <div class="mt-5 flex items-center gap-5">
                <Button type="submit" :disabled="!form.isDirty || form.processing" class="min-w-40">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    <span> {{ form.processing ? 'Submitting...' : 'Save Changes' }} </span>
                </Button>
            </div>
        </form>
    </AppLayout>
</template>
