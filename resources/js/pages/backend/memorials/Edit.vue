<script setup>
import InputError from '@/components/InputError.vue';
import AspectRatio from '@/components/ui/aspect-ratio/AspectRatio.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Carousel, CarouselContent, CarouselItem } from '@/components/ui/carousel';
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
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Skeleton } from '@/components/ui/skeleton';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { cn } from '@/lib/utils';
import { Deferred, Head, useForm, usePage } from '@inertiajs/vue3';
import { watchOnce } from '@vueuse/core';
import { Check, ChevronsUpDown, LoaderCircle, Search, X } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

// Your logic goes here
const props = defineProps({
    categories: Object,
    materials: Object,
    tags: Object,
    memorial: Object,
});

const flash = computed(() => usePage().props.flash);
const imagesPreview = ref([]);
const emblaMainApi = ref(null);
const emblaThumbnailApi = ref(null);
const selectedIndex = ref(0);
const saving = ref(false);
const publishing = ref(false);
const unPublishing = ref(false);

const breadcrumbs = [
    { title: 'Dashboard', href: '/admin/dashboard' },
    { title: 'Memorials', href: '/admin/memorials' },
    { title: 'Edit Memorial', href: `/admin/memorials/${props.memorial.data.id}edit` },
];

const mainImage = () => {
    return imagesPreview.value.find((image) => image.type === 'main_image') || null;
};

const galleryImages = () => {
    return imagesPreview.value.filter((image) => image.type === 'gallery_image') || [];
};

const form = useForm({
    title: props.memorial.data.title || '',
    slug: props.memorial.data.slug || '',
    sku: props.memorial.data.sku || '',
    dimensions: props.memorial.data.dimensions || '',
    weight: props.memorial.data.weight || '',
    estimated_delivery_time: props.memorial.data.estimated_delivery_time || '',
    description: props.memorial.data.description || '',
    category: props.memorial.data.category || null,
    materials: props.memorial.data.materials || [],
    tags: props.memorial.data.tags || [],
    price: props.memorial.data.price || '',
    sale_price: props.memorial.data.sale_price || '',
    main_image: null,
    gallery_images: null,
    status: props.memorial.data.status || '',
    stock: props.memorial.data.stock || 1,
});

const handleMainImage = (event) => {
    const file = event.target.files[0];

    if (file) {
        form.main_image = file;
        const path = URL.createObjectURL(file);

        if (imagesPreview.value.some((image) => image.path === path && image.type === 'main_image')) {
            // If the image already exists in the preview, do not add it again
            return;
        }

        imagesPreview.value = imagesPreview.value.filter((image) => image.type !== 'main_image');

        imagesPreview.value.push({
            id: imagesPreview.value.length + 1,
            path: path,
            type: 'main_image',
        });
    }
};

const handleGalleryImages = (event) => {
    const files = Array.from(event.target.files);
    form.gallery_images = files;

    files.forEach((file, index) => {
        const path = URL.createObjectURL(file);
        if (imagesPreview.value.some((image) => image.path === path && image.type === 'gallery_image')) {
            // If the image already exists in the preview, do not add it again
            return;
        }

        imagesPreview.value.push({
            id: imagesPreview.value.length + 1,
            path: path,
            type: 'gallery_image',
        });
    });
};

const handleSubmit = async (status) => {
    form.transform((data) => ({
        ...data,
        _method: 'put',
        status: status,
    })).post(route('admin.memorials.update', props.memorial.data.id), {
        forceFormData: true,
        preserveScroll: true,
        onError: (error) => {
            toast.error('Something went wrong. Please check the form and try again.');
        },
        onFinish: () => {
            saving.value = false;
            publishing.value = false;
            unPublishing.value = false;
        },
    });
};

