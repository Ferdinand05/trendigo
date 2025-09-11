<script lang="ts" setup>
import Badge from '@/components/ui/badge/Badge.vue';
import Button from '@/components/ui/button/Button.vue';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { DropdownMenu, DropdownMenuCheckboxItem, DropdownMenuContent } from '@/components/ui/dropdown-menu';
import DropdownMenuSeparator from '@/components/ui/dropdown-menu/DropdownMenuSeparator.vue';
import DropdownMenuTrigger from '@/components/ui/dropdown-menu/DropdownMenuTrigger.vue';
import Input from '@/components/ui/input/Input.vue';
import Select from '@/components/ui/select/Select.vue';
import SelectContent from '@/components/ui/select/SelectContent.vue';
import SelectGroup from '@/components/ui/select/SelectGroup.vue';
import SelectItem from '@/components/ui/select/SelectItem.vue';
import SelectLabel from '@/components/ui/select/SelectLabel.vue';
import SelectTrigger from '@/components/ui/select/SelectTrigger.vue';
import SelectValue from '@/components/ui/select/SelectValue.vue';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Order } from '@/types/orders';
import { Head } from '@inertiajs/vue3';
import { Check, EllipsisVertical, FilterIcon, MapPin, Printer, PrinterIcon, Search, ShoppingCart, Trash } from 'lucide-vue-next';
import { ref } from 'vue';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Orders',
        href: '/dashboard/orders',
    },
];

function formatRupiah(value: number) {
    return new Intl.NumberFormat('id-ID').format(value);
}

const props = defineProps<{
    orders: {
        data: Order[];
    };
}>();

const openModalDetail = ref<boolean>(false);
const openModalDetailShipping = ref<boolean>(false);

// simpan order yang dipilih
const selectedOrder = ref<Order | null>(null);

// fungsi untuk buka modal detail order
function showOrderDetail(order: Order) {
    selectedOrder.value = order;
    openModalDetail.value = true;
}

