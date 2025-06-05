<script setup>
import MetricCard from '@/components/MetricCard.vue';
import MetricFallback from '@/components/MetricFallback.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { RangeCalendar } from '@/components/ui/range-calendar';
import { Skeleton } from '@/components/ui/skeleton';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useAppearance } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import { cn } from '@/lib/utils';
import { Deferred, Head, Link, router, usePage } from '@inertiajs/vue3';
import { DateFormatter, getLocalTimeZone, parseDate } from '@internationalized/date';
import { useEchoPublic } from '@laravel/echo-vue';
import { useDebounce, useDebounceFn } from '@vueuse/core';
import Chart from 'chart.js/auto';
import { CalendarIcon } from 'lucide-vue-next';
import { nextTick, ref, watch } from 'vue';

const props = defineProps({
    totalSales: String,
    totalOrders: Number,
    totalQuotations: Number,
    totalUsers: Number,
    orders: Object,
    salesTrend: Object,
    topSellingMemorials: Object,
    filters: Object,
});

const page = usePage();
const start_date = ref(props.filters?.start_date ? parseDate(props.filters?.start_date.slice(0, 10)) : null);
const end_date = ref(props.filters?.end_date ? parseDate(props.filters?.end_date.slice(0, 10)) : null);
const { appearance, updateAppearance } = useAppearance();

const df = new DateFormatter('en-US', {
    dateStyle: 'medium',
});

const applyFilters = (value) => {
    start_date.value = value?.start || null;
    end_date.value = value?.end || null;

    const formattedFilters = {
        start_date: start_date.value ? start_date.value.toDate(getLocalTimeZone()).toISOString() : null,
        end_date: end_date.value ? end_date.value.toDate(getLocalTimeZone()).toISOString() : null,
    };

    router.get(route('admin.dashboard'), formattedFilters, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const clearFilters = () => {
    start_date.value = null;
    end_date.value = null;
    applyFilters();
};

useEchoPublic('admins', ['.order.created'], (e) => {
    useDebounceFn(() => {
        router.reload({ only: ['orders', 'totalSales', 'totalOrders', 'salesTrend', 'topSellingMemorials'] });
    }, 10000);
});

useEchoPublic('admins', ['.quotation.created'], (e) => {
    useDebounce(() => {
        router.reload({ only: ['totalQuotations'] });
    }, 10000);
});

useEchoPublic('admins', ['.user.created'], (e) => {
    useDebounce(() => {
        router.reload({ only: ['totalUsers'] });
    }, 10000);
});

let salesCanvas = ref(null);
let topSellingCanvas = ref(null);

let salesChart = null;
let topSellingChart = null;

// console.log('Dashboard Props:', props);
watch(
    () => props.salesTrend,
    async (newData) => {
        if (newData && Object.keys(newData).length > 0) {
            await nextTick();
            renderSalesChart(newData);
        }
    },
    { immediate: true, deep: true },
);

watch(
    () => props.topSellingMemorials,
    async (newData) => {
        if (newData && newData.length > 0) {
            await nextTick();
            renderTopSellingChart(newData);
        }
    },
    { immediate: true },
);

const MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

const renderSalesChart = (data) => {
    const ctx = salesCanvas.value?.getContext('2d');
    if (!ctx) return;

    if (salesChart) {
        salesChart.destroy();
    }

    const isDark = appearance.value === 'dark' || (appearance.value === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);

    const datasets = Object.entries(data).map(([year, sales], index) => {
        const color = ['#3b82f6', '#10b981'][index] || '#64748b';

        return {
            label: year,
            data: MONTHS.map((month) => sales[month] || 0),
            fill: true,
            borderColor: color,
            backgroundColor(context) {
                const { chartArea, ctx } = context.chart;
                if (!chartArea) return 'rgba(59, 130, 246, 0.5)';
                const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                gradient.addColorStop(0, `${color}80`);
                gradient.addColorStop(1, `${color}00`);
                return gradient;
            },
            tension: 0.4,
            pointBackgroundColor: color,
            pointRadius: 3,
        };
    });

    salesChart = new Chart(ctx, {
        type: 'line',
        options: {
            layout: { padding: 24 },
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 1000, easing: 'easeOutQuart' },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: (value) => `Ksh ${value}`, color: isDark ? '#e5e7eb' : '#374151' },
                    grid: { drawBorder: false, color: isDark ? '#374151' : '#e5e7eb' },
                },
                x: {
                    ticks: {
                        color: isDark ? '#e5e7eb' : '#374151',
                    },
                    grid: { display: false, color: isDark ? '#374151' : '#e5e7eb' },
                },
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Sales Per Month',
                    align: 'start',
                    color: isDark ? '#f3f4f6' : '#111827',
                },
                tooltip: {
                    callbacks: {
                        label: (context) => `Ksh ${context.parsed.y}`,
                    },
                },
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        boxHeight: 6,
                        boxWidth: 6,
                        color: isDark ? '#d1d5db' : '#374151',
                    },
                },
            },
        },
        data: {
            labels: MONTHS,
            datasets,
        },
    });
};

