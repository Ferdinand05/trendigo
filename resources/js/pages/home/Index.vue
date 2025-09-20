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
import Select from '@/components/ui/select/Select.vue';
import SelectContent from '@/components/ui/select/SelectContent.vue';
import SelectGroup from '@/components/ui/select/SelectGroup.vue';
import SelectItem from '@/components/ui/select/SelectItem.vue';
import SelectLabel from '@/components/ui/select/SelectLabel.vue';
import SelectTrigger from '@/components/ui/select/SelectTrigger.vue';
import SelectValue from '@/components/ui/select/SelectValue.vue';
import { Categories } from '@/types/categories';
import { PaginationLinks, PaginationMeta } from '@/types/pagination';
import { ProductInterface } from '@/types/products';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { Eye, FilterIcon, Plus, Rocket, Search, XCircle } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { toast, Toaster } from 'vue-sonner';
import 'vue-sonner/style.css';
// import required modules

const props = defineProps<{
    products: {
        data: ProductInterface[];
        meta: PaginationMeta;
        links: PaginationLinks;
    };
    categories: {
        data: Categories[];
    };
    newest_products: {
        data: ProductInterface[];
    };
}>();

const selectedCategory = ref<string | null>(null);
function filterCategory(val: string | null) {
    router.get(
        route('home'),
        {
            k: val,
        },
        {
            preserveScroll: true,
            preserveState: true,
        },
    );
}

watch(selectedCategory, (newVal) => {
    filterCategory(newVal);
});

