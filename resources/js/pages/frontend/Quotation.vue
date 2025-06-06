<script setup>
import InputError from '@/components/InputError.vue';
import PageHeaderSection from '@/components/PageHeaderSection.vue';
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { cn } from '@/lib/utils';
import { Deferred, Head, useForm, usePage } from '@inertiajs/vue3';
import { DateFormatter, getLocalTimeZone, today } from '@internationalized/date';
import { CalendarIcon, LoaderCircle } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

defineProps({
    memorials: Object,
    materials: Object,
});

const user = usePage().props.auth.user;

const pageSectionLinks = [
    {
        title: 'Home',
        href: route('home'),
    },
    {
        title: 'Quotation',
        href: '',
    },
];

const df = new DateFormatter('en-US', {
    dateStyle: 'long',
});

const minDate = today(getLocalTimeZone()).add({ days: 1 });

const form = useForm({
    name: user?.name || '',
    email: user?.email || '',
    phone: user?.phone || '',
    address: user?.address || '',
    memorial: '',
    material: '',
    dimensions: '',
    inscription: '',
    budget: '',
    deadline: '',
    reference_image: null,
    note: '',
    terms: false,
});

const handleSubmit = () => {
    form.transform((data) => ({
        ...data,
        deadline: data.deadline ? df.format(data.deadline.toDate(getLocalTimeZone())) : '',
    })).post(route('quotation.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Quotation has been Submitted successfully');
            form.reset();
        },
        onError: () => {
            toast.error('Failed to submit quotation. Please check the form for errors.');
        },
    });
};
</script>