const renderTopSellingChart = (data) => {
    const ctx = topSellingCanvas.value?.getContext('2d');
    if (!ctx) return;

    if (topSellingChart) {
        topSellingChart.destroy();
    }

    // Detect if dark mode is active
    const isDark = appearance.value === 'dark' || (appearance.value === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);

    // Define color sets
    const lightColors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#6366f1'];
    const darkColors = ['#60a5fa', '#34d399', '#fbbf24', '#f87171', '#818cf8'];

    topSellingChart = new Chart(ctx, {
        type: 'pie',
        options: {
            // cutout: '70%',
            spacing: 10,
            layout: {
                padding: 24,
            },
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 1000, easing: 'easeOutQuart' },
            plugins: {
                title: {
                    display: true,
                    text: 'Top Selling Products',
                    align: 'start',
                    color: isDark ? '#f3f4f6' : '#111827',
                },
                legend: {
                    display: true,
                    position: 'right',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        boxHeight: 6,
                        boxWidth: 6,
                        color: isDark ? '#d1d5db' : '#374151',
                    },
                },
            },
        },
        data: {
            labels: data.map((item) => item.label),
            datasets: [
                {
                    label: 'Top Selling Memorials',
                    data: data.map((item) => item.value),
                    backgroundColor: isDark ? darkColors : lightColors,
                    hoverOffset: 4,
                    borderRadius: 20,
                },
            ],
        },
    });
};

const orderStatusClasses = (status) => {
    switch (status) {
        case 'completed':
            return 'bg-emerald-600 text-white dark:bg-emerald-400 dark:text-black';
        case 'processing':
            return 'bg-blue-600 text-white dark:bg-blue-400 dark:text-black';
        case 'pending':
            return 'bg-yellow-400 text-black dark:bg-yellow-300 dark:text-black';
        case 'cancelled':
            return 'bg-rose-600 text-white dark:bg-rose-400 dark:text-black';
        default:
            return 'bg-slate-500 text-white dark:bg-slate-400 dark:text-black';
    }
};

const transactionStatusClasses = (status) => {
    switch (status) {
        case 'success':
            return 'bg-green-600 text-white dark:bg-green-400 dark:text-black';
        case 'failed':
        case 'reversed':
            return 'bg-red-600 text-white dark:bg-red-400 dark:text-black';
        case 'pending':
            return 'bg-yellow-400 text-black dark:bg-yellow-300 dark:text-black';
        case 'processing':
            return 'bg-blue-600 text-white dark:bg-blue-400 dark:text-black';
        case 'queued':
            return 'bg-indigo-600 text-white dark:bg-indigo-400 dark:text-black';
        case 'ongoing':
            return 'bg-cyan-600 text-white dark:bg-cyan-300 dark:text-black';
        case 'abandoned':
            return 'bg-gray-600 text-white dark:bg-gray-400 dark:text-black';
        default:
            return 'bg-slate-500 text-white dark:bg-slate-400 dark:text-black';
    }
};

