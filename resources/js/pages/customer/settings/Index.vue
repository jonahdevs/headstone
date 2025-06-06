<script setup>
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import CustomerLayout from '@/layouts/CustomerLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Link, LoaderCircle, Lock, User } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

// Your logic goes here
const page = usePage();
const user = computed(() => page.props.auth.user);
const avatar = ref(user.value.avatar);
const flash = computed(() => page.props.flash);

const breadcrumbs = [
    {
        title: 'Home',
        href: '/',
    },
    {
        title: 'Account',
        href: '/customer/account',
    },
];

const tabs = ref('account');

const form = useForm({
    name: user.value.name,
    email: user.value.email,
    phone: user.value.phone,
    avatar: '',
    country: user.value.country,
    city: user.value.city,
    address: user.value.address,
});

const password_form = useForm({
    password: '',
    password_confirmation: '',
});

const handleAccountUpdate = () => {
    form.transform((data) => ({
        ...data,
        _method: 'put',
    })).post(route('customer.account.update'));
};

const handlePasswordUpdate = () => {
    password_form.put(route('customer.account.password'), {
        preserveState: true,
        onSuccess: () => {
            password_form.reset();
        },
    });
};

const avatarPreview = (event) => {
    let file = event.target.files[0];

    if (file) {
        avatar.value = URL.createObjectURL(file);
    }
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
    <Head title="Account Settings" />

    <CustomerLayout :pageTitle="'My Account'" :breadcrumbs="breadcrumbs">
        <div class="flex flex-wrap items-center gap-4">
            <button
                @click="tabs = 'account'"
                class="flex items-center gap-2 rounded-md px-5 py-2 text-sm"
                :class="
                    tabs == 'account' ? 'bg-blue-500 text-white' : 'hover:bg-blue-100 hover:text-blue-600 dark:bg-blue-950 dark:hover:text-blue-400'
                "
            >
                <User class="h-5 w-5" /> Account
            </button>
            <button
                @click="tabs = 'security'"
                class="flex items-center gap-2 rounded-md px-5 py-2 text-sm"
                :class="
                    tabs == 'security' ? 'bg-blue-500 text-white' : 'hover:bg-blue-100 hover:text-blue-600 dark:bg-blue-950 dark:hover:text-blue-400'
                "
            >
                <Lock class="h-5 w-5" /> Security
            </button>
        </div>

        <form @submit.prevent="handleAccountUpdate" class="@container" v-show="tabs == 'account'">
            <!-- avatar -->
            <div class="space-y-2">
                <div class="flex items-center gap-5">
                    <img :src="avatar" alt="avatar" class="h-16 w-16 rounded-full" />

                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
                            <Button variant="link" as="label" for="avatar"><Link class="size-4" /> Upload Avatar</Button>
                            <button @click="avatar = user.avatar" type="button" class="text-sm font-medium">Clear selected image</button>
                        </div>

                        <p class="text-muted-foreground text-sm">
                            Please upload a .jpg or .png file with a minimum dimension of 400w x 400h not exceeding 5MB
                        </p>
                    </div>

                    <input id="avatar" type="file" class="hidden" @change="avatarPreview($event)" @input="form.avatar = $event.target.files[0]" />
                </div>
                <InputError :message="form.errors.avatar" />
            </div>

            <!-- personal information  -->
            <p class="text-muted-foreground mt-5 mb-3 text-sm">Personal Information</p>

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
            </div>

            <!-- address -->
            <p class="text-muted-foreground mt-5 mb-3 text-sm">Address</p>

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
                <Button type="submit" :disabled="!form.isDirty || form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    {{ form.processing ? 'Submitting' : 'Save Changes' }}
                </Button>
            </div>
        </form>

        <form @submit.prevent="handlePasswordUpdate" v-show="tabs == 'security'" class="@container space-y-4">
            <div class="space-y-2 rounded-md bg-red-100 p-5 text-red-500 dark:bg-red-950">
                <p class="text-sm font-medium">Ensure that these requirements are met</p>
                <ul class="list-inside list-disc text-sm">
                    <li>Minimum 8 characters long</li>
                    <li>Atleast one symbol</li>
                    <li>Atleast one character</li>
                </ul>
            </div>

            <div class="grid gap-x-5 gap-y-5 @2xl:grid-cols-2">
                <!-- New Password -->
                <div class="space-y-2">
                    <Label for="password">New Password <span class="text-red-500">*</span></Label>
                    <Input id="password" v-model="password_form.password" type="password" />
                    <InputError :message="password_form.errors.password" />
                </div>

                <!-- Password Confirmation -->
                <div class="space-y-2">
                    <Label for="password_confirmation">Confirm New Password <span class="text-red-500">*</span></Label>
                    <Input id="password_confirmation" v-model="password_form.password_confirmation" type="password" />
                </div>
            </div>

            <div class="mt-5 flex items-center gap-5">
                <Button type="submit" :disabled="!password_form.isDirty || password_form.processing">
                    <LoaderCircle v-if="password_form.processing" class="h-4 w-4 animate-spin" />
                    {{ password_form.processing ? 'Submitting' : 'Change Password' }}
                </Button>
            </div>
        </form>
    </CustomerLayout>
</template>
