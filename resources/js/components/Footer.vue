<script setup>
import { Link } from '@inertiajs/vue3';
import { PhoneCall } from 'lucide-vue-next';
import { ref } from 'vue';
import AppLogoIcon from './AppLogoIcon.vue';

const menus = ref([
    {
        title: 'Shop',
        items: [
            { text: 'Flat Headstone', link: route('memorials.byCategory', 'Flat Headstones') },
            { text: 'Upright Headstone', link: route('memorials.byCategory', 'Upright Headstones') },
            { text: 'Full Grave Covers', link: route('memorials.byCategory', 'Full Grave Covers') },
            { text: 'Memorial Plaques', link: route('memorials.byCategory', 'Memorial Plaques') },
            { text: 'Memorials', link: route('memorials') },
        ],
    },
    {
        title: 'Customer Care',
        items: [
            { text: 'How It Works', link: '#' },
            { text: 'FAQ', link: '/faqs' },
            { text: 'Returns', link: '#' },
            { text: 'Contact Us', link: '/contact' },
            { text: 'About Us', link: '/about' },
        ],
    },
]);
</script>

<template>
    <footer class="bg-primary text-primary-foreground relative z-10 pt-20 pb-10 lg:pt-[120px] xl:pb-20">
        <div class="container mx-auto max-w-7xl px-4 md:px-8 xl:px-0">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
                <!-- Logo and description -->

                <div class="mb-10 w-full px-4">
                    <Link :href="route('home')" class="mb-6 flex items-center">
                        <AppLogoIcon class="w-fit" />
                        <span class="ml-2 text-xl font-semibold">Everstone</span>
                    </Link>

                    <p class="mb-6 text-base">Honoring legacies through timeless memorials crafted with care and precision.</p>
                    <p class="flex items-center text-sm font-medium">
                        <PhoneCall stroke="currentColor" class="mr-2 size-5" />
                        +254 000 000 000
                    </p>
                </div>

                <!-- Dynamic Menu Sections -->
                <div v-for="menu in menus" :key="menu.title" class="mb-10 w-full px-4">
                    <h4 class="mb-5 text-lg font-semibold">
                        {{ menu.title }}
                    </h4>
                    <ul class="space-y-3">
                        <li v-for="item in menu.items" :key="item.text">
                            <Link :href="item.link" class="text-sm font-medium">
                                {{ item.text }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Social Section -->
                <div class="mb-10 w-full px-4">
                    <h4 class="mb-5 text-lg font-semibold">Follow Us On</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="flex h-8 w-8 items-center justify-center rounded-full border">
                            <!-- Facebook -->
                            <svg width="8" height="16" fill="currentColor" viewBox="0 0 8 16">
                                <path
                                    d="M7.44 6.4H5.76V3.77H7.13c.24 0 .44-.21.44-.52V.52C7.57.23 7.39 0 7.13 0H5.51c-1.75 0-2.97 1.45-2.97 3.59v2.24H.6c-.31 0-.6.29-.6.7v1.86c0 .36.24.7.6.7h1.46v5.19c0 .36.25.7.6.7h2.08c.13 0 .25-.08.34-.18.09-.1.16-.28.16-.44V9.63h1c.29 0 .51-.21.55-.53L8 7.28a.56.56 0 0 0-.56-.88z"
                                />
                            </svg>
                        </a>
                        <a href="#" class="text-dark flex h-8 w-8 items-center justify-center rounded-full border">
                            <!-- Twitter -->
                            <svg width="16" height="12" fill="currentColor" viewBox="0 0 16 12">
                                <path
                                    d="M14.22 2.07 15.2.94c.28-.31.36-.54.39-.65-.77.42-1.49.56-1.95.56h-.18l-.1-.1c-.62-.49-1.39-.75-2.22-.75C9.32 0 7.9 1.36 7.9 2.94c0 .1 0 .25.02.35-2.58-.13-4.87-1.36-6.42-3.3-.28.46-.4.98-.4 1.52 0 1.05.53 2 1.41 2.55-.43 0-.82-.12-1.17-.31v.03c0 1.47 1.03 2.7 2.4 2.98-.25.07-.53.11-.81.11-.2 0-.38-.02-.56-.05.38 1.18 1.49 2.04 2.8 2.06-1.03.8-2.32 1.27-3.74 1.27H0C1.33 11.21 2.9 12 4.63 12c5.55 0 8.59-4.59 8.59-8.57 0-.13 0-.25-.01-.37z"
                                />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <footer class="bg-primary text-primary-foreground border-t py-3">
        <div class="container mx-auto max-w-7xl text-center">
            <p class="text-sm">
                Made with ❤️ by <a href="https://jonahdevs.co.ke" target="_blank" class="underline">Jonahdevs</a>. &copy;
                {{ new Date().getFullYear() }} All rights reserved
            </p>
        </div>
    </footer>
</template>
