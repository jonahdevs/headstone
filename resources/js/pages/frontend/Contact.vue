<script setup>
import InputError from '@/components/InputError.vue';
import PageHeaderSection from '@/components/PageHeaderSection.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Mail, MapPin, PhoneCall } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: '',
});

const pageSectionLinks = [
    {
        title: 'Home',
        href: route('home'),
    },
    {
        title: 'Contact',
        href: '',
    },
];

const handleSubmit = () => {
    form.post(route('contact.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Thank you! Your message has been received. We’ll get back to you shortly.');
            form.reset();
        },
        onError: () => {
            toast.error('Failed to submit. Please check the form for errors.');
        },
    });
};
</script>

<template>
    <Head title="Contact Us" />

    <GuestLayout>
        <PageHeaderSection :title="'Contact'" :links="pageSectionLinks" />

        <section class="container mx-auto my-10 max-w-5xl px-4 md:px-8 xl:px-0">
            <section class="@container/summary flex max-w-5xl flex-col gap-5 rounded-xl border p-2 shadow-xs md:flex-row md:gap-8">
                <div class="bg-primary text-primary-foreground w-full space-y-7 rounded-lg p-4 md:w-72 @2xl/summary:p-8">
                    <div>
                        <h1 class="text-lg font-bold">Contact Information</h1>
                        <p class="text-muted text-sm tracking-wide">Have a question or just want to say hello? We're here to help.</p>
                    </div>

                    <div class="flex flex-col gap-5">
                        <div class="flex items-center gap-5">
                            <PhoneCall stroke="currentColor" class="size-5 shrink-0" />
                            <p class="text-sm tracking-wide">+254 700 000 000</p>
                        </div>

                        <div class="flex items-center gap-5">
                            <Mail stroke="currentColor" class="size-5 shrink-0" />
                            <p class="text-sm tracking-wide">contact@everstone.co.ke</p>
                        </div>

                        <div class="flex items-center gap-5">
                            <MapPin stroke="currentColor" class="size-5 shrink-0" />
                            <p class="text-sm tracking-wide">Thika Town, Kenya</p>
                        </div>
                    </div>
                </div>

                <div class="@container/form flex-1 p-4 @2xl/form:p-8">
                    <form @submit.prevent="handleSubmit">
                        <div class="grid grid-cols-1 gap-5 @xl/form:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="name">Your name</Label>
                                <Input id="name" name="name" v-model="form.name" />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="space-y-2">
                                <Label for="email">Your email</Label>
                                <Input id="email" name="email" type="email" v-model="form.email" />
                                <InputError :message="form.errors.email" />
                            </div>
                        </div>

                        <div class="mt-5 space-y-2">
                            <Label for="subject">Subject</Label>
                            <Input id="subject" name="subject" v-model="form.subject" />
                            <InputError :message="form.errors.subject" />
                        </div>

                        <div class="mt-5 space-y-2">
                            <Label for="message">Message</Label>
                            <Textarea id="message" name="message" v-model="form.message"></Textarea>
                            <InputError :message="form.errors.message" />
                        </div>

                        <div class="mt-5">
                            <Button type="submit" :disabled="!form.isDirty || form.processing">
                                <LoaderCircle v-if="form.processing" class="mr-2 animate-spin" />
                                {{ form.processing ? 'Submitting...' : 'Send Message' }}
                            </Button>
                        </div>
                    </form>
                </div>
            </section>
        </section>
    </GuestLayout>
</template>
