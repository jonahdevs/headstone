<script setup>
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Breadcrumb, BreadcrumbItem, BreadcrumbLink, BreadcrumbList, BreadcrumbPage, BreadcrumbSeparator } from '@/components/ui/breadcrumb';
import { Button } from '@/components/ui/button';
import { Link, router, usePage } from '@inertiajs/vue3';
import { LogOut } from 'lucide-vue-next';
import GuestLayout from './GuestLayout.vue';

const sidebarNavItems = [
    {
        title: 'Account',
        href: '/customer/account',
    },
    {
        title: 'Orders',
        href: '/customer/orders',
    },
    {
        title: 'Notifications',
        href: '/customer/notifications',
    },
];

const page = usePage();
const user = page.props.auth.user;

const props = defineProps({
    pageTitle: String,
    breadcrumbs: Array,
});

const currentPath = page.props.ziggy?.location ? new URL(page.props.ziggy.location).pathname : '';
</script>

<template>
    <GuestLayout>
        <section class="container mx-auto max-w-7xl space-y-6 px-4 pt-6 pb-10 md:px-8 xl:px-0">
            <!-- breadcrumb -->
            <Breadcrumb aria-label="Breadcrumb navigation">
                <BreadcrumbList>
                    <template v-for="(item, index) in breadcrumbs" :key="index">
                        <BreadcrumbItem>
                            <template v-if="index === breadcrumbs.length - 1">
                                <BreadcrumbPage class="">{{ item.title }}</BreadcrumbPage>
                            </template>
                            <template v-else>
                                <BreadcrumbLink as-child>
                                    <Link :href="item.href ?? '#'">{{ item.title }}</Link>
                                </BreadcrumbLink>
                            </template>
                        </BreadcrumbItem>
                        <BreadcrumbSeparator v-if="index !== breadcrumbs.length - 1">/</BreadcrumbSeparator>
                    </template>
                </BreadcrumbList>
            </Breadcrumb>

            <div class="flex flex-col gap-6 lg:flex-row lg:gap-8">
                <!-- sidebar -->
                <aside class="w-full rounded-md border shadow-xs lg:w-64">
                    <nav aria-label="Customer navigation" class="flex flex-col gap-2 p-4">
                        <!-- User info -->
                        <div class="flex flex-col items-center gap-2 border-b pb-4">
                            <Avatar class="h-20 w-20">
                                <AvatarImage :src="user.avatar" />
                                <AvatarFallback>{{ user.name.charAt(0) }}</AvatarFallback>
                            </Avatar>
                            <p class="text-muted-foreground truncate text-center text-sm">{{ user.email }}</p>
                        </div>

                        <!-- links -->
                        <div class="mt-2 w-full space-y-1">
                            <Button
                                v-for="item in sidebarNavItems"
                                :key="item.href"
                                variant="ghost"
                                as-child
                                :class="[
                                    'w-full justify-start text-sm',
                                    currentPath.startsWith(item.href)
                                        ? 'bg-stone-100 font-medium text-gray-800 hover:bg-gray-200 dark:text-gray-800 dark:hover:bg-stone-200'
                                        : 'text-muted-foreground hover:bg-gray-50',
                                ]"
                            >
                                <Link :href="item.href">{{ item.title }}</Link>
                            </Button>
                            <Button
                                variant="ghost"
                                class="w-full justify-start text-red-600 hover:bg-red-50 dark:text-red-400"
                                @click="router.post(route('logout'))"
                            >
                                <LogOut class="mr-2 h-4 w-4" /> Logout
                            </Button>
                        </div>
                    </nav>
                </aside>

                <!-- Main Content -->
                <div class="flex-1 overflow-hidden rounded-md border shadow-xs">
                    <!-- Header -->
                    <div class="border-b px-4 py-3">
                        <h5 v-if="pageTitle" class="text-base font-semibold">{{ pageTitle }}</h5>
                        <slot name="header" />
                    </div>

                    <!-- Content -->
                    <section class="space-y-6 p-4 lg:py-6">
                        <slot />
                    </section>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
