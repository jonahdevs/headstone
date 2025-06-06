<script setup>
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import AppLayout from '@/layouts/AppLayout.vue';
import { Deferred, Head, router, useForm, usePage, WhenVisible } from '@inertiajs/vue3';
import { useEchoModel } from '@laravel/echo-vue';
import { AlertCircle } from 'lucide-vue-next';
import { computed, onUnmounted } from 'vue';

// Your logic goes here
const props = defineProps({ notifications: Object, notification_paginations: Object });
const user = computed(() => usePage().props.auth.user);

const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
    },
    {
        title: 'Notifications',
        href: '/admin/notifications',
    },
];

const { channel } = useEchoModel('App.Models.User', user.value?.id);

channel().notification(() => {
    router.reload();
});

const updatePage = (page) => {
    router.get(
        route('admin.notifications'),
        { page },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

const markAllAsRead = () => {
    const form = useForm();
    form.post(route('admin.notifications.markAsRead'));
};

onUnmounted(() => {
    markAllAsRead();
});

const loadMore = () => {
    router.reload({
        data: {
            page: props.notification_paginations.current_page + 1,
        },
        only: ['notifications', 'notification_paginations'],
    });
};
</script>

<template>
    <Head title="Notifications" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageTitle> Notifications List </template>

        <div class="flex flex-col gap-4">
            <Deferred data="notifications">
                <template #fallback>
                    <div></div>
                </template>

                <template v-for="notification in notifications" :key="notification.id">
                    <div
                        class="rounded-md border p-4 shadow-sm"
                        :class="{
                            'border-l-4 border-l-blue-500 bg-blue-50 dark:bg-blue-950': !notification.read_at,
                            'bg-card': notification.read_at,
                        }"
                    >
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="flex flex-1 items-center gap-1">
                                    <template v-if="notification.type.includes('order')">🛒</template>
                                    <template v-else-if="notification.type.includes('quotation')">📃</template>
                                    <template v-else-if="notification.type.includes('user')">👤</template>
                                    <template v-else-if="notification.type.includes('message')">📩</template>
                                    <p class="ms-1 text-sm font-medium" v-html="notification.data.title"></p>
                                </div>
                                <p class="text-muted-foreground mt-1 text-xs">{{ notification.time }}</p>
                            </div>
                            <span
                                v-if="!notification.read_at"
                                class="inline-block rounded-full bg-blue-600 px-2 py-0.5 text-xs text-white dark:bg-blue-400 dark:text-black"
                                >New</span
                            >
                        </div>
                    </div>
                </template>

                <Alert
                    v-if="notifications.length < 1"
                    class="border-blue-200 bg-blue-50 text-blue-600 dark:border-blue-800 dark:bg-blue-950 dark:text-blue-500"
                >
                    <AlertCircle />
                    <AlertTitle>No notifications found</AlertTitle>
                    <AlertDescription class="text-blue-600 dark:text-blue-400">
                        You're all caught up for now. We'll notify you when there's an update.
                    </AlertDescription>
                </Alert>
            </Deferred>
            <!-- <button @click="loadMore">Load more</button> -->

            <WhenVisible
                always
                :params="{
                    data: {
                        page: notification_paginations.current_page + 1,
                    },
                    only: ['notifications', 'notification_paginations'],
                }"
            >
                <div v-if="notification_paginations.current_page >= notification_paginations.last_page">You have reached the end</div>
            </WhenVisible>
        </div>
    </AppLayout>
</template>
