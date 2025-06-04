<script setup>
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Skeleton } from '@/components/ui/skeleton';
import AppLayout from '@/layouts/AppLayout.vue';
import { Deferred, Head, Link, usePage } from '@inertiajs/vue3';
import { Copy, KeyRound, Users } from 'lucide-vue-next';
import { computed, onMounted } from 'vue';
import { toast } from 'vue-sonner';

defineProps({ roles: Object });
const flash = computed(() => usePage().props.flash);

const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
    },
    {
        title: 'Roles',
        href: '/admin/roles',
    },
];

onMounted(() => {
    let msg = flash.value.message;

    if (msg && msg.type === 'success') {
        toast.success(msg.body);
    } else if (msg && msg.type === 'error') {
        toast.error(msg.body);
    }
});
</script>

<template>
    <Head title="Roles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageTitle> Roles </template>

        <div class="flex flex-wrap gap-5">
            <Deferred data="roles">
                <template #fallback>
                    <template v-for="i in 3" :key="i">
                        <div class="bg-card w-full max-w-md rounded-xl border p-6">
                            <!-- header -->
                            <div class="mb-4 flex flex-wrap items-center justify-between">
                                <Skeleton class="h-4 w-32 rounded" />

                                <!-- Avatars -->
                                <div class="flex items-center -space-x-3">
                                    <Skeleton v-for="n in 5" :key="n" class="size-9 rounded-full border-2 bg-stone-200 shadow-sm dark:bg-stone-800" />
                                </div>
                            </div>

                            <!-- Details -->
                            <div class="space-y-3">
                                <div class="flex items-center gap-2">
                                    <Skeleton class="h-4 w-4" />
                                    <Skeleton class="h-4 w-24" />
                                </div>

                                <div class="flex items-center gap-2">
                                    <Skeleton class="h-4 w-4" />
                                    <Skeleton class="h-4 w-20" />
                                </div>
                            </div>

                            <!-- footer -->
                            <div class="mt-4 flex items-center justify-between">
                                <Skeleton class="h-3 w-16 rounded" />
                                <Skeleton class="h-5 w-5 rounded" />
                            </div>
                        </div>
                    </template>
                </template>

                <template v-for="role in roles.data" :key="role.id">
                    <div class="w-full max-w-md rounded-xl border p-6">
                        <!-- header -->
                        <div class="mb-4 flex flex-wrap items-center justify-between">
                            <p class="text-lg font-semibold capitalize">{{ role.name }}</p>

                            <!-- avatars -->
                            <div class="flex items-center -space-x-3">
                                <template v-for="user in role.users.slice(0, 4)" :key="user.id">
                                    <Avatar class="transition-translate z-10 size-9 border-2 shadow-sm duration-200 hover:z-30 hover:scale-105">
                                        <AvatarImage :src="user.avatar" alt="user avatar" />
                                        <AvatarFallback>{{ user.name?.charAt(0) ?? 'U' }}</AvatarFallback>
                                    </Avatar>
                                </template>

                                <!-- Extra User Count -->
                                <div
                                    v-if="role.users_count > 4"
                                    class="text-muted-foreground z-10 grid size-9 place-items-center rounded-full border-2 bg-stone-200 text-xs font-semibold shadow-sm dark:bg-stone-800"
                                >
                                    +{{ role.users_count - 4 }}
                                </div>
                            </div>
                        </div>

                        <!-- Details -->
                        <div class="text-muted-foreground space-y-2 text-sm">
                            <div class="flex items-center gap-2">
                                <KeyRound class="stroke-muted-foreground h-4 w-4" />
                                <span>{{ role.permissions_count }} Permissions</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <Users class="stroke-muted-foreground h-4 w-4" />
                                <span>{{ role.users_count }} Users</span>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="mt-4 flex items-center justify-between">
                            <Link
                                :href="route('admin.roles.edit', role.id)"
                                class="text-sm text-blue-700 transition-all duration-150 hover:underline dark:text-blue-400"
                                >Edit Role</Link
                            >
                            <Copy class="stroke-muted-foreground h-5 w-5" />
                        </div>
                    </div>
                </template>
            </Deferred>
        </div>
    </AppLayout>
</template>
