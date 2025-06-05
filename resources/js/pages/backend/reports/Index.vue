<script setup>
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuRadioGroup, DropdownMenuRadioItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { RangeCalendar } from '@/components/ui/range-calendar';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Skeleton } from '@/components/ui/skeleton';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { cn } from '@/lib/utils';
import { Deferred, Head, router } from '@inertiajs/vue3';
import { DateFormatter, getLocalTimeZone, parseDate } from '@internationalized/date';
import axios from 'axios';
import { CalendarIcon, Download, Loader } from 'lucide-vue-next';
import { reactive, ref } from 'vue';
import { toast } from 'vue-sonner';

// Your logic goes here
const props = defineProps({
    resources: Object,
    resource: String,
    filters: Object,
    resourceFilters: Object,
    data: Object,
});

const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
    },
    {
        title: 'Reports',
        href: '/admin/reports',
    },
];

const selectedResource = ref(props.resource);
const filters = reactive({ ...props.filters });
const documentType = ref('');
const generatingReport = ref(false);

const start_date = ref(props.filters?.start_date ? parseDate(props.filters?.start_date.slice(0, 10)) : null);
const end_date = ref(props.filters?.end_date ? parseDate(props.filters?.end_date.slice(0, 10)) : null);

const df = new DateFormatter('en-US', {
    dateStyle: 'medium',
});

