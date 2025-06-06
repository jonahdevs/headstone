<script setup>
import PageHeaderSection from '@/components/PageHeaderSection.vue';
import { Button } from '@/components/ui/button';
import { NumberField, NumberFieldContent, NumberFieldDecrement, NumberFieldIncrement, NumberFieldInput } from '@/components/ui/number-field';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Deferred, Head, router, useForm, usePage } from '@inertiajs/vue3';
import { LoaderCircle, ShoppingBag } from 'lucide-vue-next';
import { computed, onMounted, watch } from 'vue';
import { toast } from 'vue-sonner';

// Your logic goes here
const props = defineProps({ cart: Object });
const flash = computed(() => usePage().props.flash);
const pageSectionLinks = [
    {
        title: 'Home',
        href: route('home'),
    },
    {
        title: 'Cart',
        href: '',
    },
];

const checkoutForm = useForm();

const handleCheckout = () => {
    checkoutForm.post(route('checkout'), {
        preserveScroll: true,
        onError: () => {
            toast.error('Failed to proceed to checkout. Please try again.');
        },
    });
};

const updateQuantity = (itemId, val) => {
    const updateForm = useForm({
        quantity: val,
    });

    updateForm.put(route('cart.update', itemId), {
        preserveScroll: true,
        onError: () => {
            toast.error('Failed to update cart. Please try again.');
        },
    });
};

const deleteItem = (itemId) => {
    router.delete(route('cart.destroy', itemId), {
        preserveScroll: true,

        onError: () => {
            toast.error('Failed to remove item from cart. Please try again.');
        },
    });
};

watch(
    () => flash.value.message,
    (msg) => {
        if (msg && msg.type === 'success') {
            toast.success(msg.body);
        } else if (msg && msg.type === 'error') {
            toast.error(msg.body);
        }
    },
);

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
    <Head title="Cart" />

    <GuestLayout>
        <PageHeaderSection :title="'Cart'" :links="pageSectionLinks" />

        <Deferred data="cart">
            <template #fallback>
                <div></div>
            </template>

            <section v-if="cart.items.length > 0" class="container mx-auto max-w-7xl px-4 py-10 md:px-8 xl:px-0">
                <template v-if="cart?.items.length > 0">
                    <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-3">
                        <!-- cart Items -->
                        <div class="@container/memorial space-y-6 lg:col-span-2">
                            <h1 class="text-2xl font-bold">Your Memorials</h1>

                            <template v-for="item in cart.items" :key="item.id">
                                <div class="flex flex-wrap gap-4 rounded-xl border p-4">
                                    <img :src="item.memorial.image" alt="memorial image" class="h-24 w-24 rounded-md object-cover" />

                                    <div class="flex-1 space-y-1">
                                        <h2 class="text-base font-semibold">{{ item.memorial.title }}</h2>
                                        <div class="flex items-center gap-2 text-base font-medium">
                                            <h2 class="text-base" :class="{ 'text-muted-foreground line-through': item.memorial.sale_price }">
                                                {{ item.memorial.price }}
                                            </h2>
                                            <h2 class="text-primary text-base" v-if="item.memorial.sale_price">{{ item.memorial.sale_price }}</h2>
                                        </div>
                                        <p class="text-muted-foreground text-sm">
                                            Deceased: <strong>{{ item.first_name + ' ' + item.last_name }}</strong>
                                        </p>
                                        <p class="text-muted-foreground text-sm">{{ item.DOB + ' - ' + item.DOD }}</p>
                                        <p class="text-muted-foreground text-sm">Font: {{ item.font }}</p>
                                        <p class="text-muted-foreground line-clamp-2 text-sm">Epitaph: {{ item.epitaph }}</p>
                                        <p class="text-muted-foreground line-clamp-3 text-sm">Instructions: {{ item.instructions }}</p>

                                        <div v-if="item.image" class="mt-2">
                                            <img :src="item.image" alt="Deceased photo" class="h-16 w-16 rounded-md border object-cover" />
                                        </div>

                                        <button
                                            type="button"
                                            @click="deleteItem(item.id)"
                                            class="mt-2 text-sm text-red-600 hover:underline dark:text-red-400"
                                        >
                                            Remove
                                        </button>
                                    </div>

                                    <div class="flex flex-col items-end justify-between">
                                        <p class="text-lg font-semibold">{{ item.total }}</p>

                                        <NumberField
                                            id="quantity"
                                            :min="1"
                                            class="max-w-28"
                                            :default-value="item.quantity"
                                            @update:modelValue="(val) => updateQuantity(item.id, val)"
                                        >
                                            <NumberFieldContent>
                                                <NumberFieldDecrement />
                                                <NumberFieldInput />
                                                <NumberFieldIncrement />
                                            </NumberFieldContent>
                                        </NumberField>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="space-y-4 rounded-xl border p-6">
                            <h2 class="text-xl font-bold">Order Summary</h2>

                            <div class="flex justify-between text-sm">
                                <span>Subtotal</span>
                                <span>{{ cart.subtotal }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span>Discount</span>
                                <span>{{ cart.discount }}</span>
                            </div>

                            <div class="flex justify-between text-sm">
                                <span>Delivery</span>
                                <span>Free</span>
                            </div>

                            <div class="flex justify-between text-base font-semibold">
                                <span>Total</span>
                                <span>{{ cart.total }}</span>
                            </div>

                            <Button @click="handleCheckout" class="w-full" :disabled="checkoutForm.processing">
                                <LoaderCircle v-if="checkoutForm.processing" class="h-4 w-4 animate-spin" />
                                Proceed to Checkout
                            </Button>
                        </div>
                    </div>
                </template>
            </section>

            <section v-else class="grid min-h-[50svh] place-items-center">
                <div class="flex flex-col items-center gap-2">
                    <ShoppingBag class="size-16 stroke-1 md:size-20 lg:size-24 xl:size-28" />
                    <p class="text-lg">Your Cart is empty!</p>
                    <p class="text-muted-foreground">You can add some items here before you check out</p>
                    <Button @click="router.visit(route('memorials'))" variant="outline">Go Shopping</Button>
                </div>
            </section>
        </Deferred>
    </GuestLayout>
</template>
