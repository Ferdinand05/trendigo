<script setup lang="ts">
import Footer from '@/components/Footer.vue';
import Navbar from '@/components/Navbar.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import Carousel from '@/components/ui/carousel/Carousel.vue';
import CarouselContent from '@/components/ui/carousel/CarouselContent.vue';
import CarouselItem from '@/components/ui/carousel/CarouselItem.vue';
import CarouselNext from '@/components/ui/carousel/CarouselNext.vue';
import CarouselPrevious from '@/components/ui/carousel/CarouselPrevious.vue';
import Input from '@/components/ui/input/Input.vue';
import Pagination from '@/components/ui/pagination/Pagination.vue';
import PaginationContent from '@/components/ui/pagination/PaginationContent.vue';
import PaginationItem from '@/components/ui/pagination/PaginationItem.vue';
import PaginationNext from '@/components/ui/pagination/PaginationNext.vue';
import PaginationPrevious from '@/components/ui/pagination/PaginationPrevious.vue';
import Separator from '@/components/ui/separator/Separator.vue';
import { Categories } from '@/types/categories';
import { PaginationLinks, PaginationMeta } from '@/types/pagination';
import { ProductInterface } from '@/types/products';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { Eye, Plus, Rocket, Search, XCircle } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast, Toaster } from 'vue-sonner';

const props = defineProps<{
    products: {
        data: ProductInterface[];
        meta: PaginationMeta;
        links: PaginationLinks;
    };
    category: Categories;
    newest_products: {
        data: ProductInterface[];
    };
}>();

const searchKeyword = ref('');
watchDebounced(
    searchKeyword,
    (val) => {
        router.get(
            route('product.in.category', props.category.slug),
            {
                s: val,
            },
            {
                preserveScroll: true,
                preserveState: true,
            },
        );
    },
    {
        debounce: 600,
        maxWait: 1200,
    },
);

const productCart = useForm<{
    id_product: number | null;
}>({
    id_product: null,
});

function addToCart(id: number | null) {
    productCart.id_product = id;

    productCart.post(route('add.to.cart'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (result: any) => {
            if (result.props.flash.message == 'Stok produk tidak mencukupi') {
                toast('Ooops!', {
                    description: `${result.props.flash.message}`,
                    icon: XCircle,
                });
            } else {
                toast('Yeayy!', {
                    description: `${result.props.flash.message}`,
                    icon: Rocket,
                });
            }

            router.reload({ only: ['cartProduct'] });
        },
    });
}

function formatRupiah(value: number) {
    return new Intl.NumberFormat('id-ID').format(value);
}
</script>

