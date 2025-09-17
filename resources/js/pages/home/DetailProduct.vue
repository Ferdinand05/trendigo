<script setup lang="ts">
import Footer from '@/components/Footer.vue';
import Navbar from '@/components/Navbar.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import Button from '@/components/ui/button/Button.vue';
import { Card, CardContent } from '@/components/ui/card';
import type { CarouselApi } from '@/components/ui/carousel';
import { Carousel, CarouselContent, CarouselItem, CarouselNext, CarouselPrevious } from '@/components/ui/carousel';
import Separator from '@/components/ui/separator/Separator.vue';
import { Categories } from '@/types/categories';
import { ProductInterface } from '@/types/products';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { watchOnce } from '@vueuse/core';
import { Plus, Rocket } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast, Toaster } from 'vue-sonner';
import 'vue-sonner/style.css';

const props = defineProps<{
    product: {
        data: ProductInterface;
    };
    category: Categories;
}>();

const emblaMainApi = ref<CarouselApi>();
const emblaThumbnailApi = ref<CarouselApi>();
const selectedIndex = ref(0);

function onSelect() {
    if (!emblaMainApi.value || !emblaThumbnailApi.value) return;
    selectedIndex.value = emblaMainApi.value.selectedScrollSnap();
    emblaThumbnailApi.value.scrollTo(emblaMainApi.value.selectedScrollSnap());
}

function onThumbClick(index: number) {
    if (!emblaMainApi.value || !emblaThumbnailApi.value) return;
    emblaMainApi.value.scrollTo(index);
}

watchOnce(emblaMainApi, (emblaMainApi) => {
    if (!emblaMainApi) return;

    onSelect();
    emblaMainApi.on('select', onSelect);
    emblaMainApi.on('reInit', onSelect);
});

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
            toast('Yeayy!', {
                description: `${result.props.flash.message}`,
                icon: Rocket,
            });
        },
    });
}

function formatRupiah(value: number) {
    return new Intl.NumberFormat('id-ID').format(value);
}

const page = usePage();
const location = page.props.ziggy.location;
</script>

<template>
    <head title="Detail Produk"></head>
    <Navbar></Navbar>
    <Toaster />
    <div class="w-full">
        <img src="/img/pattern/waves1.svg" alt="" class="absolute w-full" />
    </div>

    <section class="mx-auto max-w-7xl overflow-hidden px-6 py-14 pt-40 font-poppins dark:text-gray-200">
        <!-- share/bagikan -->
        <div class="flex max-w-7xl flex-col items-end justify-end">
            <p class="text-sm text-gray-600">Bagikan</p>
            <ul class="share-buttons flex gap-x-1" data-source="simplesharingbuttons.com">
                <li>
                    <a
                        :href="'https://www.facebook.com/sharer/sharer.php?u=' + location + '=&quote=Trendigo%20Produk'"
                        title="Share  on Facebook"
                        target="_blank"
                    >
                        <img src="https://img.icons8.com/?size=100&id=13912&format=png&color=000000" width="38" height="38" alt="" />
                    </a>
                </li>
                <li>
                    <a :href="'https://x.com/intent/tweet?source=' + location + '&text=Trendigo%20Produk:' + location" target="_blank" title="Tweet"
                        ><img alt="Tweet" src="https://img.icons8.com/?size=100&id=yoQabS8l0qpr&format=png&color=000000" width="38" height="38"
                    /></a>
                </li>
                <li>
                    <a :href="'https://pinterest.com/pin/create/button/?url=' + location + '&description='" target="_blank" title="Pin it"
                        ><img alt="Pin it" src="https://img.icons8.com/?size=100&id=63676&format=png&color=000000" width="38" height="38"
                    /></a>
                </li>
            </ul>
        </div>
        <!-- end share/bagikan -->

        <div class="flex flex-col justify-around pt-10 md:flex-row md:gap-x-14">
            <!-- product image -->
            <div class="max-w-[550px]">
                <Carousel class="relative rounded-md border py-5 shadow" @init-api="(val) => (emblaMainApi = val)">
                    <CarouselContent class="">
                        <CarouselItem class="" v-for="(image, index) in props.product.data.product_images" :key="index">
                            <div class="">
                                <Card class="border-none py-2">
                                    <CardContent class="flex items-center justify-center">
                                        <img class="h-64 object-cover object-center" :src="'/storage/' + image.image_path" alt="" />
                                    </CardContent>
                                </Card>
                            </div>
                        </CarouselItem>
                    </CarouselContent>
                    <CarouselPrevious />
                    <CarouselNext />
                </Carousel>

                <Carousel class="relative mt-2 w-full max-w-xs" @init-api="(val) => (emblaThumbnailApi = val)">
                    <CarouselContent class="ml-0 flex gap-2">
                        <CarouselItem
                            v-for="(image, index) in props.product.data.product_images"
                            :key="index"
                            class="basis-1/4 cursor-pointer pl-0"
                            @click="onThumbClick(index)"
                        >
                            <div :class="index === selectedIndex ? '' : 'opacity-50'">
                                <Card class="flex h-24 items-center justify-center p-4 md:h-28">
                                    <CardContent class="p-0">
                                        <img class="h-full w-full rounded-md object-cover" :src="'/storage/' + image.image_path" alt="" />
                                    </CardContent>
                                </Card>
                            </div>
                        </CarouselItem>
                    </CarouselContent>
                </Carousel>
            </div>

            <!-- information -->
            <div class="mt-7 max-w-[620px] px-4 md:mt-0">
                <Link :href="route('product.in.category', props.category.slug)">
                    <Badge variant="secondary" class="mb-1">{{ props.category.name }}</Badge>
                </Link>
                <div class="">
                    <h1 class="text-xl font-semibold tracking-wide md:text-2xl">
                        {{ props.product.data.name }}
                    </h1>
                    <div class="font-light md:text-lg">
                        {{ props.product.data.description }}
                    </div>
                </div>
                <div class="mt-3">
                    <h1 class="font-medium md:text-lg">Informasi</h1>
                    <Separator />
                    <div class="mt-2 space-y-2">
                        <div>
                            <div class="font-light">Harga</div>
                            <div class="text-xl font-semibold tracking-wide md:text-2xl">Rp {{ formatRupiah(props.product.data.price) }}</div>
                        </div>
                        <div>
                            <div class="font-light">Tersisa</div>
                            <div class="text-lg font-medium md:text-xl">{{ props.product.data.stock }} Unit</div>
                        </div>
                        <div>
                            <div class="font-light">Berat</div>
                            <div class=": text-lg font-medium md:text-xl">{{ props.product.data.weight }} gram</div>
                        </div>
                    </div>
                    <Separator class="mt-4" />
                    <div class="mt-2 flex w-full">
                        <Button @click.prevent="addToCart(props.product.data.id)" class="w-full hover:cursor-pointer"
                            ><Plus /> Tambah ke Keranjang</Button
                        >
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="mt-20">
        <Footer></Footer>
    </div>
</template>
