<script setup>
import { PaginationList, PaginationListItem } from 'reka-ui';
import { Button } from './ui/button';
import { Pagination, PaginationEllipsis, PaginationFirst, PaginationLast, PaginationNext, PaginationPrevious } from './ui/pagination';

defineProps({
    meta: {
        type: Object,
        required: true,
    },
    onPageChange: {
        type: Function,
        required: true,
    },
});
</script>

<template>
    <div class="flex w-full flex-col items-center gap-y-2 sm:flex-row sm:justify-between">
        <div>
            <p class="text-muted-foreground text-sm">
                Showing
                <span class="font-medium">{{ meta.from }}</span>
                to
                <span class="font-medium">{{ meta.to }}</span>
                of
                <span class="font-medium">{{ meta.total }}</span>
                results
            </p>
        </div>

        <Pagination
            :items-per-page="meta.per_page"
            :total="meta.total"
            :sibling-count="1"
            show-edges
            :default-page="meta.current_page"
            class="sm:flex-1 sm:justify-end"
        >
            <PaginationList v-slot="{ items }" class="flex items-center gap-1">
                <PaginationFirst @click="onPageChange(meta.first_page)" />
                <PaginationPrevious @click="onPageChange(meta.current_page - 1)" class="me-2 text-sm" />

                <template v-for="(item, index) in items">
                    <PaginationListItem v-if="item.type === 'page'" :key="index" :value="item.value" as-child>
                        <Button @click="onPageChange(item.value)" :variant="item.value === meta.current_page ? 'default' : 'outline'" size="sm">
                            {{ item.value }}
                        </Button>
                    </PaginationListItem>
                    <PaginationEllipsis v-else :key="item.type" :index="index" />
                </template>

                <PaginationNext @click="onPageChange(meta.current_page + 1)" class="ms-2 text-sm" />
                <PaginationLast @click="onPageChange(meta.last_page)" />
            </PaginationList>
        </Pagination>
    </div>
</template>
