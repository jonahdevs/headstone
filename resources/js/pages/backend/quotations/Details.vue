<script setup>
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { cn } from '@/lib/utils';
import { Head, useForm } from '@inertiajs/vue3';
import { DateFormatter, getLocalTimeZone, today } from '@internationalized/date';
import { CalendarIcon, LoaderCircle } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

// Your logic goes here
const props = defineProps({ quotation: Object });
const breadcrumbs = [
    { title: 'Dashboard', href: route('admin.dashboard') },
    { title: 'Quotations', href: route('admin.quotations.index') },
    { title: 'Quotations Details' },
];

const df = new DateFormatter('en-US', {
    dateStyle: 'long',
});

const form = useForm({
    quoted_price: '',
    valid_until: '',
    message: '',
});

const handleSubmit = () => {
    form.transform((data) => ({
        ...data,
        valid_until: data.valid_until ? df.format(data.valid_until.toDate(getLocalTimeZone())) : '',
    })).put(route('admin.quotations.update', props.quotation.data.id), {
        onSuccess: () => {
            form.reset();
            toast.success('Response sent successfully!');
        },
        onError: () => {
            toast.error('Failed to send response. Please try again.');
        },
    });
};
</script>

<template>
    <Head title="Quotation Details" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageTitle>Quotation Details</template>

        <section class="@container">
            <section class="grid grid-cols-1 items-start gap-6 @4xl:grid-cols-2">
                <div class="@container/overview space-y-4 rounded-xl border p-5">
                    <h2 class="mb-4 text-xl font-semibold">Quotation Overview</h2>

                    <div class="text-muted-foreground grid grid-cols-1 gap-3 text-sm @xl/overview:grid-cols-2">
                        <p><strong>Quote #:</strong> {{ quotation.data.id }}</p>
                        <p>
                            <strong>Status:</strong>
                            <Badge class="ml-3 capitalize" :class="{ 'bg-green-500': quotation.data.status == 'responded' }">
                                {{ quotation.data.status }}</Badge
                            >
                        </p>
                        <p><strong>Memorial:</strong> {{ quotation.data.memorial }}</p>
                        <p><strong>Material:</strong> {{ quotation.data.material }}</p>
                        <p><strong>Budget:</strong> {{ quotation.data.budget }}</p>
                        <p><strong>Deadline:</strong> {{ quotation.data.deadline }}</p>
                        <p><strong>Source:</strong> {{ quotation.data.source }}</p>
                        <p><strong>Created:</strong> {{ quotation.data.date }}</p>
                    </div>

                    <div>
                        <h3 class="mb-1 text-sm font-medium">Instructions</h3>
                        <p class="text-muted-foreground mb-2 text-sm">{{ quotation.data.instructions }}</p>
                    </div>

                    <div>
                        <h3 class="mb-1 text-sm font-medium">Customer</h3>

                        <div class="text-muted-foreground text-sm">
                            <p>{{ quotation.data.customer.name }}</p>
                            <p>{{ quotation.data.customer.email }}</p>
                            <p>{{ quotation.data.customer.phone }}</p>
                            <p>{{ quotation.data.customer.address }}</p>
                        </div>
                    </div>
                </div>
                <!-- Response Form -->
                <div class="rounded-xl border p-5">
                    <div v-if="quotation.data.response_data.length > 0" class="mb-5 flex flex-col gap-2 rounded-xl p-4">
                        <div
                            v-for="(message, index) in quotation.data.response_data"
                            :key="index"
                            :class="message.current_user ? 'justify-end' : 'justify-start'"
                            class="flex gap-4"
                        >
                            <div
                                :class="
                                    message.current_user
                                        ? 'rounded-br-none bg-blue-600 text-white dark:bg-blue-400 dark:text-black'
                                        : 'bg-muted-foreground rounded-bl-none'
                                "
                                class="max-w-[75%] rounded-xl p-4 shadow"
                            >
                                <p v-if="message.quoted_price"><strong>Quoted:</strong> {{ message.quoted_price }}</p>
                                <p class="mt-1 text-sm">{{ message.message }}</p>
                                <p class="mt-1 text-xs" v-if="message.valid_until">
                                    <span class="font-medium">Valid Until:</span> {{ message.valid_until }}
                                </p>
                                <p class="mt-2 text-right text-xs">{{ message.response_by }} • {{ message.response_at }}</p>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="handleSubmit" class="space-y-5">
                        <h2 class="text-lg font-semibold">Write a Response</h2>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="quoted_price">Quoted Price</Label>
                                <Input id="quoted_price" v-model="form.quoted_price" />
                                <InputError :message="form.errors.quoted_price" />
                            </div>

                            <div class="space-y-2">
                                <Label for="valid_until">Valid Until</Label>
                                <Popover id="valid_until">
                                    <PopoverTrigger as-child>
                                        <Button
                                            variant="outline"
                                            :class="
                                                cn(
                                                    'w-full justify-start bg-transparent text-left font-normal',
                                                    !form.valid_until && 'text-muted-foreground',
                                                )
                                            "
                                        >
                                            <CalendarIcon class="mr-2 h-4 w-4" />
                                            {{ form.valid_until ? df.format(form.valid_until.toDate(getLocalTimeZone())) : 'Pick a date' }}
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-auto p-0">
                                        <Calendar v-model="form.valid_until" :min-value="today(getLocalTimeZone())" initial-focus />
                                    </PopoverContent>
                                </Popover>
                                <InputError :message="form.errors.valid_until" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="message">Message</Label>
                            <Textarea id="message" placeholder="Write a response..." v-model="form.message"></Textarea>
                            <InputError :message="form.errors.message" />
                        </div>

                        <Button type="submit" :disabled="!form.isDirty || form.processing" class="min-w-44">
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                            <span v-else> Send Response </span>
                        </Button>
                    </form>
                </div>
            </section>
        </section>
    </AppLayout>
</template>