const searchKeyword = ref('');
watchDebounced(
    searchKeyword,
    (val) => {
        router.get(
            route('home'),
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

const mode = ref(localStorage.getItem('appearance'));
</script>

<template>
    <Head title="Home"></Head>
    <Navbar />
    <Toaster />
    <div class="w-full">
        <img src="/img/pattern/waves1.svg" alt="" class="absolute w-full" />
    </div>

    <section id="hero" class="mx-auto max-w-7xl px-6 pt-36 font-poppins sm:px-6 md:pt-20 lg:px-6">
        <div class="flex flex-col-reverse items-center justify-center space-y-10 gap-x-10 md:flex-row md:py-28">
            <!-- text hero -->
            <div class="space-y-2 text-center sm:text-left">
                <h1 class="text-3xl font-bold tracking-wider text-blue-800 md:text-5xl md:tracking-wider dark:text-blue-400">
                    <span class="dark:text-blue-600">Tren</span>digo
                </h1>

                <h1 class="text-2xl font-bold md:text-4xl dark:text-gray-200">
                    Temukan <span class="text-blue-800 dark:text-blue-600">Tren</span>, Ikuti Gaya
                </h1>
                <p class="mt-2 font-light md:text-lg dark:text-gray-200">
                    Trendigo menghadirkan koleksi fashion, elektronik & aksesoris terbaru dengan harga terbaik. Belanja mudah, cepat, dan selalu
                    up-to-date dengan tren terkini.
                </p>

                <Link href="#product" class="hover:cursor-pointer">
                    <Button class="mt-5 bg-blue-800 text-lg hover:cursor-pointer hover:bg-blue-900 dark:text-gray-200">Belanja Sekarang</Button>
                </Link>
            </div>
            <div class="px-8 md:w-1/2 md:px-0">
                <img :src="mode == 'dark' ? '/img/hero/dark.gif' : '/img/hero/light.gif'" class="" />
            </div>
        </div>
    </section>

    <!-- product section -->
    <section id="product" class="mx-auto max-w-7xl px-6 py-10 font-poppins sm:px-6 lg:px-6">
        <div class="mb-3">
            <h1 class="text-xl font-semibold tracking-wide md:text-2xl dark:text-gray-200"><span class="text-blue-700">Produk</span> Kami</h1>
        </div>

        <!-- select  -->
        <div class="flex flex-col justify-start space-y-2 md:flex-row md:justify-between">
            <div class="relative w-full max-w-sm items-center md:max-w-md">
                <Input v-model="searchKeyword" id="search" type="text" placeholder="Cari..." class="pl-10" />
                <span class="absolute inset-y-0 start-0 flex items-center justify-center px-2">
                    <Search class="size-5 text-blue-600" />
                </span>
            </div>
            <Select v-model="selectedCategory">
                <SelectTrigger class="w-[225px]">
                    <SelectValue> <FilterIcon class="size-5 text-blue-600" /> Kategori </SelectValue>
                </SelectTrigger>
                <SelectContent>
                    <SelectGroup>
                        <SelectLabel>Pilih Kategori</SelectLabel>
                        <SelectItem value="semua">Semua</SelectItem>
                        <SelectItem v-for="(category, index) in props.categories.data" :key="index" :value="category.slug">
                            {{ category.name }}
                        </SelectItem>
                    </SelectGroup>
                </SelectContent>
            </Select>
        </div>
        <!-- list produk -->
        <div class="mt-5 grid justify-around gap-4 md:mt-7 md:grid-cols-4">
            <div
                v-for="product in props.products.data"
                :key="product.id"
                class="overflow-hidden rounded-md border p-0 shadow-sm transition-all duration-150 hover:shadow-lg dark:border-gray-700 dark:text-gray-200"
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
                            <Link
                                :href="route('detail.product', product.slug)"
                                prefetch="hover"
                                class="transition-all duration-150 hover:text-blue-500"
                            >
                                {{ product.limit_name }}
                            </Link>
                        </div>
                        <div class="font-bold md:text-lg">Rp {{ formatRupiah(product.price) }}</div>
                    </div>
                    <div class="flex items-center justify-around gap-x-3.5 p-2">
                        <div class="w-full">
                            <Link :href="route('detail.product', product.slug)" prefetch="hover">
                                <Button class="w-full hover:cursor-pointer" variant="outline"><Eye /> Lihat</Button>
                            </Link>
                        </div>
                        <div class="w-full">
                            <Button
                                @click.prevent="addToCart(product.id)"
                                type="button"
                                class="w-full cursor-pointer dark:bg-blue-700 dark:text-gray-200"
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
                class="dark:text-gray-200"
                :v-slot="props.products.meta.current_page"
                :items-per-page="props.products.meta.per_page"
                :total="props.products.meta.total"
                :default-page="props.products.meta.current_page"
            >
                <PaginationContent :v-slot="props.products.meta.links">
                    <Button variant="link" :disabled="props.products.links.prev == null">
                        <Link :href="props.products.links.prev ?? ''" preserve-scroll preserve-state prefetch="click">
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

    <!-- newest produk -->

    <section class="mx-auto max-w-7xl px-6 py-10 font-poppins sm:px-6 lg:px-6 dark:text-gray-200">
        <div>
            <h1 class="text-xl font-semibold md:text-2xl">Produk <span class="text-blue-700"> Terbaru </span></h1>
            <p class="text-sm font-light text-gray-700 dark:text-gray-500">Produk yang baru saja rilis</p>
        </div>
        <div class="mt-5 grid justify-around gap-4 md:mt-7 md:grid-cols-4">
            <div
                v-for="product in props.newest_products.data"
                :key="product.id"
                class="overflow-hidden rounded-md border p-0 shadow-sm transition-all duration-150 hover:shadow-lg"
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
                        <div class="font-bold md:text-lg">Rp. {{ formatRupiah(product.price) }}</div>
                    </div>
                    <!-- button -->
                    <div class="flex items-center justify-end gap-x-3.5 p-2">
                        <div class="w-full">
                            <Link :href="route('detail.product', product.slug)" prefetch="hover">
                                <Button class="w-full hover:cursor-pointer" variant="outline"><Eye /> Lihat</Button>
                            </Link>
                        </div>
                        <div class="w-full">
                            <Button
                                @click.prevent="addToCart(product.id)"
                                type="button"
                                class="w-full cursor-pointer dark:bg-blue-700 dark:text-gray-200"
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

    <!-- cta  -->
    <section class="mx-auto max-w-7xl rounded-full px-8 py-12 pt-20 font-poppins shadow-md sm:px-6 lg:px-6">
        <div class="flex flex-col items-center justify-around space-y-10 px-2 md:flex-row md:px-0">
            <!-- text hero -->
            <div class="text-center sm:text-left dark:text-blue-700">
                <h1 class="text-4xl font-bold tracking-wider">Saatnya Upgrade <span class="text-blue-700 dark:text-gray-200">Gaya</span>mu!</h1>
            </div>
            <div class="">
                <img class="h-80 w-full" :src="mode == 'dark' ? '/img/hero/cta-dark.gif' : '/img/hero/cta.gif'" />
            </div>
        </div>
    </section>

    <section class="mt-20">
        <Footer />
    </section>
</template>
