<script lang="ts" setup>
import Footer from '@/components/Footer.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import Navbar from '@/components/Navbar.vue';
import ProfileUserLayout from '@/components/ProfileUserLayout.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardFooter from '@/components/ui/card/CardFooter.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import DialogClose from '@/components/ui/dialog/DialogClose.vue';
import { Order } from '@/types/orders';
import { Head } from '@inertiajs/vue3';
import { Eye, Printer } from 'lucide-vue-next';
import { ref } from 'vue';

function formatRupiah(value: number) {
    return new Intl.NumberFormat('id-ID').format(value);
}

const props = defineProps<{
    orders: {
        data: Order[];
    };
}>();

const openModalDetail = ref<boolean>(false);
const selectedOrder = ref<Order | null>(null);

const fillModal = (order: Order) => {
    // isi data
    selectedOrder.value = order;

    // open modal
    openModalDetail.value = true;
};
</script>

<template>
    <Head title="Profile User | Pesanan" />
    <Navbar />
    <div class="w-full">
        <img src="/img/pattern/waves1.svg" alt="" class="absolute w-full" />
    </div>
    <section class="mx-auto max-w-7xl px-8 pt-20 md:pt-32">
        <ProfileUserLayout>
            <div>
                <HeadingSmall title="Pesanan" description="Laporan pesanan anda"></HeadingSmall>
            </div>
            <div class="grid w-full grid-cols-1 gap-3 md:grid-cols-2">
                <div v-for="(order, index) in props.orders.data" :key="index">
                    <Card class="gap-3 dark:text-gray-300">
                        <CardHeader>
                            <div class="rounded-xl bg-gradient-to-r from-blue-300 via-blue-400 to-blue-800 p-2.5 dark:bg-blue-800">
                                <small>Kode Order</small>
                                <CardTitle>#{{ order.order_code }}</CardTitle>
                                <CardDescription class="mt-2">
                                    <Badge
                                        :class="{
                                            'bg-blue-500 uppercase': order.status == 'pending',
                                            'bg-green-500 uppercase': order.status == 'paid',
                                        }"
                                        >{{ order.status }}</Badge
                                    >
                                </CardDescription>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-2">
                            <div>
                                <small>Total</small>
                                <div class="text-lg font-semibold md:text-xl">Rp {{ formatRupiah(order.total) }}</div>
                            </div>
                            <div>
                                <small>Jumlah Item</small>
                                <div class="text-lg font-semibold md:text-xl">{{ order.order_items.length }}</div>
                            </div>
                        </CardContent>
                        <CardFooter class="flex items-center justify-between">
                            <div>
                                <Button @click.prevent="fillModal(order)" variant="secondary" size="sm" class="hover:cursor-pointer"
                                    ><Eye /> Detail</Button
                                >
                            </div>
                            <div>
                                <Button size="sm" v-if="order.status == 'pending'" class="hover:cursor-pointer">Selesaikan Pembayaran</Button>
                                <Button size="sm" v-else-if="order.status == 'paid'" class="hover:cursor-pointer"><Printer /></Button>
                            </div>
                        </CardFooter>
                    </Card>
                </div>
            </div>
        </ProfileUserLayout>
    </section>

    <Dialog v-model:open="openModalDetail">
        <DialogContent class="sm:max-w-[650px]">
            <DialogHeader>
                <DialogTitle>Detail Order</DialogTitle>
            </DialogHeader>
            <div class="grid gap-4 py-4">
                <div>
                    <div class="font-semibold"># {{ selectedOrder?.order_code }}</div>
                    <div class="mt-3">
                        <div>Nama : {{ selectedOrder?.user.name }}</div>
                        <div>Pembayaran : {{ selectedOrder?.midtrans_payment_type }}</div>
                    </div>
                </div>
                <div>
                    <table class="w-full border-collapse overflow-hidden rounded-lg text-sm shadow-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium">Produk</th>
                                <th class="px-4 py-2 font-medium">Harga</th>
                                <th class="px-4 py-2 text-center font-medium">Jumlah</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                            <tr v-for="(item, index) in selectedOrder?.order_items" :key="index" class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-left">{{ item.product }}</td>
                                <td class="px-4 py-2">{{ formatRupiah(item.price) }}</td>
                                <td class="px-4 py-2 text-center">{{ item.quantity }}</td>
                            </tr>

                            <tr class="bg-gray-50">
                                <td class="px-4 py-2 text-left font-light">(Pengiriman) {{ selectedOrder?.shipping_service }}</td>
                                <td class="px-4 py-2">{{ formatRupiah(selectedOrder?.shipping_cost ?? 0) }}</td>
                                <td class="px-4 py-2 text-center">1</td>
                            </tr>
                        </tbody>

                        <tfoot class="bg-gray-100 font-semibold text-gray-800">
                            <tr>
                                <td colspan="2" class="px-4 py-2 text-right">Total</td>
                                <td class="px-4 py-2 text-center">
                                    {{ formatRupiah(selectedOrder?.total ?? 0) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <DialogFooter class="flex items-center justify-between">
                <div class="w-full text-sm text-gray-600">{{ selectedOrder?.created_at }}</div>
                <DialogClose class="">
                    <Button type="submit"> Tutup </Button>
                </DialogClose>
            </DialogFooter>
        </DialogContent>
    </Dialog>

    <div class="mt-20">
        <Footer />
    </div>
</template>