<template>
    <Head title="Home"></Head>
    <Navbar />
    <Toaster />
    <div class="w-full">
        <img src="/img/pattern/waves1.svg" alt="" class="absolute w-full" />
    </div>
    <div class="mx-auto max-w-7xl px-6 py-14 pt-24 font-poppins dark:text-gray-200">
        <section class="pt-10">
            <div class="py-2 md:py-5">
                <div class="text-[12px] tracking-wide text-blue-800 md:text-sm">Kategori</div>
                <h1 class="text-2xl font-bold tracking-wide md:text-3xl">{{ props.category.name }}</h1>
                <p class="font-light md:text-lg">{{ props.category.description }}</p>
            </div>

            <div>
                <div class="relative w-full max-w-sm items-center md:max-w-md">
                    <Input v-model="searchKeyword" id="search" type="text" placeholder="Cari..." class="pl-10" />
                    <span class="absolute inset-y-0 start-0 flex items-center justify-center px-2">
                        <Search class="size-5 text-blue-600" />
                    </span>
                </div>
            </div>

            <!-- list produk -->
            <div class="mt-5 grid justify-center gap-4 sm:justify-around md:mt-7 md:grid-cols-4">
                <div
                    v-for="product in props.products.data"
                    :key="product.id"
                    class="overflow-hidden rounded-md border p-0 shadow-sm transition-all duration-150 hover:shadow-lg dark:border-gray-700"
                >
                    <Carousel class="rounded-none p-0">
                        <CarouselContent class="rounded-none p-0">
                            <CarouselItem class="rounded-none p-0" v-for="image in product.product_images" :key="image.id">
                                <div class="p-0">
                                    <Card class="rounded-none border-none py-2">
                                        <CardContent class="flex items-center justify-center rounded-none p-0">
                                            <img class="h-48 object-cover md:h-40" :src="'/storage/' + image.image_path" alt="" />
                                        </CardContent>
                                    </Card>
                                </div>
                            </CarouselItem>
                        </CarouselContent>
                        <CarouselPrevious class="top-1/2 left-1" size="sm" />
                        <CarouselNext class="top-1/2 right-1" size="sm" />
                    </Carousel>
                    <div class="px-4 py-2">
                        <div class="space-y-2">
                            <Badge variant="secondary" class="font-light">
                                {{ product.category }}
                            </Badge>
                            <div>
                                <Link :href="route('detail.product', product.slug)" class="transition-all duration-150 hover:text-blue-500">
                                    {{ product.limit_name }}
                                </Link>
                            </div>
                            <div class="font-bold md:text-lg">Rp {{ formatRupiah(product.price) }}</div>
                        </div>
                        <div class="flex items-center justify-end gap-x-3.5 p-2">
                            <div class="w-full">
                                <Link :href="route('detail.product', product.slug)">
                                    <Button class="w-full hover:cursor-pointer" variant="outline"><Eye /> Lihat</Button>
                                </Link>
                            </div>
                            <div class="w-full">
                                <Button
                                    @click.prevent="addToCart(product.id)"
                                    type="button"
                                    class="w-full cursor-pointer dark:bg-blue-800 dark:text-gray-200 dark:hover:bg-blue-700"
                                    size="sm"
                                >
                                    <Plus /> Keranjang
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- pagination  -->
            <div class="mt-7">
                <Pagination
                    :v-slot="props.products.meta.current_page"
                    :items-per-page="props.products.meta.per_page"
                    :total="props.products.meta.total"
                    :default-page="props.products.meta.current_page"
                >
                    <PaginationContent :v-slot="props.products.meta.links">
                        <Button variant="link" :disabled="props.products.links.prev == null">
                            <Link :href="props.products.links.prev ?? ''" preserve-scroll preserve-state>
                                <PaginationPrevious />
                            </Link>
                        </Button>

                        <template v-for="(item, index) in props.products.meta.links" :key="index">
                            <PaginationItem v-if="!isNaN(Number(item.label))" :value="props.products.meta.current_page" :is-active="item.active">
                                <Link :href="item.url ?? ''" preserve-scroll preserve-state>
                                    {{ item.label }}
                                </Link>
                            </PaginationItem>
                        </template>

                        <Button variant="link" :disabled="props.products.links.next == null">
                            <Link :href="props.products.links.next ?? ''" preserve-scroll preserve-state>
                                <PaginationNext />
                            </Link>
                        </Button>
                    </PaginationContent>
                </Pagination>
            </div>
        </section>
    </div>

    <Separator />

    <!-- newest produk -->
    <section class="mx-auto max-w-7xl px-6 py-10 font-poppins sm:px-6 lg:px-6 dark:text-gray-200">
        <div class="dark:text-gray-200">
            <h1 class="text-xl font-semibold md:text-2xl">Produk <span class="text-blue-700"> Terbaru </span></h1>
            <p class="text-sm font-light text-gray-700">Produk yang baru saja rilis</p>
        </div>
        <div class="mt-5 grid justify-around gap-4 md:mt-7 md:grid-cols-4">
            <div
                v-for="product in props.newest_products.data"
                :key="product.id"
                class="overflow-hidden rounded-md border p-0 shadow-sm transition-all duration-150 hover:shadow-lg dark:border-gray-700"
            >
                <Carousel class="rounded-none p-0">
                    <CarouselContent class="rounded-none p-0">
                        <CarouselItem class="rounded-none p-0" v-for="image in product.product_images" :key="image.id">
                            <div class="p-0">
                                <Card class="rounded-none border-none py-2">
                                    <CardContent class="flex items-center justify-center rounded-none p-0">
                                        <img class="h-48 object-cover md:h-40" :src="'/storage/' + image.image_path" alt="" />
                                    </CardContent>
                                </Card>
                            </div>
                        </CarouselItem>
                    </CarouselContent>
                    <CarouselPrevious class="top-1/2 left-1" size="sm" />
                    <CarouselNext class="top-1/2 right-1" size="sm" />
                </Carousel>
                <div class="px-4 py-2">
                    <div class="space-y-2">
                        <Badge variant="secondary" class="font-light">
                            {{ product.category }}
                        </Badge>
                        <div>
                            <Link href="" class="transition-all duration-150 hover:text-blue-500">
                                {{ product.limit_name }}
                            </Link>
                        </div>
                        <div class="font-bold md:text-lg">Rp {{ formatRupiah(product.price) }}</div>
                    </div>
                    <!-- button -->
                    <div class="flex items-center justify-end gap-x-3.5 p-2">
                        <div class="w-full">
                            <Link :href="route('detail.product', product.slug)">
                                <Button class="w-full hover:cursor-pointer" variant="outline"><Eye /> Lihat</Button>
                            </Link>
                        </div>
                        <div class="w-full">
                            <Button
                                @click.prevent="addToCart(product.id)"
                                type="button"
                                class="w-full cursor-pointer dark:bg-blue-800 dark:text-gray-200 dark:hover:bg-blue-700"
                                size="sm"
                            >
                                <Plus /> Keranjang
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-14">
        <Footer></Footer>
    </section>
</template>
