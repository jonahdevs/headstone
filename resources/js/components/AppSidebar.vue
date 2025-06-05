<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import {
    Bell,
    Boxes,
    ClipboardList,
    FileText,
    Folder,
    HelpCircle,
    KeyRound,
    Landmark,
    LayoutGrid,
    LineChart,
    ReceiptText,
    Shield,
    Star,
    Tag,
    User,
    Users,
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const page = usePage();
const currentUrl = page.url;

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
        icon: LayoutGrid,
    },
];

const links = {
    userControl: [
        {
            title: 'Users',
            href: '/admin/users',
            icon: User,
        },
        {
            title: 'Customers',
            href: '/admin/customers',
            icon: Users,
        },
        {
            title: 'Roles',
            href: '/admin/roles',
            icon: Shield,
        },
        {
            title: 'Permissions',
            href: '/admin/permissions',
            icon: KeyRound,
        },
    ],
    memorialManagement: [
        {
            title: 'Categories',
            href: '/admin/categories',
            icon: Folder,
        },
        {
            title: 'Memorials',
            href: '/admin/memorials',
            icon: Landmark,
        },
        {
            title: 'Materials',
            href: '/admin/materials',
            icon: Boxes,
        },
        {
            title: 'Tags',
            href: '/admin/tags',
            icon: Tag,
        },
    ],
    orderSales: [
        {
            title: 'Orders',
            href: '/admin/orders',
            icon: ClipboardList,
        },
        {
            title: 'Transactions',
            href: '/admin/transactions',
            icon: ReceiptText,
        },
        {
            title: 'Quotations',
            href: '/admin/quotations',
            icon: FileText,
        },
    ],
    contentFeedback: [
        {
            title: 'Reviews',
            href: '/admin/reviews',
            icon: Star,
        },
        {
            title: 'FAQs',
            href: '/admin/faqs',
            icon: HelpCircle,
        },
        {
            title: 'Notifications',
            href: '/admin/notifications',
            icon: Bell,
        },
        {
            title: 'Reports',
            href: '/admin/reports',
            icon: LineChart,
        },
    ],
};
</script>

<template>
    <Sidebar collapsible="icon" variant="sidebar">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('admin.dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />

            <SidebarGroup>
                <SidebarGroupLabel>User & Access Control</SidebarGroupLabel>

                <SidebarMenu>
                    <SidebarMenuItem v-for="(link, i) in links.userControl" :key="i">
                        <SidebarMenuButton as-child :is-active="currentUrl.startsWith(link.href)">
                            <Link :href="link.href">
                                <component :is="link.icon" />
                                <span>{{ link.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>

            <!-- memorial management -->
            <SidebarGroup>
                <SidebarGroupLabel>Memorial Management</SidebarGroupLabel>

                <SidebarMenu>
                    <SidebarMenuItem v-for="(link, i) in links.memorialManagement" :key="i">
                        <SidebarMenuButton as-child :is-active="currentUrl.startsWith(link.href)">
                            <Link :href="link.href">
                                <component :is="link.icon" />
                                <span>{{ link.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>

            <SidebarGroup>
                <SidebarGroupLabel>Order & Sales</SidebarGroupLabel>

                <SidebarMenu>
                    <SidebarMenuItem v-for="(link, i) in links.orderSales" :key="i">
                        <SidebarMenuButton as-child :is-active="currentUrl.startsWith(link.href)">
                            <Link :href="link.href">
                                <component :is="link.icon" />
                                <span>{{ link.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>

            <SidebarGroup>
                <SidebarGroupLabel>Content & Others</SidebarGroupLabel>

                <SidebarMenu>
                    <SidebarMenuItem v-for="(link, i) in links.contentFeedback" :key="i">
                        <SidebarMenuButton as-child :is-active="currentUrl.startsWith(link.href)">
                            <Link :href="link.href">
                                <component :is="link.icon" />
                                <span>{{ link.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