// fungsi untuk buka modal detail shipping
function showOrderShipping(order: Order) {
    selectedOrder.value = order;
    openModalDetailShipping.value = true;
}
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="p-10">
            <div class="mb-2 flex justify-between">
                <Select id="category">
                    <SelectTrigger>
                        <SelectValue> <FilterIcon /> Filter </SelectValue>
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectLabel>Status</SelectLabel>
                            <SelectItem value="active"> Active </SelectItem>
                            <SelectItem value="inactive"> Inactive </SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>
            </div>
            <!-- search and print laporan -->
            <div class="flex items-center justify-between">
                <div class="relative mb-2 w-full max-w-sm items-center md:max-w-md">
                    <Input id="search" type="text" placeholder="Search..." class="pl-10" />
                    <span class="absolute inset-y-0 start-0 flex items-center justify-center px-2">
                        <Search class="size-5 text-muted-foreground" />
                    </span>
                </div>
                <div>
                    <Button type="button" variant="outline" class="cursor-pointer"> <PrinterIcon /> Export </Button>
                </div>
            </div>

            <Table>
                <TableCaption>A list of your recent orders.</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead>No</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Order Status</TableHead>
                        <TableHead>Code</TableHead>
                        <TableHead>Payment Type</TableHead>
                        <TableHead>Date</TableHead>
                        <TableHead class="font-medium">Total</TableHead>
                        <TableHead>Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="(order, index) in props.orders.data" :key="index">
                        <TableCell>{{ index + 1 }}</TableCell>
                        <TableCell class="font-medium">
                            <Badge v-if="order.status == 'paid'" class="bg-green-600 uppercase">{{ order.status }}</Badge>
                            <Badge v-else-if="order.status == 'pending'" class="uppercase" variant="secondary">{{ order.status }}</Badge>
                            <Badge v-else class="uppercase" variant="destructive">{{ order.status }}</Badge>
                        </TableCell>
                        <TableCell class="font-medium">
                            <Badge v-if="order.order_status == 'done'" class="bg-green-600 uppercase">{{ order.order_status }}</Badge>
                            <Badge v-else-if="order.order_status == 'process'" class="uppercase" variant="secondary">{{ order.order_status }}</Badge>
                        </TableCell>
                        <TableCell
                            ><kbd>{{ order.order_code }}</kbd></TableCell
                        >
                        <TableCell>{{ order.midtrans_payment_type ?? '-' }}</TableCell>
                        <TableCell>{{ order.created_at }}</TableCell>
                        <TableCell class="font-medium">{{ formatRupiah(order.total) }}</TableCell>
                        <TableCell class="space-x-2">
                            <Button size="sm" class="hover:cursor-pointer" variant="destructive" title=" shipping information"><Trash /> </Button>
                            <!-- dropdown -->
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button size="sm" class="hover:cursor-pointer" variant="outline"><EllipsisVertical /> </Button>
                                </DropdownMenuTrigger>

                                <DropdownMenuContent class="mr-10 w-56">
                                    <DropdownMenuCheckboxItem @click="showOrderDetail(order)">
                                        <ShoppingCart /> Detail Order
                                    </DropdownMenuCheckboxItem>
                                    <DropdownMenuCheckboxItem @click="showOrderShipping(order)">
                                        <MapPin /> Detail Shipping
                                    </DropdownMenuCheckboxItem>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuCheckboxItem> <Check /> Order Done </DropdownMenuCheckboxItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </section>

        <!-- modal detail dialog -->
        <Dialog v-model:open="openModalDetail">
            <DialogContent class="sm:max-w-[600px]">
                <DialogHeader>
                    <DialogTitle>Order Information</DialogTitle>
                </DialogHeader>
                <div v-if="selectedOrder" class="grid grid-cols-1 gap-x-3 gap-y-4 py-4 text-[13px] md:grid-cols-2">
                    <div>
                        <p><b>Status :</b> {{ selectedOrder.status }}</p>
                        <p><b>Order Code :</b> {{ selectedOrder.order_code }}</p>
                        <p><b>Payment Type :</b> {{ selectedOrder.midtrans_payment_type ?? '-' }}</p>
                        <p><b>Total :</b> {{ formatRupiah(selectedOrder.total) }}</p>
                        <p><b>Date :</b> {{ selectedOrder.created_at }}</p>
                    </div>
                    <div>
                        <p><b>Order Status : </b> {{ selectedOrder.order_status }}</p>
                        <p><b>Pemesan :</b> {{ selectedOrder.user.name }}</p>
                        <p><b>Email :</b> {{ selectedOrder.user.email }}</p>
                    </div>
                    <div>
                        <p><b>Transaction Status : </b> {{ selectedOrder.midtrans_transaction_status ?? '-' }}</p>
                        <p><b>Transaction Id : </b> {{ selectedOrder.midtrans_transaction_id ?? '-' }}</p>
                        <p><b>Fraud Status : </b> {{ selectedOrder.fraud_status ?? '-' }}</p>
                    </div>
                    <div>
                        <p><b>Ekspedisi : </b> {{ selectedOrder.shipping_name ?? '-' }}</p>
                        <p><b>Service : </b> {{ selectedOrder.shipping_service }}</p>
                        <p><b>Cost : </b> {{ formatRupiah(selectedOrder.shipping_cost) }}</p>
                        <p><b>Etd : </b> {{ selectedOrder.shipping_etd }}</p>
                    </div>
                </div>
                <div>
                    <table class="w-full border-collapse overflow-hidden rounded-xl shadow-md">
                        <thead class="rounded-xl bg-gray-100 text-gray-800">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium">Produk</th>
                                <th class="px-4 py-2 text-sm font-medium">Subtotal</th>
                                <th class="px-4 py-2 text-sm font-medium">Quantity</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="(item, index) in selectedOrder?.order_items" :key="index" class="text-center text-[14px]">
                                <td class="px-4 py-2 text-left text-sm">{{ item.product }}</td>
                                <td class="px-4 py-2 text-sm">{{ formatRupiah(item.subtotal) }}</td>
                                <td class="px-4 py-2 text-sm">{{ item.quantity }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <DialogFooter>
                    <Button> <Printer /> </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
        <!-- modal detail dialog shipping -->
        <Dialog v-model:open="openModalDetailShipping">
            <DialogContent class="sm:max-w-[600px]">
                <DialogHeader>
                    <DialogTitle>Shipping Information</DialogTitle>
                </DialogHeader>
                <div class="">
                    <div class="text-[14px]">
                        <p>{{ selectedOrder?.shipping_name }} - {{ selectedOrder?.shipping_service }}</p>
                    </div>
                    <div class="mt-2 grid grid-cols-1 gap-4 text-[14px] md:grid-cols-2">
                        <div>
                            <p>From : Trendigo</p>
                            <p>Kreo,Larangan, Kota Tangerang Selatan, 15156</p>
                        </div>
                        <div>
                            <p class="font-medium">
                                To : {{ selectedOrder?.shipping_address.recipient_name }} ({{ selectedOrder?.shipping_address.phone }})
                            </p>
                            <p>
                                KEL. {{ selectedOrder?.shipping_address.sub_district }}, KEC.{{ selectedOrder?.shipping_address.district }},
                                {{ selectedOrder?.shipping_address.city }}, {{ selectedOrder?.shipping_address.province }} -
                                {{ selectedOrder?.shipping_address.postal_code }}
                            </p>
                            <p>{{ selectedOrder?.shipping_address.address }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <table class="w-full border-collapse overflow-hidden rounded-xl border shadow-md">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="px-4 py-2 text-left text-sm">Produk</th>
                                    <th class="px-4 py-2 text-center text-sm">Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in selectedOrder?.order_items" :key="index" class="">
                                    <td class="px-4 py-2 text-sm">{{ item.product }}</td>
                                    <td class="px-4 py-2 text-center text-sm">{{ item.quantity }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <DialogFooter>
                    <Button> <Printer /> </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
