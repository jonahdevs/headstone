<script setup lang="ts">
import { SidebarTrigger } from '@/components/ui/sidebar';
import { useAppearance } from '@/composables/useAppearance';
import type { BreadcrumbItemType } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { useEchoModel } from '@laravel/echo-vue';
import axios from 'axios';
import { Bell, Moon, Sun, SunMoon } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Button } from './ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuLabel, DropdownMenuTrigger } from './ui/dropdown-menu';

const { appearance, updateAppearance } = useAppearance();

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItemType[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const modes = ['light', 'dark', 'system'] as const;
const page = usePage();
const user = computed(() => page.props.auth.user);
const unreadNotifications = ref({});
const notificationsCount = ref(page.props.notificationsCount);
const { channel } = useEchoModel('App.Models.User', user.value.id);

channel().notification(() => {
    notificationsCount.value++;
});

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

const getUnreadNotifications = () => {
    if (notificationsCount.value === 0) {
        return;
    }
    axios.get('/admin/notifications/unread').then((response) => {
        unreadNotifications.value = response.data;
    });
};
</script>

<template>
    <header
        class="border-sidebar-border/70 flex h-16 shrink-0 items-center gap-2 border-b px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
        </div>

        <div class="ms-auto flex items-center gap-2">
            <DropdownMenu>
                <DropdownMenuTrigger @click="getUnreadNotifications">
                    <button class="relative cursor-pointer p-2">
                        <Bell class="size-5" />

                        <span
                            v-if="notificationsCount > 0"
                            class="absolute top-0 right-0 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] text-white"
                        >
                            {{ notificationsCount }}
                        </span>
                    </button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="w-80 rounded-xl border p-0 shadow-sm">
                    <DropdownMenuLabel class="border-b p-3">
                        <p class="font-medium tracking-wide">Notifications</p>
                    </DropdownMenuLabel>

                    <div
                        v-if="unreadNotifications.length > 0"
                        class="bg-muted max-h-72 divide-y divide-stone-300 overflow-auto text-sm dark:divide-stone-700"
                    >
                        <div v-for="notification in unreadNotifications" :key="notification.id" class="hover:bg-secondary gap-5 space-y-1 p-4">
                            <div class="flex flex-1">
                                <template v-if="notification.type.includes('order')">🛒</template>
                                <template v-else-if="notification.type.includes('quotation')">📃</template>
                                <template v-else-if="notification.type.includes('user')">👤</template>
                                <template v-else-if="notification.type.includes('message')">📩</template>
                                <p class="ms-1 text-sm" v-html="notification.data.title"></p>
                            </div>
                            <p class="text-muted-foreground text-xs">{{ notification.time }}</p>
                        </div>
                    </div>

                    <div v-else class="text-muted-foreground bg-stone-100 p-4 text-center text-sm dark:bg-stone-900">No new notifications</div>

                    <Button @click="router.visit(route('admin.notifications'))" class="w-full rounded-none"> View all notifications </Button>
                </DropdownMenuContent>
            </DropdownMenu>

            <button class="cursor-pointer p-2" @click="cycleAppearance">
                <component :is="appearanceIcon" class="size-5" />
            </button>
        </div>
    </header>
</template>