const deleteImage = (imageId) => {
    const image = imagesPreview.value.find((image) => image.id === imageId);

    if (!image) return;

    if (image.type === 'main_image') {
        form.main_image = null;
    } else {
        form.gallery_images = form.gallery_images.filter((img) => URL.createObjectURL(img) !== image?.path);
    }
    // Remove the image from the preview
    imagesPreview.value = imagesPreview.value.filter((img) => img.id !== imageId);
};

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

onMounted(() => {
    if (props.memorial.data.image) {
        imagesPreview.value.push({
            id: 1,
            path: props.memorial.data.image,
            type: 'main_image',
            database: true,
        });
    }

    if (props.memorial.data.images && props.memorial.data.images.length > 0) {
        props.memorial.data.images.forEach((image, index) => {
            imagesPreview.value.push({
                id: imagesPreview.value.length + 1,
                database_id: image.id,
                path: image.path,
                type: 'gallery_image',
                database: true,
            });
        });
    }
});

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
    <Head title="Edit Memorial" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageTitle> Edit Memorial </template>

        <template #pageActions>
            <Button
                @click="
                    handleSubmit(form.status);
                    saving = true;
                "
                variant="secondary"
                :disabled="!form.isDirty || form.processing"
            >
                <LoaderCircle v-if="saving && form.processing" class="h-4 w-4 animate-spin" />
                <span>{{ saving && form.processing ? 'Processing...' : 'Save Changes' }}</span>
            </Button>

            <Button
                v-if="form.status === 'draft'"
                @click="
                    handleSubmit('published');
                    publishing = true;
                "
                :disabled="form.processing"
            >
                <LoaderCircle v-if="publishing && form.processing" class="h-4 w-4 animate-spin" />
                <span>{{ publishing && form.processing ? 'Processing...' : 'Publish' }}</span>
            </Button>

            <Button
                v-else
                @click="
                    handleSubmit('draft');
                    unPublishing = true;
                "
                :disabled="form.processing"
                variant="destructive"
            >
                <LoaderCircle v-if="unPublishing && form.processing" class="h-4 w-4 animate-spin" />
                <span>{{ unPublishing && form.processing ? 'Processing...' : 'Un Publish' }}</span>
            </Button>
        </template>

        <section class="@container">
            <div class="flex flex-col flex-wrap items-start gap-6 @2xl:flex-row">
                <div class="w-full max-w-sm rounded-xl border p-5">
                    <!-- carousel -->
                    <div v-if="imagesPreview.length > 0" class="md:col-span-1">
                        <Carousel class="relative w-full" @init-api="(val) => (emblaMainApi = val)">
                            <CarouselContent>
                                <CarouselItem v-for="image in imagesPreview" :key="image.id">
                                    <div class="p-1">
                                        <AspectRatio :ratio="1 / 1">
                                            <img
                                                :src="image.path"
                                                alt="memorial image"
                                                class="h-full w-full rounded-t-md object-cover object-center"
                                            />
                                        </AspectRatio>
                                    </div>
                                </CarouselItem>
                            </CarouselContent>
                        </Carousel>

                        <!-- thumbnail -->
                        <Carousel class="relative w-full" @init-api="(val) => (emblaThumbnailApi = val)">
                            <CarouselContent class="ml-0 flex gap-1">
                                <CarouselItem
                                    v-for="(image, index) in imagesPreview"
                                    :key="image.id"
                                    class="basis-1/4 cursor-pointer pl-0"
                                    @click="onThumbClick(index)"
                                >
                                    <div class="p-1" :class="index === selectedIndex ? '' : 'opacity-50'">
                                        <AspectRatio :ratio="1 / 1">
                                            <img
                                                :src="image.path"
                                                alt="memorial image"
                                                class="h-full w-full rounded-t-md object-cover object-center"
                                            />
                                        </AspectRatio>
                                    </div>
                                </CarouselItem>
                            </CarouselContent>
                        </Carousel>
                    </div>

                    <div v-else class="space-y-2">
                        <Skeleton class="h-60 w-full rounded-xl" />

                        <div class="grid grid-cols-4 gap-2">
                            <Skeleton class="h-12 w-full rounded-md" />
                            <Skeleton class="h-12 w-full rounded-md" />
                            <Skeleton class="h-12 w-full rounded-md" />
                            <Skeleton class="h-12 w-full rounded-md" />
                        </div>
                    </div>

                    <div class="mt-4 space-y-6">
                        <div v-if="mainImage()">
                            <p class="text-muted-foreground text-sm">Main Image</p>

                            <div class="flex items-center gap-3">
                                <p class="text-muted-foreground text-xs break-all">{{ mainImage()?.path }}</p>
                                <button class="cursor-pointer" type="button" @click="deleteImage(mainImage().id)">
                                    <X class="size-4" />
                                </button>
                            </div>
                        </div>

                        <div v-if="galleryImages().length > 0">
                            <p class="text-muted-foreground text-sm">Gallery Images</p>

                            <div class="mt-1 flex flex-col gap-2">
                                <template v-for="image in galleryImages()" :key="image.id">
                                    <div class="flex items-center gap-3">
                                        <p class="text-muted-foreground text-xs break-all">{{ image.path }}</p>

                                        <button class="cursor-pointer" type="button" @click="deleteImage(image.id)">
                                            <X class="size-4" />
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <form class="@container/form w-full flex-1 space-y-6">
                    <div class="space-y-6 rounded-xl border p-4">
                        <p class="text-muted-foreground text-sm">Memorial Information</p>

                        <div class="grid grid-cols-1 gap-5 gap-y-3 @lg/form:grid-cols-2 @4xl/form:grid-cols-3">
                            <!-- memorial title -->
                            <div class="space-y-2">
                                <Label for="title">Memorial Title <span class="text-red-500">*</span></Label>
                                <Input id="title" type="text" name="title" placeholder="e.g. Elegant Marble Headstone" v-model="form.title" />
                                <InputError :message="form.errors.title" />
                            </div>

                            <!-- memorial slug -->
                            <div class="space-y-2">
                                <Label for="slug">Slug </Label>
                                <Input id="slug" type="text" name="slug" placeholder="e.g. elegant-marble-headstone" v-model="form.slug" />
                                <InputError :message="form.errors.slug" />
                            </div>

                            <!-- memorial  sku -->
                            <div class="space-y-2">
                                <Label for="sku">Sku</Label>
                                <Input id="sku" type="text" name="sku" placeholder="e.g. MHS-2025-001" v-model="form.sku" />
                                <InputError :message="form.errors.sku" />
                            </div>
                            <div class="space-y-2">
                                <Label for="dimensions">Dimensions <span class="text-red-500">*</span></Label>
                                <Input id="dimensions" type="text" name="dimensions" placeholder="e.g. 24x32x6" v-model="form.dimensions" />
                                <InputError :message="form.errors.dimensions" />
                            </div>

                            <!-- memorial weight -->
                            <div class="space-y-2">
                                <Label for="weight">Weight (in kg)</Label>
                                <Input id="weight" name="weight" placeholder="e.g. 32" v-model="form.weight" />
                                <InputError :message="form.errors.weight" />
                            </div>

                            <!-- memorial  estimated Delivery -->
                            <div class="space-y-2">
                                <Label for="estimated_delivery_time">Estimated Delivery Period</Label>
                                <Select id="estimated_delivery_time" name="estimated_delivery_time" v-model="form.estimated_delivery_time">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Estimated Delivery Time" />
                                    </SelectTrigger>
                                    <SelectContent class="w-full">
                                        <SelectGroup>
                                            <SelectItem value="one_week">One Week</SelectItem>
                                            <SelectItem value="two_weeks">Two Weeks</SelectItem>
                                            <SelectItem value="one_month">One Month</SelectItem>
                                            <SelectItem value="one_month+">One Month+</SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.estimated_delivery_time" />
                            </div>
                        </div>

                        <!-- memorial description -->
                        <div class="space-y-2">
                            <Label for="description">Description <span class="text-red-500">*</span></Label>
                            <Textarea
                                id="description"
                                name="description"
                                class="min-h-32"
                                v-model="form.description"
                                placeholder="Brief description of the memorial design, material, or finish..."
                            />
                            <InputError :message="form.errors.description" />
                        </div>

                        <div class="grid grid-cols-1 gap-5 gap-y-3 @lg/form:grid-cols-2 @4xl/form:grid-cols-3">
                            <div class="space-y-2">
                                <Label for="category">Category </Label>
                                <Select id="category" name="category" v-model="form.category">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Select Category" />
                                    </SelectTrigger>
                                    <SelectContent class="w-full">
                                        <SelectGroup>
                                            <template v-for="(category, key) in categories" :key="key">
                                                <SelectItem :value="category">
                                                    {{ category }}
                                                </SelectItem>
                                            </template>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.category" />
                            </div>

                            <div class="space-y-2">
                                <Label for="materials">Materials <span class="text-red-500">*</span></Label>
                                <Combobox v-model="form.materials" by="id" multiple name="materials" id="materials">
                                    <ComboboxAnchor as-child>
                                        <ComboboxTrigger as-child class="h-fit">
                                            <Button variant="outline" class="flex w-full flex-wrap items-center gap-2">
                                                <div v-if="form.materials && form.materials.length" class="flex flex-wrap gap-1">
                                                    <Badge v-for="m in form.materials" :key="m.id" class="capitalize">{{ m.name }} </Badge>
                                                </div>
                                                <p v-else>Select material</p>
                                                <ChevronsUpDown class="ms-auto h-4 w-4 shrink-0 opacity-50" />
                                            </Button>
                                        </ComboboxTrigger>
                                    </ComboboxAnchor>

                                    <ComboboxList class="w-full">
                                        <div class="relative w-full max-w-sm items-center">
                                            <ComboboxInput
                                                class="h-10 rounded-none border-0 border-b pl-1 focus-visible:ring-0"
                                                placeholder="Select material..."
                                            />
                                            <span class="absolute inset-y-0 start-0 flex items-center justify-center px-3">
                                                <Search class="text-muted-foreground h-4 w-4" />
                                            </span>
                                        </div>

                                        <ComboboxEmpty> No material found. </ComboboxEmpty>

                                        <ComboboxGroup class="max-h-64 overflow-y-auto">
                                            <Deferred data="materials">
                                                <template #fallback>
                                                    <div class="ps-2 text-sm text-slate-500">Loading...</div>
                                                </template>
                                                <ComboboxItem v-for="material in materials" :key="material.id" :value="material" class="capitalize"
                                                    >{{ material.name }}

                                                    <ComboboxItemIndicator>
                                                        <Check :class="cn('ml-auto h-4 w-4')" />
                                                    </ComboboxItemIndicator>
                                                </ComboboxItem>
                                            </Deferred>
                                        </ComboboxGroup>
                                    </ComboboxList>
                                </Combobox>
                                <InputError :message="form.errors.materials" />
                            </div>

                            <div class="space-y-2">
                                <Label for="tags">Tags <span class="text-red-500">*</span></Label>
                                <Combobox v-model="form.tags" by="id" multiple name="tags" id="tags">
                                    <ComboboxAnchor as-child>
                                        <ComboboxTrigger as-child class="h-fit">
                                            <Button variant="outline" class="flex w-full flex-wrap items-center gap-2">
                                                <div v-if="form.tags && form.tags.length" class="flex flex-wrap gap-1">
                                                    <Badge v-for="m in form.tags" :key="m.id" class="capitalize">{{ m.name }} </Badge>
                                                </div>
                                                <p v-else>Select tag</p>
                                                <ChevronsUpDown class="ms-auto h-4 w-4 shrink-0 opacity-50" />
                                            </Button>
                                        </ComboboxTrigger>
                                    </ComboboxAnchor>

                                    <ComboboxList class="relative w-full">
                                        <div class="relative w-full max-w-sm items-center">
                                            <ComboboxInput
                                                class="h-10 rounded-none border-0 border-b pl-1 focus-visible:ring-0"
                                                placeholder="Select material..."
                                            />
                                            <span class="absolute inset-y-0 start-0 flex items-center justify-center px-3">
                                                <Search class="text-muted-foreground h-4 w-4" />
                                            </span>
                                        </div>

                                        <ComboboxEmpty> No tag found. </ComboboxEmpty>

                                        <ComboboxGroup class="max-h-64 overflow-y-auto">
                                            <Deferred data="tags">
                                                <template #fallback>
                                                    <div class="ps-2 text-sm text-slate-500">Loading...</div>
                                                </template>

                                                <ComboboxItem v-for="tag in tags" :key="tag.id" :value="tag" class="capitalize">
                                                    {{ tag.name }}

                                                    <ComboboxItemIndicator>
                                                        <Check :class="cn('ml-auto h-4 w-4')" />
                                                    </ComboboxItemIndicator>
                                                </ComboboxItem>
                                            </Deferred>
                                        </ComboboxGroup>
                                    </ComboboxList>
                                </Combobox>
                                <InputError :message="form.errors.tags" />
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6 rounded-xl border p-4">
                        <p class="text-muted-foreground text-sm">Pricing Details</p>

                        <div class="grid grid-cols-1 gap-5 gap-y-3 @lg/form:grid-cols-2">
                            <!-- Regular Price -->
                            <div class="space-y-2">
                                <Label for="price">Regular Price <span class="text-red-500">*</span></Label>
                                <Input id="price" type="text" name="price" placeholder="e.g. 45000" v-model="form.price" />
                                <InputError :message="form.errors.price" />
                            </div>

                            <!-- Sale Price -->
                            <div class="space-y-2">
                                <Label for="sale_price">Sale Price </Label>
                                <Input id="sale_price" type="text" name="sale_price" placeholder="e.g. 30000" v-model="form.sale_price" />
                                <InputError :message="form.errors.sale_price" />
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6 rounded-xl border p-4">
                        <p class="text-muted-foreground text-sm">Memorial Images</p>

                        <div class="grid w-full items-center gap-1.5">
                            <Label for="main_image">Main Image</Label>
                            <Input id="main_image" type="file" @change="handleMainImage($event)" />
                            <InputError :message="form.errors.main_image" />
                        </div>

                        <div class="space-y-2">
                            <Label for="images">Gallery Images</Label>
                            <div class="mt-2 flex w-full justify-center rounded-lg border border-dashed px-6 py-10">
                                <div class="text-center">
                                    <svg
                                        class="text-muted-foreground mx-auto size-12"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                        aria-hidden="true"
                                        data-slot="icon"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M1.5 6a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 6v12a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 18V6ZM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0 0 21 18v-1.94l-2.69-2.689a1.5 1.5 0 0 0-2.12 0l-.88.879.97.97a.75.75 0 1 1-1.06 1.06l-5.16-5.159a1.5 1.5 0 0 0-2.12 0L3 16.061Zm10.125-7.81a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                    <div class="text-muted-foreground mt-4 flex text-sm/6">
                                        <label
                                            for="file-upload"
                                            class="relative cursor-pointer rounded-md font-semibold text-indigo-600 focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 focus-within:outline-hidden hover:text-indigo-500 dark:text-blue-400 dark:hover:text-indigo-300"
                                        >
                                            <span>Upload a file</span>
                                            <input
                                                id="file-upload"
                                                name="images"
                                                type="file"
                                                class="sr-only"
                                                multiple
                                                @change="handleGalleryImages($event)"
                                            />
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-muted-foreground text-xs/5">PNG, JPG, GIF up to 10MB</p>
                                </div>
                            </div>
                            <InputError :message="form.errors.gallery_images" />
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </AppLayout>
</template>