<template>
    <Head title="Quotation" />

    <GuestLayout>
        <PageHeaderSection :title="'Quotation'" :links="pageSectionLinks" />

        <section class="container mx-auto my-10 max-w-5xl px-4 md:px-8 xl:px-0">
            <section class="rounded-xl border p-5">
                <div class="mb-7 text-center">
                    <h1 class="text-lg font-bold">Share your requirements with us</h1>
                    <p class="text-muted-foreground text-sm">By Filling this simple form below</p>
                </div>

                <form @submit.prevent="handleSubmit" class="@container/form space-y-10">
                    <section>
                        <p class="text-muted-foreground mb-3 text-base font-medium">Personal Information</p>
                        <div class="grid grid-cols-1 items-center gap-5 gap-y-4 @2xl/form:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="name">Your Name</Label>
                                <Input type="text" v-model="form.name" id="name" />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="space-y-2">
                                <Label for="email">Email Address</Label>
                                <Input type="email" v-model="form.email" id="email" />
                                <InputError :message="form.errors.email" />
                            </div>

                            <div class="space-y-2">
                                <Label for="phone">Phone Number</Label>
                                <Input type="text" v-model="form.phone" id="phone" />
                                <p class="text-muted-foreground text-xs">Include your mobile number with area code (e.g., 0712 345 678).</p>
                                <InputError :message="form.errors.phone" />
                            </div>
                            <div class="space-y-2">
                                <Label for="address">Your Address</Label>
                                <Input type="text" v-model="form.address" id="address" />
                                <p class="text-muted-foreground text-xs">
                                    Provide your current location for delivery or reference (e.g., Karen, Nairobi).
                                </p>
                                <InputError :message="form.errors.address" />
                            </div>
                        </div>
                    </section>

                    <!-- memorial details -->
                    <section class="space-y-5">
                        <p class="text-muted-foreground mb-3 text-base font-medium">Memorial Details</p>
                        <div class="grid grid-cols-1 items-center gap-5 @2xl/form:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="memorial">Memorial Type</Label>
                                <Select id="memorial" v-model="form.memorial">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Select Headstone" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <Deferred data="memorials">
                                                <template #fallback>
                                                    <div class="text-muted-foreground py-1 ps-3 text-sm">Loading...</div>
                                                </template>
                                                <SelectItem v-for="memorial in memorials" :key="memorial.id" :value="memorial.id">
                                                    {{ memorial.title }}
                                                </SelectItem>
                                            </Deferred>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.memorial" />
                            </div>

                            <div class="space-y-2">
                                <Label for="material">Preferred Material</Label>
                                <Select id="material" v-model="form.material">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Select Material" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <Deferred data="materials">
                                                <template #fallback>
                                                    <div class="text-muted-foreground py-1 ps-3 text-sm">Loading...</div>
                                                </template>
                                                <SelectItem v-for="material in materials" :key="material.id" :value="material.id">
                                                    {{ material.name }}
                                                </SelectItem>
                                            </Deferred>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.material" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="dimensions">Dimensions</Label>
                            <Input type="text" v-model="form.dimensions" id="dimensions" />
                            <p class="text-muted-foreground text-xs">Specify size in centimeters (e.g., 60cm x 30cm x 10cm).</p>
                            <InputError :message="form.errors.dimensions" />
                        </div>

                        <div class="space-y-2">
                            <Label for="inscription">Inscription Text</Label>
                            <Textarea id="inscription" v-model="form.inscription"></Textarea>
                            <p class="text-muted-foreground text-xs">Enter the engraving text (e.g., “In Loving Memory of…”).</p>
                            <InputError :message="form.errors.inscription" />
                        </div>
                    </section>

                    <!-- Budget and Extras -->
                    <section class="space-y-5">
                        <p class="text-muted-foreground mb-3 text-base font-medium">Budget and Extras</p>
                        <div class="grid grid-cols-1 items-center gap-5 @2xl/form:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="budget">Estimated Budget (KES)</Label>
                                <Input type="text" v-model="form.budget" id="budget" />
                                <p class="text-muted-foreground text-xs">Indicate the maximum amount you’re willing to spend.</p>
                                <InputError :message="form.errors.budget" />
                            </div>

                            <div class="space-y-2">
                                <Label for="deadline">Preferred Completion Date</Label>
                                <Popover>
                                    <PopoverTrigger as-child>
                                        <Button
                                            variant="outline"
                                            :class="
                                                cn(
                                                    'w-full justify-start bg-transparent text-left font-normal',
                                                    !form.deadline && 'text-muted-foreground',
                                                )
                                            "
                                        >
                                            <CalendarIcon class="mr-2 h-4 w-4" />
                                            {{ form.deadline ? df.format(form.deadline.toDate(getLocalTimeZone())) : 'Pick a date' }}
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-auto p-0">
                                        <Calendar v-model="form.deadline" :minValue="minDate" initial-focus />
                                    </PopoverContent>
                                </Popover>
                                <p class="text-muted-foreground text-xs">Let us know your ideal delivery deadline.</p>
                                <InputError :message="form.errors.deadline" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="reference_image">Reference Image</Label>
                            <Input type="file" id="reference_image" @input="form.reference_image = $event.target.files[0]" />
                            <p class="text-muted-foreground text-xs">Upload a reference sketch or image (JPEG or PNG).</p>
                            <InputError :message="form.errors.reference_image" />
                        </div>

                        <div class="space-y-2">
                            <Label for="note">Additional Notes</Label>
                            <Textarea type="text" v-model="form.note" id="note" class="min-h-28"></Textarea>
                            <p class="text-muted-foreground text-xs">Include anything else you want us to consider.</p>
                            <InputError :message="form.errors.note" />
                        </div>
                    </section>

                    <div class="space-y-2">
                        <div class="flex items-start space-x-2">
                            <Checkbox id="terms" v-model="form.terms" />
                            <label for="terms" class="text-muted-foreground text-sm">
                                I accept the <a href="#" class="text-blue-600 underline dark:text-blue-500">terms and conditions</a>.
                            </label>
                        </div>
                        <InputError :message="form.errors.terms" />
                    </div>

                    <div class="flex items-center justify-center">
                        <Button type="submit" class="w-full" :disabled="!form.isDirty || form.processing">
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                            {{ form.processing ? 'Submitting...' : 'Submit Request' }}
                        </Button>
                    </div>
                </form>
            </section>
        </section>
    </GuestLayout>
</template>