const resourceUpdated = () => {
    Object.keys(filters).forEach((key) => {
        filters[key] = '';
    });

    start_date.value = null;
    end_date.value = null;

    router.visit(route('admin.reports', selectedResource.value), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const applyFilters = (value) => {
    start_date.value = value?.start || null;
    end_date.value = value?.end || null;

    // Filter out empty values
    const filteredData = Object.fromEntries(Object.entries(filters).filter(([_, value]) => value !== ''));

    Object.assign(filteredData, {
        start_date: start_date.value?.toDate(getLocalTimeZone()).toISOString() ?? null,
        end_date: end_date.value?.toDate(getLocalTimeZone()).toISOString() ?? null,
    });

    router.visit(route('admin.reports', selectedResource.value), {
        data: filteredData,
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const clearFilters = () => {
    Object.keys(filters).forEach((key) => {
        filters[key] = '';
    });
    selectedResource.value = '';
    start_date.value = null;
    end_date.value = null;
    applyFilters();
};

const generateReport = () => {
    if (generatingReport.value) return;

    generatingReport.value = true;
    const resource = selectedResource.value;

    if (!resource) {
        toast.error('Please select a resource');
        generatingReport.value = false;
        return;
    }

    const filterParams = Object.fromEntries(Object.entries(filters).filter(([_, value]) => value));

    // Determine file extension and MIME type
    let fileExtension = 'pdf';
    let mimeType = 'application/pdf';

    switch (documentType.value) {
        case 'excel':
            fileExtension = 'xlsx';
            mimeType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            break;
        case 'csv':
            fileExtension = 'csv';
            mimeType = 'text/csv';
            break;
        case 'pdf':
        default:
            fileExtension = 'pdf';
            mimeType = 'application/pdf';
            break;
    }

    axios
        .get(route('admin.reports.generate', resource), {
            params: {
                filters: filterParams,
                documentType: documentType.value,
            },
            responseType: 'blob',
        })
        .then((response) => {
            const blob = new Blob([response.data], { type: mimeType });
            const url = window.URL.createObjectURL(blob);

            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `${resource}-report.${fileExtension}`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            toast.success(`Your ${documentType.value.toUpperCase()} report has been generated`);
        })
        .catch(() => {
            toast.error(`Failed to generate the ${documentType.value.toUpperCase()} report`);
        })
        .finally(() => {
            generatingReport.value = false;
            documentType.value = '';
        });
};
</script>

<template>
    <Head title="Reports" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="space-y-6">
            <div class="flex w-full flex-wrap items-center gap-4">
                <!-- Resource Selector -->
                <Select v-model="selectedResource" @update:modelValue="resourceUpdated">
                    <SelectTrigger>
                        <SelectValue placeholder="Select Resource" class="capitalize" />
                    </SelectTrigger>
                    <SelectContent class="capitalize">
                        <SelectItem v-for="resource in resources" :key="resource" :value="resource">
                            {{ resource }}
                        </SelectItem>
                    </SelectContent>
                </Select>

                <!-- Dynamic Filters -->
                <template v-for="(options, key) in props.resourceFilters" :key="key">
                    <Select v-model="filters[key]" @update:modelValue="applyFilters">
                        <SelectTrigger>
                            <SelectValue :placeholder="`Select ${key}`" class="capitalize" />
                        </SelectTrigger>
                        <SelectContent class="capitalize">
                            <SelectItem v-for="option in options" :key="option" :value="option">
                                {{ option }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </template>

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

                <Button @click.prevent="clearFilters" variant="link" as="button"> Clear Filters </Button>

                <DropdownMenu class="ms-auto">
                    <DropdownMenuTrigger>
                        <Button variant="secondary">
                            <Download />
                            Generate Report
                            <Loader v-if="generatingReport" class="animate-spin" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent>
                        <DropdownMenuRadioGroup v-model="documentType" @update:modelValue="generateReport">
                            <DropdownMenuRadioItem value="pdf"> PDF </DropdownMenuRadioItem>
                            <DropdownMenuRadioItem value="excel"> Excel </DropdownMenuRadioItem>
                            <DropdownMenuRadioItem value="csv"> CSV </DropdownMenuRadioItem>
                        </DropdownMenuRadioGroup>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>

            <section class="border-8 p-5 shadow-sm">
                <Table>
                    <TableHeader>
                        <template v-if="resource === 'users' || resource === 'customers'">
                            <TableRow>
                                <TableHead>Name</TableHead>
                                <TableHead>Email</TableHead>
                                <TableHead>Phone</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Joined On</TableHead>
                            </TableRow>
                        </template>

                        <template v-else-if="resource === 'memorials'">
                            <TableRow>
                                <TableHead>ID</TableHead>
                                <TableHead>title</TableHead>
                                <TableHead>Price</TableHead>
                                <TableHead>Sale Price</TableHead>
                                <TableHead>Sales</TableHead>
                            </TableRow>
                        </template>

                        <template v-else-if="resource === 'orders'">
                            <TableRow>
                                <TableHead>ID</TableHead>
                                <TableHead>customer</TableHead>
                                <TableHead>Fullfillment Status</TableHead>
                                <TableHead>Payment Status</TableHead>
                                <TableHead>Items</TableHead>
                                <TableHead>Total</TableHead>
                                <TableHead>Date</TableHead>
                            </TableRow>
                        </template>

                        <template v-else-if="resource === 'transactions'">
                            <TableRow>
                                <TableHead>ID</TableHead>
                                <TableHead>Transaction Id</TableHead>
                                <TableHead>Order Id</TableHead>
                                <TableHead>Customer</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Amount</TableHead>
                                <TableHead>Date</TableHead>
                            </TableRow>
                        </template>

                        <template v-else>
                            <TableRow>
                                <TableHead class="py-4">
                                    <Skeleton class="h-4 w-16" />
                                </TableHead>
                                <TableHead class="py-4">
                                    <Skeleton class="h-4 w-32" />
                                </TableHead>
                                <TableHead class="py-4">
                                    <Skeleton class="h-4 w-28" />
                                </TableHead>
                                <TableHead class="py-4">
                                    <Skeleton class="h-4 w-24" />
                                </TableHead>
                                <TableHead class="py-4">
                                    <Skeleton class="h-4 w-20" />
                                </TableHead>
                                <TableHead class="py-4">
                                    <Skeleton class="h-4 w-20" />
                                </TableHead>
                                <TableHead class="py-4">
                                    <Skeleton class="h-4 w-20" />
                                </TableHead>
                            </TableRow>
                        </template>
                    </TableHeader>
                    <TableBody>
                        <Deferred data="data">
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
                                    </TableRow>
                                </template>
                            </template>

                            <template v-if="resource == 'customers' || resource === 'users'">
                                <TableRow v-for="customer in data" :key="customer.id">
                                    <!-- customer id -->
                                    <!-- <TableCell>#{{ customer.id }}</TableCell> -->

                                    <!-- customer avatar -->
                                    <TableCell>
                                        <p>{{ customer.name }}</p>
                                    </TableCell>

                                    <!-- customer email -->
                                    <TableCell>{{ customer.email }}</TableCell>

                                    <!-- customer phone -->
                                    <TableCell>{{ customer.phone }}</TableCell>

                                    <!-- customer status -->
                                    <TableCell>
                                        {{ customer.status }}
                                    </TableCell>

                                    <!-- date of joining -->
                                    <TableCell>{{ customer.joined_on }}</TableCell>
                                </TableRow>
                            </template>

                            <template v-else-if="resource == 'memorials'">
                                <TableRow v-for="memorial in data" :key="memorial.id">
                                    <!-- memorial id -->
                                    <TableCell>#{{ memorial.id }}</TableCell>

                                    <TableCell>
                                        <p>{{ memorial.title }}</p>
                                    </TableCell>

                                    <!-- memorial price -->
                                    <TableCell>{{ memorial.price }}</TableCell>

                                    <!-- memorial sale_price -->
                                    <TableCell>{{ memorial.sale_price ?? '--' }}</TableCell>

                                    <!-- memorial sales -->
                                    <TableCell>
                                        {{ memorial.sales }}
                                    </TableCell>
                                </TableRow>
                            </template>

                            <template v-else-if="resource == 'orders'">
                                <TableRow v-for="order in data" :key="order.id">
                                    <!-- order id -->
                                    <TableCell>#{{ order.id }}</TableCell>

                                    <!-- order customer -->
                                    <TableCell>
                                        <p>{{ order.customer }}</p>
                                    </TableCell>

                                    <!-- order status -->
                                    <TableCell>{{ order.status }}</TableCell>

                                    <!-- order payment_status -->
                                    <TableCell>{{ order.payment_status }}</TableCell>

                                    <!-- order items -->
                                    <TableCell>
                                        {{ order.items }}
                                    </TableCell>

                                    <!-- order total -->
                                    <TableCell>
                                        {{ order.total }}
                                    </TableCell>

                                    <!-- date -->
                                    <TableCell>{{ order.date }}</TableCell>
                                </TableRow>
                            </template>

                            <template v-if="resource == 'transactions'">
                                <TableRow v-for="transaction in data" :key="transaction.id">
                                    <!-- transaction id -->
                                    <TableCell>#{{ transaction.id }}</TableCell>

                                    <!-- transaction id -->
                                    <TableCell>
                                        <p>{{ transaction.transaction_id }}</p>
                                    </TableCell>

                                    <!-- transaction order_id -->
                                    <TableCell>{{ transaction.order_id }}</TableCell>

                                    <!-- transaction customer -->
                                    <TableCell>{{ transaction.customer }}</TableCell>

                                    <!-- transaction status -->
                                    <TableCell>
                                        {{ transaction.status }}
                                    </TableCell>

                                    <!-- transaction amount -->
                                    <TableCell>
                                        {{ transaction.amount }}
                                    </TableCell>

                                    <!-- date-->
                                    <TableCell>{{ transaction.date }}</TableCell>
                                </TableRow>
                            </template>

                            <TableRow v-if="!data.length">
                                <TableCell colspan="9" class="text-muted-foreground py-4 text-center"> No data found. </TableCell>
                            </TableRow>
                        </Deferred>
                    </TableBody>
                </Table>
            </section>
        </section>
    </AppLayout>
</template>
