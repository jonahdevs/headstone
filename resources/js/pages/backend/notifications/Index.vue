<script setup>
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import AppLayout from '@/layouts/AppLayout.vue';
import { Deferred, Head, router, usePage } from '@inertiajs/vue3';
import { useEchoModel } from '@laravel/echo-vue';
import axios from 'axios';
import { AlertCircle } from 'lucide-vue-next';
import { computed, onUnmounted } from 'vue';

// Your logic goes here
const props = defineProps({ notifications: Object });
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

const markAllAsRead = async () => {
    await axios.post(route('admin.notifications.markAsRead'));
};

onUnmounted(() => {
    markAllAsRead();
});
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

                <template v-for="notification in notifications.data" :key="notification.id">
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
                    v-if="notifications.data.length < 1"
                    class="border-blue-200 bg-blue-50 text-blue-600 dark:border-blue-800 dark:bg-blue-950 dark:text-blue-500"
                >
                    <AlertCircle />
                    <AlertTitle>No notifications found</AlertTitle>
                    <AlertDescription class="text-blue-600 dark:text-blue-400">
                        You're all caught up for now. We'll notify you when there's an update.
                    </AlertDescription>
                </Alert>
            </Deferred>

            <Pagination v-if="notifications" :meta="notifications.meta" :onPageChange="updatePage" />
        </div>
    </AppLayout>
</template>
