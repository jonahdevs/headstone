<script setup>
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import InputError from '@/components/InputError.vue';
import PageHeaderSection from '@/components/PageHeaderSection.vue';
import { AspectRatio } from '@/components/ui/aspect-ratio';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Carousel, CarouselContent, CarouselItem, CarouselNext, CarouselPrevious } from '@/components/ui/carousel';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Skeleton } from '@/components/ui/skeleton';
import { Textarea } from '@/components/ui/textarea';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { cn } from '@/lib/utils';
import { Deferred, Head, useForm } from '@inertiajs/vue3';
import { DateFormatter, getLocalTimeZone, today } from '@internationalized/date';
import { watchOnce } from '@vueuse/core';
import Autoplay from 'embla-carousel-autoplay';
import { Link as Attach, BadgeCheck, CalendarIcon, LoaderCircle, Star, X } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

// Your logic goes here
const { memorial } = defineProps({ memorial: Object, testimonies: Object });
const autoplay = Autoplay({ delay: 3000, stopOnInteraction: false, stopOnMouseEnter: true });

const pageSectionLinks = [
    {
        title: 'Home',
        href: route('home'),
    },
    {
        title: 'Memorials',
        href: route('memorials'),
    },
    {
        title: 'Details',
        href: '#',
    },
];

const emblaMainApi = ref(null);
const emblaThumbnailApi = ref(null);
const selectedIndex = ref(0);
const imageUrl = ref(null);
const showCustomEpitaph = ref(true);

function onSelect(index) {
    if (!emblaMainApi.value || !emblaThumbnailApi.value) return;

    selectedIndex.value = emblaMainApi.value.selectedScrollSnap();
    emblaThumbnailApi.value.scrollTo(emblaMainApi.value.selectedScrollSnap());
}

function onThumbClick(index) {
    if (!emblaMainApi.value || !emblaThumbnailApi.value) return;

    emblaMainApi.value.scrollTo(index);
}

watchOnce(emblaMainApi, (emblaMainApi) => {
    if (!emblaMainApi) return;

    onSelect();
    emblaMainApi.on('select', onSelect);
    emblaMainApi.on('reInit', onSelect);
});

const form = useForm({
    memorial_id: '',
    font: 'nunito',
    first_name: 'John',
    last_name: 'Mwangi',
    DOB: '',
    DOD: '',
    epitaph: '',
    instructions:
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla sollicitudin hendrerit lorem sit amet viverra. Nulla tempus fermentum nunc, sed posuere quam pretium eget.',
    image: null,
    terms: false,
});

const fonts = ['nunito', 'amatic', 'pacifico', 'dancing', 'satisfy'];

const df = new DateFormatter('en-US', {
    dateStyle: 'long',
});

const handleImage = (event) => {
    let file = event.target.files[0];

    if (file) {
        imageUrl.value = URL.createObjectURL(file);
    }
};

const handleCustomEpitaph = (val) => {
    if (val === 'custom') {
        form.epitaph = '';
        showCustomEpitaph.value = true;
    } else {
        showCustomEpitaph.value = false;
    }
};

const handleSubmit = (memorialId) => {
    console.log('test');
    form.transform((data) => ({
        ...data,
        memorial_id: memorialId,
        DOB: data.DOB ? df.format(data.DOB.toDate(getLocalTimeZone())) : '',
        DOD: data.DOD ? df.format(data.DOD.toDate(getLocalTimeZone())) : '',
    })).post(route('cart.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Item added to your cart successfully!');
            form.reset(), (imageUrl.value = null);
        },
        onError: (error) => {
            console.log(error);
            toast.error('Oops! Something went wrong while adding this item to your cart. Please try again.');
        },
    });
};
</script>