watch(appearance, () => {
    renderSalesChart(props.salesTrend, appearance);
    renderTopSellingChart(props.topSellingMemorials, appearance);
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <template #pageActions>
            <div class="flex flex-wrap items-center gap-2">
                <Popover>
                    <PopoverTrigger as-child>
                        <Button
                            variant="outline"
                            :class="cn('w-full max-w-[280px] justify-start text-left font-normal', !start_date && 'text-muted-foreground')"
                        >
                            <CalendarIcon class="mr-2 h-4 w-4" />
                            <template v-if="start_date">
                                <template v-if="end_date">
                                    {{ df.format(start_date.toDate(getLocalTimeZone())) }} - {{ df.format(end_date.toDate(getLocalTimeZone())) }}
                                </template>

                                <template v-else>
                                    {{ df.format(start_date.toDate(getLocalTimeZone())) }}
                                </template>
                            </template>
                            <template v-else> Pick a date </template>
                        </Button>
                    </PopoverTrigger>
                    <PopoverContent class="w-auto p-0">
                        <RangeCalendar initial-focus :number-of-months="1" @update:modelValue="(value) => applyFilters(value)" />
                    </PopoverContent>
                </Popover>

                <Button
                    v-if="page.url.includes('start_date') || page.url.includes('end_date')"
                    @click.prevent="clearFilters"
                    variant="link"
                    as="button"
                >
                    Clear Filters
                </Button>
            </div>
        </template>

        <section class="@container space-y-6">
            <div class="grid grid-cols-1 gap-5 @md:grid-cols-2 @4xl:grid-cols-4">
                <Deferred data="totalSales">
                    <template #fallback>
                        <MetricFallback />
                    </template>
                    <MetricCard icon="Rocket" title="Total Sales" :value="totalSales" trend="12%" trendDirection="up" />
                </Deferred>

                <Deferred data="totalOrders">
                    <template #fallback>
                        <MetricFallback />
                    </template>
                    <MetricCard icon="ShoppingBag" title="Total Orders" :value="totalOrders" trend="12%" trendDirection="up" />
                </Deferred>

                <Deferred data="totalQuotations">
                    <template #fallback>
                        <MetricFallback />
                    </template>
                    <MetricCard icon="FileText" title="Total Quotations" :value="totalQuotations" trend="12%" trendDirection="up" />
                </Deferred>

                <Deferred data="totalUsers">
                    <template #fallback>
                        <MetricFallback />
                    </template>
                    <MetricCard icon="Users" title="Total Users" :value="totalUsers" trend="12%" trendDirection="down" />
                </Deferred>
            </div>

            <div class="grid grid-cols-1 gap-5 @4xl:grid-cols-3">
                <Deferred data="salesTrend">
                    <template #fallback>
                        <div class="relative h-[420px] min-h-40 w-full animate-pulse rounded-xl border p-4 @4xl:col-span-2">
                            <Skeleton class="mb-6 h-6 w-32 rounded" />

                            <div class="space-y-4">
                                <div class="flex h-56 items-end gap-4">
                                    <Skeleton class="h-[30%] w-full rounded" />
                                    <Skeleton class="h-[50%] w-full rounded" />
                                    <Skeleton class="h-[70%] w-full rounded" />
                                    <Skeleton class="h-[40%] w-full rounded" />
                                    <Skeleton class="h-[60%] w-full rounded" />
                                    <Skeleton class="h-[30%] w-full rounded" />
                                    <Skeleton class="h-[80%] w-full rounded" />
                                    <Skeleton class="h-[45%] w-full rounded" />
                                    <Skeleton class="h-[55%] w-full rounded" />
                                    <Skeleton class="h-[25%] w-full rounded" />
                                    <Skeleton class="h-[65%] w-full rounded" />
                                    <Skeleton class="h-[35%] w-full rounded" />
                                </div>
                                <Skeleton class="h-3 w-full rounded" />
                            </div>
                        </div>
                    </template>
                    <div class="relative h-[420px] min-h-40 w-full rounded-xl border p-4 @4xl:col-span-2">
                        <canvas ref="salesCanvas"></canvas>
                    </div>
                </Deferred>

                <Deferred data="topSellingMemorials">
                    <template #fallback>
                        <div class="relative flex h-[420px] w-full animate-pulse rounded-xl border p-4 @4xl:col-span-1">
                            <!-- Doughnut circle skeleton -->
                            <div class="flex w-1/2 items-center justify-center">
                                <div class="h-36 w-36 rounded-full border-[8px]"></div>
                            </div>

                            <!-- Legend labels skeleton -->
                            <div class="flex w-1/2 flex-col justify-center space-y-4 pl-4">
                                <div class="flex items-center gap-3">
                                    <Skeleton class="h-3 w-3 rounded-full" />
                                    <Skeleton class="h-3 w-24 rounded" />
                                </div>
                                <div class="flex items-center gap-3">
                                    <Skeleton class="h-3 w-3 rounded-full" />
                                    <Skeleton class="h-3 w-20 rounded" />
                                </div>
                                <div class="flex items-center gap-3">
                                    <Skeleton class="h-3 w-3 rounded-full" />
                                    <Skeleton class="h-3 w-28 rounded" />
                                </div>
                            </div>
                        </div>
                    </template>

                    <div class="relative h-[420px] w-full rounded-xl border p-4 @4xl:col-span-1">
                        <canvas ref="topSellingCanvas"></canvas>
                    </div>
                </Deferred>
            </div>

            <div class="space-y-5 rounded-xl border p-5">
                <h2 class="mb-5 text-lg font-semibold">Recent Orders</h2>

                <div class="overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Order ID</TableHead>
                                <TableHead>Customer</TableHead>
                                <TableHead>Fullfillment Status</TableHead>
                                <TableHead>Payment Status</TableHead>
                                <TableHead>Date</TableHead>
                                <TableHead>Items</TableHead>
                                <TableHead>Total</TableHead>
                                <TableHead>Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <Deferred data="orders">
                                <template #fallback>
                                    <template v-for="n in 5" :key="n">
                                        <TableRow>
                                            <TableCell class="py-4">
                                                <Skeleton class="h-4 w-16" />
                                            </TableCell>
                                            <TableCell class="py-4">
                                                <Skeleton class="h-4 w-32" />
                                            </TableCell>
                                            <TableCell class="py-4">
                                                <Skeleton class="h-4 w-28" />
                                            </TableCell>
                                            <TableCell class="py-4">
                                                <Skeleton class="h-4 w-24" />
                                            </TableCell>
                                            <TableCell class="py-4">
                                                <Skeleton class="h-4 w-20" />
                                            </TableCell>
                                            <TableCell class="py-4">
                                                <Skeleton class="h-4 w-20" />
                                            </TableCell>
                                            <TableCell class="py-4">
                                                <Skeleton class="h-4 w-20" />
                                            </TableCell>
                                            <TableCell class="py-4">
                                                <Skeleton class="h-4 w-24" />
                                            </TableCell>
                                        </TableRow>
                                    </template>
                                </template>
                                <TableRow v-for="order in orders.data" :key="order.id">
                                    <TableCell>#{{ order.id }}</TableCell>
                                    <TableCell>
                                        <div class="flex items-center gap-2">
                                            <p class="line-clamp-1 w-full">{{ order.customer }}</p>
                                        </div>
                                    </TableCell>
                                    <TableCell>
                                        <Badge :class="orderStatusClasses(order.status)" class="capitalize">{{ order.status }} </Badge>
                                    </TableCell>
                                    <TableCell>
                                        <Badge :class="transactionStatusClasses(order.payment_status)" class="capitalize"
                                            >{{ order.payment_status }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell>{{ order.placed_on }}</TableCell>
                                    <TableCell>{{ order.items }}</TableCell>
                                    <TableCell>{{ order.total }}</TableCell>
                                    <TableCell>
                                        <div class="flex items-center gap-2">
                                            <Link :href="route('admin.orders.show', order.id)" class="hover:text-blue-500 hover:underline">
                                                <span>View</span>
                                            </Link>
                                        </div>
                                    </TableCell>
                                </TableRow>

                                <TableRow v-if="orders.data.length == 0">
                                    <TableCell colspan="8" class="py-4 text-center">
                                        <p class="text-slate-500">No orders found</p>
                                    </TableCell>
                                </TableRow>
                            </Deferred>
                        </TableBody>
                    </Table>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
