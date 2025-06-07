<script setup>
import NoDataFound from '@/components/NoDataFound.vue';
import Pagination from '@/components/Pagination.vue';
import CustomerLayout from '@/layouts/CustomerLayout.vue';
import { Deferred, Head, router, useForm, usePage } from '@inertiajs/vue3';
import { useEchoModel } from '@laravel/echo-vue';
import { computed, onUnmounted } from 'vue';

// Your logic goes here
defineProps({ notifications: Object });
const user = computed(() => usePage().props.auth.user);
const { channel } = useEchoModel('App.Models.User', user.value?.id);

const breadcrumbs = [
    {
        title: 'Home',
        href: '/',
    },
    {
        title: 'Notifications',
        href: '/notifications',
    },
];

channel().notification(() => {
    router.reload();
});

const updatePage = (page) => {
    router.get(
        route('notifications.index'),
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
    form.post(route('customer.notifications.markAsRead'));
};

onUnmounted(() => {
    markAllAsRead();
});
</script>

<template>
    <Head title="Notifications" />

    <CustomerLayout :pageTitle="'Notifications'" :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4">
            <Deferred data="notifications">
                <template #fallback>
                    <div></div>
                </template>

                <template v-for="notification in notifications.data" :key="notification.id">
                    <div
                        class="rounded-md border p-4 shadow-sm"
                        :class="{
                            'border-l-4 border-l-blue-500 bg-blue-50 dark:bg-blue-950 dark:text-white': !notification.read_at,
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
                                    <p class="ms-1 text-sm" v-html="notification.data.title"></p>
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

                <section
                    v-if="notifications.data.length < 1"
                    class="text-muted-foreground flex min-h-[50svh] w-full flex-col items-center justify-center gap-2"
                >
                    <NoDataFound />
                </section>
            </Deferred>

            <Pagination v-if="notifications" :meta="notifications.meta" :onPageChange="updatePage" />
        </div>
    </CustomerLayout>
</template>
