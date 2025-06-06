<script setup>
import { useAppearance } from '@/composables/useAppearance';
import { Link, router, usePage } from '@inertiajs/vue3';
import { useEchoModel } from '@laravel/echo-vue';
import { Bell, ListMinus, Mail, Moon, PhoneCall, Search, ShoppingBag, ShoppingCart, Sun, SunMoon, User } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import AppLogoIcon from './AppLogoIcon.vue';
import { Avatar, AvatarFallback, AvatarImage } from './ui/avatar';
import { Button } from './ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from './ui/dropdown-menu';
import { Sheet, SheetContent, SheetFooter, SheetHeader, SheetTitle, SheetTrigger } from './ui/sheet';

const page = usePage();
const cart_items = computed(() => page.props.cart_items);
const user = computed(() => page.props.auth.user);
const { appearance, updateAppearance } = useAppearance();
const modes = ['light', 'dark', 'system'];
const notificationsCount = ref(page.props.notificationsCount);

if (user.value) {
    const { channel } = useEchoModel('App.Models.User', user.value?.id);
    channel().notification(() => {
        notificationsCount.value++;
    });
}

const smallNavLinks = [
    { title: 'Home', href: '/' },
    { title: 'About', href: '/about' },
    { title: 'Faqs', href: '/faqs' },
    { title: 'Quotation', href: '/quotation' },
    { title: 'Contact', href: '/contact' },
];

const categories = ['Flat Headstones', 'Upright Headstones', 'Memorial Plaques', 'Full Grave Covers'];

function cycleAppearance() {
    const index = modes.indexOf(appearance.value);
    const next = modes[(index + 1) % modes.length];
    updateAppearance(next);
}

const appearanceIcon = computed(() => {
    switch (appearance.value) {
        case 'light':
            return Sun;
        case 'dark':
            return Moon;
        case 'system':
            return SunMoon;
    }
});
</script>

