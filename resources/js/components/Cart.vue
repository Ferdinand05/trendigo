<script setup lang="ts">
import { AppPageProps } from '@/types';
import { Link, router, usePage } from '@inertiajs/vue3';
import { CircleAlert, Minus, Plus, ShoppingCart, Trash } from 'lucide-vue-next';
import { computed } from 'vue';

import 'vue-sonner/style.css';
import Badge from './ui/badge/Badge.vue';
import Button from './ui/button/Button.vue';
import Popover from './ui/popover/Popover.vue';
import PopoverContent from './ui/popover/PopoverContent.vue';
import PopoverTrigger from './ui/popover/PopoverTrigger.vue';
const page = usePage<AppPageProps>();
// Format ke rupiah
function formatRupiah(value: number) {
    return new Intl.NumberFormat('id-ID').format(value);
}

const cartTotal = computed(() => {
    return page.props.cartProduct.data.reduce((sum: number, item) => sum + item.subtotal, 0);
});

function reduceQuantity(id: number): void {
    router.put(
        route('reduce.qty'),
        {
            id_cart: id,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                router.reload({ only: ['cartProduct'] });
            },
        },
    );
}

function addQuantity(id: number): void {
    router.put(
        route('add.qty'),
        {
            id_cart: id,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                router.reload({ only: ['cartProduct'] });
            },
        },
    );
}

function deleteCartItem(id: number) {
    router.delete(route('delete.cart.item'), {
        data: {
            id_cart: id,
        },
        preserveScroll: true,
        onSuccess: () => {
            router.reload({ only: ['cartProduct'] });
        },
    });
}
</script>

<template>
    <Popover class="dark:text-gray-400">
        <PopoverTrigger class="cursor-pointer">
            <div class="relative">
                <ShoppingCart class="text-gray-200 transition-transform hover:scale-110" />
                <Badge variant="destructive" size="sm" class="absolute -top-3 -right-5 bg-red-500 text-white">
                    {{ page.props.cartProduct?.data?.length ?? 0 }}
                </Badge>
            </div>
        </PopoverTrigger>

        <PopoverContent
            class="mt-5 mr-5 w-[450px] rounded-xl border border-gray-200 bg-white shadow-lg md:w-[500px] dark:border-gray-700 dark:bg-gray-900"
        >
            <!-- Header -->
            <div class="mb-2 w-full rounded-md bg-gradient-to-r from-blue-600 to-indigo-600 py-2 text-center text-[15px] font-semibold text-white">
                Keranjang
            </div>

            <!-- Flash Message -->
            <div class="mb-2 flex justify-center gap-x-1.5 text-sm text-red-600" v-show="page.props.flash.message">
                <CircleAlert class="size-4.5" />
                {{ page.props.flash.message }}
            </div>

            <!-- Items -->
            <div class="max-h-[380px] w-full overflow-y-scroll">
                <ul class="space-y-3 px-1">
                    <li
                        v-for="(cart, index) in page.props.cartProduct.data"
                        :key="index"
                        class="rounded-lg border border-gray-200 bg-gray-50 p-3 shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
                    >
                        <div class="line-clamp-2">
                            <span class="font-medium text-gray-800 dark:text-gray-200">
                                {{ cart.product.limit_name }}
                            </span>
                            <span class="pl-2 text-gray-500">@{{ formatRupiah(cart.product.price) }}</span>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Jumlah : {{ cart.quantity }}</div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-3 flex gap-x-2">
                            <Button
                                @click.prevent="addQuantity(cart.id)"
                                size="icon"
                                class="size-7 rounded-full bg-blue-500 text-white hover:cursor-pointer hover:bg-blue-600"
                            >
                                <Plus />
                            </Button>
                            <Button
                                @click.prevent="reduceQuantity(cart.id)"
                                size="icon"
                                variant="outline"
                                class="size-7 rounded-full border-blue-400 text-blue-500 hover:cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900"
                            >
                                <Minus />
                            </Button>
                            <Button
                                @click.prevent="deleteCartItem(cart.id)"
                                size="icon"
                                variant="destructive"
                                class="size-7 rounded-full bg-red-500 text-white hover:cursor-pointer hover:bg-red-600"
                            >
                                <Trash />
                            </Button>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Footer -->
            <div class="mt-3 flex justify-between font-semibold text-gray-800 dark:text-gray-200">
                <span>Total</span>
                <span>{{ formatRupiah(cartTotal) }}</span>
            </div>
            <div class="mt-3 w-full" v-show="page.props.cartProduct?.data?.length >= 1">
                <Link :href="route('checkout.index')" prefetch="hover">
                    <Button
                        size="sm"
                        class="w-full cursor-pointer rounded-lg bg-gradient-to-r from-indigo-600 to-blue-600 font-medium text-white shadow hover:opacity-90"
                    >
                        Checkout
                    </Button>
                </Link>
            </div>
        </PopoverContent>
    </Popover>
</template>