<template>
    <Head title="Memorial Details" />

    <GuestLayout>
        <PageHeaderSection :title="memorial?.data?.title" :links="pageSectionLinks" />

        <section class="container mx-auto max-w-7xl space-y-16 px-4 py-10">
            <div class="relative grid grid-cols-1 gap-8 gap-x-0 md:grid-cols-2">
                <div class="sticky top-20">
                    <Carousel class="relative w-10/12" @init-api="(val) => (emblaMainApi = val)">
                        <CarouselContent>
                            <CarouselItem v-for="image in memorial.data.images" :key="image.id">
                                <div class="p-1">
                                    <AspectRatio :ratio="1 / 1">
                                        <img :src="image.path" alt="memorial image" class="h-full w-full rounded-t-md object-cover object-center" />
                                    </AspectRatio>
                                </div>
                            </CarouselItem>
                        </CarouselContent>
                    </Carousel>

                    <!-- thumbnail -->
                    <Carousel class="relative w-10/12" @init-api="(val) => (emblaThumbnailApi = val)">
                        <CarouselContent class="ml-0 flex gap-1">
                            <CarouselItem
                                v-for="(image, index) in memorial.data.images"
                                :key="image.id"
                                class="basis-1/8 cursor-pointer pl-0"
                                @click="onThumbClick(index)"
                            >
                                <div class="p-1" :class="index === selectedIndex ? '' : 'opacity-50'">
                                    <AspectRatio :ratio="1 / 1">
                                        <img :src="image.path" alt="memorial image" class="h-full w-full rounded-t-md object-cover object-center" />
                                    </AspectRatio>
                                </div>
                            </CarouselItem>
                        </CarouselContent>
                    </Carousel>
                </div>

                <div class="space-y-4">
                    <h1 class="text-2xl font-bold">{{ memorial.data.title }}</h1>

                    <div class="flex items-center gap-3">
                        <p :class="{ 'text-lg font-bold text-slate-400 line-through': memorial.data.sale_price }" class="text-lg font-semibold">
                            {{ memorial.data.price }}
                        </p>
                        <p class="text-xl font-bold text-green-600" v-if="memorial.data.sale_price">{{ memorial.data.sale_price }}</p>

                        <span v-if="memorial.data.sale_price" class="rounded-full bg-slate-100 px-5 py-1 text-sm">Special Offer</span>
                    </div>

                    <p class="text-muted-foreground text-sm leading-relaxed whitespace-pre-line">
                        {{ memorial.data.description }}
                    </p>

                    <Dialog>
                        <DialogTrigger as-child>
                            <Button type="button">Add to Cart </Button>
                        </DialogTrigger>
                        <DialogContent class="max-h-[90dvh] grid-rows-[auto_minmax(0,1fr)_auto] p-0 sm:max-w-3xl">
                            <DialogHeader class="p-4">
                                <!-- Heading and Description -->
                                <div class="space-y-1">
                                    <DialogTitle>Add Memorial Details</DialogTitle>
                                    <p class="text-muted-foreground text-sm">
                                        Please provide the details to personalize the selected memorial. These details will be engraved and prepared
                                        as per your instructions.
                                    </p>
                                </div>

                                <!-- Tips Section -->
                                <div class="bg-secondary text-secondary-foreground rounded-md p-4 text-sm">
                                    <p class="mb-1 font-medium">Tips:</p>
                                    <ul class="list-inside list-disc space-y-1">
                                        <li>Double-check spelling and dates for accuracy.</li>
                                        <li>Choose an epitaph that reflects your sentiments.</li>
                                        <li>Portraits should be high quality and front-facing.</li>
                                    </ul>
                                </div>
                            </DialogHeader>

                            <form @submit.prevent="handleSubmit(memorial.data.id)" class="grid gap-4 overflow-y-auto border-y px-6 py-4">
                                <section class="@container/form space-y-6 rounded-xl">
                                    <!-- inputs grid -->
                                    <div class="grid grid-cols-1 gap-4 @xl/form:grid-cols-2">
                                        <!-- first name -->
                                        <div class="space-y-2">
                                            <Label for="first_name">First Name</Label>
                                            <Input name="first_name" id="first_name" v-model="form.first_name" />
                                            <p class="text-muted-foreground text-xs">This will be engraved as the first name.</p>
                                            <InputError :message="form.errors.first_name" />
                                        </div>

                                        <!-- last name -->
                                        <div class="space-y-2">
                                            <Label for="last_name">Last Name</Label>
                                            <Input name="last_name" id="last_name" v-model="form.last_name" />
                                            <p class="text-muted-foreground text-xs">This will be engraved as the last name.</p>
                                            <InputError :message="form.errors.last_name" />
                                        </div>

                                        <!-- date of birth -->
                                        <div class="space-y-2">
                                            <Label for="DOB">Date of Birth</Label>
                                            <Popover id="DOB">
                                                <PopoverTrigger as-child>
                                                    <Button
                                                        variant="outline"
                                                        :class="
                                                            cn('w-full justify-start text-left font-normal', !form.DOB && 'text-muted-foreground')
                                                        "
                                                    >
                                                        <CalendarIcon class="mr-2 h-4 w-4" />
                                                        {{ form.DOB ? df.format(form.DOB.toDate(getLocalTimeZone())) : 'Pick a date' }}
                                                    </Button>
                                                </PopoverTrigger>
                                                <PopoverContent class="w-auto p-0">
                                                    <Calendar
                                                        v-model="form.DOB"
                                                        :max-value="today(getLocalTimeZone()).subtract({ days: 1 })"
                                                        initial-focus
                                                    />
                                                </PopoverContent>
                                            </Popover>
                                            <InputError :message="form.errors.DOB" />
                                        </div>

                                        <!-- date of passing -->
                                        <div class="space-y-2">
                                            <Label for="DOD">Death of Passing</Label>
                                            <Popover id="DOD">
                                                <PopoverTrigger as-child>
                                                    <Button
                                                        variant="outline"
                                                        :class="
                                                            cn(
                                                                'w-full justify-start bg-transparent text-left font-normal',
                                                                !form.DOD && 'text-muted-foreground',
                                                            )
                                                        "
                                                    >
                                                        <CalendarIcon class="mr-2 h-4 w-4" />
                                                        {{ form.DOD ? df.format(form.DOD.toDate(getLocalTimeZone())) : 'Pick a date' }}
                                                    </Button>
                                                </PopoverTrigger>
                                                <PopoverContent class="w-auto p-0">
                                                    <Calendar
                                                        v-model="form.DOD"
                                                        :max-value="today(getLocalTimeZone()).subtract({ days: 1 })"
                                                        initial-focus
                                                    />
                                                </PopoverContent>
                                            </Popover>
                                            <InputError :message="form.errors.DOD" />
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="select_epitaph">Select Epitaph</Label>
                                        <Select id="select_epitaph" v-model="form.epitaph" @update:modelValue="(val) => handleCustomEpitaph(val)">
                                            <SelectTrigger class="w-full">
                                                <SelectValue placeholder="Select Epitaph" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="custom">Custom</SelectItem>
                                                <SelectItem value="In Loving Memory">In Loving Memory</SelectItem>
                                                <SelectItem value="Forever in Our Hearts">Forever in Our Hearts</SelectItem>
                                                <SelectItem value="Beloved Father/Mother">Beloved Father/Mother</SelectItem>
                                                <SelectItem value="Rest in Peace">Rest in Peace</SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <InputError :message="form.errors.epitaph" />
                                    </div>

                                    <div class="space-y-2" v-if="showCustomEpitaph">
                                        <Label for="epitaph">Write Epitaph</Label>
                                        <Textarea name="epitaph" id="epitaph" v-model="form.epitaph" />
                                        <InputError :message="form.errors.epitaph" />
                                    </div>

                                    <div class="space-y-2">
                                        <Label>Preferred Font Style for the Inscription</Label>
                                        <div class="flex flex-wrap gap-3">
                                            <label
                                                v-for="(font, index) in fonts"
                                                :key="index"
                                                class="cursor-pointer rounded-xl border bg-white px-4 py-2 text-sm shadow-sm transition-all hover:shadow-md"
                                                :class="
                                                    form.font === font ? 'border-primary text-primary font-medium' : 'border-slate-200 text-slate-600'
                                                "
                                                :style="{ fontFamily: font }"
                                            >
                                                <input type="radio" name="font" class="hidden" :value="font" v-model="form.font" />
                                                Abc
                                            </label>
                                        </div>
                                        <InputError :message="form.errors.font" />
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="image">Would you like to include an image or portrait?</Label>
                                        <div class="flex items-center gap-3">
                                            <img
                                                v-if="imageUrl"
                                                :src="imageUrl"
                                                alt="image"
                                                class="h-16 w-16 rounded-md object-cover object-center"
                                            />

                                            <div class="flex items-center gap-5">
                                                <label for="image" class="group flex items-center gap-2 text-sm">
                                                    <Attach class="h-4 w-4" />
                                                    <span class="group-hover:underline">
                                                        {{ imageUrl ? 'Replace Image' : 'Attach image' }}
                                                    </span>
                                                </label>
                                                <input
                                                    type="file"
                                                    id="image"
                                                    class="hidden"
                                                    @change="handleImage($event)"
                                                    @input="form.image = $event.target.files[0]"
                                                />

                                                <button v-if="imageUrl" type="button" @click="((imageUrl = null), (form.image = null))">
                                                    <X class="h-4 w-5" />
                                                </button>
                                            </div>
                                        </div>
                                        <InputError :message="form.errors.image" />
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="instructions">Is there anything else you'd like to share about this order?</Label>
                                        <Textarea name="instructions" id="instructions" v-model="form.instructions" />
                                        <InputError :message="form.errors.instructions" />
                                    </div>

                                    <div class="flex items-center space-x-2">
                                        <Checkbox id="terms" v-model="form.terms" />
                                        <label for="terms" class="text-sm leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                            By clicking here, I agree that the information above is true, accurate and ready for production
                                        </label>
                                    </div>
                                </section>
                            </form>

                            <DialogFooter class="p-6 pt-0">
                                <Button @click="handleSubmit(memorial.data.id)" class="min-w-28" :disabled="form.processing">
                                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                                    Add to Cart
                                </Button>
                            </DialogFooter>
                        </DialogContent>
                    </Dialog>
                </div>
            </div>

            <section>
                <div class="flex flex-col items-start gap-3">
                    <div class="flex items-center gap-3">
                        <AppLogoIcon class="size-7" />
                        <p class="text-muted-foreground text-sm uppercase">Testimonials</p>
                    </div>
                    <h2 class="text-center text-3xl font-bold">What Our Customers Say</h2>
                    <p class="text-center text-sm">Hear from our satisfied customers about their experiences with our memorials.</p>
                </div>

                <Carousel
                    class="relative mt-10 w-full"
                    :opts="{
                        align: 'start',
                        loop: true,
                    }"
                    :plugins="[autoplay]"
                >
                    <CarouselContent>
                        <Deferred data="testimonies">
                            <template #fallback>
                                <CarouselItem v-for="i in 4" :key="i" class="h-full sm:basis-1/2 lg:basis-1/4">
                                    <div class="bg-card h-full min-h-36 animate-pulse space-y-3 rounded-md border p-3 shadow-sm">
                                        <!-- Header: Avatar + Name + Verified -->
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <!-- Avatar Placeholder -->
                                                <Skeleton class="h-10 w-10 rounded-full" />

                                                <div class="space-y-1">
                                                    <Skeleton class="h-3 w-24 rounded" />
                                                    <Skeleton class="h-2 w-20 rounded" />
                                                </div>
                                            </div>

                                            <!-- Star Rating Placeholder -->
                                            <div class="flex items-center gap-1">
                                                <Skeleton v-for="n in 5" :key="n" class="h-3 w-3 rounded" />
                                            </div>
                                        </div>

                                        <!-- Review Text Placeholder -->
                                        <div class="space-y-1">
                                            <Skeleton class="h-3 w-full rounded" />
                                            <Skeleton class="h-3 w-11/12 rounded" />
                                            <Skeleton class="h-3 w-10/12 rounded" />
                                        </div>
                                    </div>
                                </CarouselItem>
                            </template>

                            <template v-if="testimonies">
                                <CarouselItem v-for="testimony in testimonies.data" :key="testimony.id" class="h-full pb-3 sm:basis-1/2 lg:basis-1/4">
                                    <div class="bg-card h-full min-h-36 rounded-md border p-3 shadow-sm">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <Avatar>
                                                    <AvatarImage :src="testimony.customer.avatar" alt="customer avatar" />
                                                    <AvatarFallback>{{ testimony.customer.name.charAt(0) }} </AvatarFallback>
                                                </Avatar>
                                                <div>
                                                    <p class="text-sm font-medium">{{ testimony.customer.name }}</p>
                                                    <div class="flex items-center">
                                                        <BadgeCheck class="h-4 w-4 text-green-500" />
                                                        <p class="text-muted-foreground ml-1 text-xs">Verified customer</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-1">
                                                <template v-for="i in 5" :key="i">
                                                    <Star
                                                        class="h-3 w-3"
                                                        :class="
                                                            i <= testimony.rating
                                                                ? 'fill-yellow-500 stroke-yellow-500'
                                                                : 'dark:stoke-stone-800 fill-stone-300 stroke-stone-300 dark:fill-stone-800'
                                                        "
                                                    />
                                                </template>
                                            </div>
                                        </div>

                                        <p class="mt-3 line-clamp-3 text-sm">{{ testimony.review }}</p>
                                    </div>
                                </CarouselItem>
                            </template>
                        </Deferred>
                    </CarouselContent>
                    <CarouselPrevious />
                    <CarouselNext />
                </Carousel>
            </section>
        </section>
    </GuestLayout>
</template>