<template>
    <header class="bg-primary text-primary-foreground">
        <div class="container mx-auto flex max-w-7xl items-center px-4 md:px-8 xl:px-0">
            <div class="flex flex-wrap items-center gap-4 py-3">
                <div class="flex items-center gap-2">
                    <Mail class="size-4" />
                    <a href="mailto:info@everstone.co.ke" class="text-sm">info@everstone.co.ke</a>
                </div>
                <div class="flex items-center gap-2">
                    <PhoneCall class="size-4" />
                    <p class="text-xs">+254 000 000 000</p>
                </div>
            </div>
            <nav class="ms-auto flex items-center gap-4 py-3 max-lg:hidden">
                <template v-for="(link, index) in smallNavLinks" :key="index">
                    <Link
                        :href="link.href"
                        :class="{ 'before:w-full': page.url == link.href }"
                        class="before:bg-primary-foreground relative text-sm before:absolute before:top-full before:h-[1px] before:w-0 before:duration-300 before:ease-in-out hover:before:w-full"
                    >
                        {{ link.title }}
                    </Link>
                </template>
            </nav>
        </div>
    </header>

    <header class="bg-primary-foreground">
        <div class="@container/nav mx-auto flex max-w-7xl items-center justify-between px-4 md:px-8 xl:px-0">
            <Link :href="route('home')" class="hidden items-center gap-2 py-5 max-lg:order-2 @xs/nav:flex">
                <div class="flex aspect-square size-8 items-center justify-center">
                    <AppLogoIcon />
                </div>
                <div class="ml-1 grid flex-1 text-left text-sm">
                    <span class="mb-0.5 truncate text-xl leading-none font-semibold">Everstone</span>
                </div>
            </Link>

            <!-- mobile menus -->
            <Sheet class="order-1 lg:hidden">
                <SheetTrigger as-child class="lg:hidden">
                    <Button variant="secondary" size="sm">
                        <ListMinus />
                    </Button>
                </SheetTrigger>

                <SheetContent side="left" class="lg:hidden">
                    <SheetHeader>
                        <SheetTitle>
                            <Link :href="route('home')" class="flex items-center gap-2">
                                <AppLogoIcon class="w-fit" />
                                <div class="ml-1 grid flex-1 text-left text-sm">
                                    <span class="mb-0.5 truncate text-xl leading-none font-semibold">Everstone</span>
                                </div>
                            </Link>
                        </SheetTitle>
                        <div class="relative mt-3 w-full items-center">
                            <input
                                type="text"
                                placeholder="Search..."
                                class="w-full border-none py-2 pe-3 pl-10 text-sm shadow-none outline-none placeholder:text-sm focus:border-none focus:shadow-none focus:ring-0 focus:outline-none"
                            />
                            <span class="absolute inset-y-0 start-0 flex items-center justify-center px-2">
                                <Search class="text-muted-foreground size-5" />
                            </span>
                        </div>
                    </SheetHeader>

                    <nav class="flex flex-col divide-y">
                        <template v-for="(category, index) in categories" :key="index">
                            <Link
                                :href="route('memorials.byCategory', category)"
                                class="px-5 py-3 text-sm hover:bg-stone-100 dark:hover:bg-stone-900"
                            >
                                {{ category }}
                            </Link>
                        </template>
                        <Link :href="route('memorials')" class="px-5 py-3 text-sm hover:bg-stone-100 dark:hover:bg-stone-900"> All Memorials </Link>

                        <template v-for="(link, index) in smallNavLinks" :key="index">
                            <Link :href="link.href" class="px-5 py-3 text-sm hover:bg-stone-100 dark:hover:bg-stone-900">
                                {{ link.title }}
                            </Link>
                        </template>

                        <template v-if="user">
                            <Link href="#" class="px-5 py-3 text-sm hover:bg-stone-100 dark:hover:bg-stone-900"> My Account </Link>
                            <Link href="#" class="px-5 py-3 text-sm hover:bg-stone-100 dark:hover:bg-stone-900"> My Orders </Link>
                        </template>
                    </nav>

                    <SheetFooter v-if="user">
                        <Link href="#" class="px-5 py-3 text-sm text-red-600 hover:bg-red-50"> Logout </Link>
                    </SheetFooter>
                </SheetContent>
            </Sheet>

            <!-- memorials links -->
            <nav class="hidden items-center gap-5 lg:flex">
                <template v-for="(category, index) in categories" :key="index">
                    <Link :href="route('memorials.byCategory', category)" class="shrink-0 text-sm">
                        {{ category }}
                    </Link>
                </template>
                <Link :href="route('memorials')" class="shrink-0 text-sm"> All Memorials </Link>
            </nav>

            <div class="flex items-center justify-between gap-3 py-5 max-lg:order-3">
                <button class="cursor-pointer p-2" @click="cycleAppearance">
                    <component :is="appearanceIcon" class="size-5" />
                </button>

                <Link href="#" class="max-lg:hidden">
                    <Search class="h-5 w-5" />
                </Link>
                <Link :href="route('cart')" class="relative">
                    <ShoppingCart class="h-5 w-5" />
                    <span
                        v-if="cart_items > 0"
                        class="absolute -top-2 -right-2 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] text-white"
                    >
                        {{ cart_items }}
                    </span>
                </Link>

                <Link v-if="!user" :href="route('login')">
                    <User class="h-5 w-5" />
                </Link>

                <DropdownMenu v-else class="max-lg:hidden">
                    <DropdownMenuTrigger>
                        <div class="relative w-fit">
                            <Avatar>
                                <AvatarImage :src="user?.avatar" />
                                <AvatarFallback>{{ user?.name.charAt(0) }}</AvatarFallback>
                            </Avatar>
                            <div
                                v-if="notificationsCount > 0"
                                class="absolute top-1 right-1 h-2.5 w-2.5 translate-x-1/2 -translate-y-1/2 rounded-full bg-red-500"
                            >
                                <span class="absolute inset-0 inline-flex h-2.5 w-2.5 animate-ping rounded-full bg-red-400 opacity-75"></span>
                            </div>
                        </div>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent class="min-w-44 p-0" align="end">
                        <Link
                            :href="route('customer.account')"
                            class="flex items-center gap-2 px-3 py-2 text-sm hover:bg-stone-100 dark:hover:bg-stone-900"
                        >
                            <User class="h-4 w-4" />
                            Account
                        </Link>
                        <Link
                            :href="route('customer.orders')"
                            class="flex items-center gap-2 px-3 py-2 text-sm hover:bg-stone-100 dark:hover:bg-stone-900"
                        >
                            <ShoppingBag class="h-4 w-4" />
                            Orders
                        </Link>
                        <Link
                            :href="route('customer.notifications')"
                            class="flex items-center gap-2 px-3 py-2 text-sm hover:bg-stone-100 dark:hover:bg-stone-900"
                        >
                            <Bell class="h-4 w-4" />
                            Notifications

                            <span
                                v-if="notificationsCount"
                                class="ms-auto flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] text-white"
                            >
                                {{ notificationsCount }}
                            </span>
                        </Link>
                        <div class="border-t px-2 py-2">
                            <Button @click="router.post(route('logout'))" class="w-full">Logout</Button>
                        </div>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </div>
    </header>
</template>

<style scoped>
/* Add styles here */
</style>
