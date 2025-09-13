<script lang="ts" setup>
import Badge from '@/components/ui/badge/Badge.vue';
import Button from '@/components/ui/button/Button.vue';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { DropdownMenu, DropdownMenuCheckboxItem, DropdownMenuContent } from '@/components/ui/dropdown-menu';
import DropdownMenuSeparator from '@/components/ui/dropdown-menu/DropdownMenuSeparator.vue';
import DropdownMenuTrigger from '@/components/ui/dropdown-menu/DropdownMenuTrigger.vue';
import Input from '@/components/ui/input/Input.vue';
import { PaginationContent } from '@/components/ui/pagination';
import Pagination from '@/components/ui/pagination/Pagination.vue';
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
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Order } from '@/types/orders';
import { PaginationLinks, PaginationMeta } from '@/types/pagination';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { Check, EllipsisVertical, FilterIcon, MapPin, Printer, Search, ShoppingCart, Trash } from 'lucide-vue-next';
import Swal from 'sweetalert2';
import { ref, watch } from 'vue';
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
        meta: PaginationMeta;
        links: PaginationLinks;
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

const selectedStatus = ref();
watch(selectedStatus, (newVal) => {
    router.get(
        route('orders.index'),
        {
            status: newVal,
        },
        {
            preserveScroll: true,
            preserveState: true,
        },
    );
});

const keyword = ref('');
watchDebounced(
    keyword,
    (newKeyword) => {
        router.get(
            route('orders.index'),
            {
                keyword: newKeyword,
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
const page = usePage();
function orderDone(orderId: number) {
    router.post(
        route('order.done'),
        {
            orderId: orderId,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                if (page.props.flash.message == 'Order Done!') {
                    Swal.fire({
                        title: 'Good job!',
                        text: `${page.props.flash.message}`,
                        icon: 'success',
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: `${page.props.flash.message}`,
                    });
                }
            },
        },
    );
}

function destroyOrder(id: number) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('order.destroy', id), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: 'Good job!',
                        text: `${page.props.flash.message}`,
                        icon: 'success',
                    });
                },
            });
        }
    });
}
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="p-10">
            <div class="mb-2 flex justify-between">
                <Select id="category" v-model="selectedStatus">
                    <SelectTrigger>
                        <SelectValue> <FilterIcon /> Filter </SelectValue>
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectLabel>Status</SelectLabel>
                            <SelectItem value=" "> <Badge variant="outline">All</Badge> </SelectItem>
                            <SelectItem value="pending"> <Badge variant="secondary">Pending</Badge> </SelectItem>
                            <SelectItem value="paid"> <Badge class="bg-green-600">Paid</Badge> </SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>
            </div>
            <!-- search and print laporan -->
            <div class="flex items-center justify-between">
                <div class="relative mb-2 w-full max-w-sm items-center md:max-w-md">
                    <Input id="search" v-model="keyword" type="text" placeholder="Search..." class="pl-10" />
                    <span class="absolute inset-y-0 start-0 flex items-center justify-center px-2">
                        <Search class="size-5 text-muted-foreground" />
                    </span>
                </div>
                <div>
                    <form :action="route('print.orders.pdf')" method="get" target="_blank">
                        <input type="hidden" v-model="selectedStatus" name="status" />
                        <Button type="submit" class="hover:cursor-pointer">Export PDF <Printer /></Button>
                    </form>
                </div>
            </div>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>No</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Order Status</TableHead>
                        <TableHead>Code</TableHead>
                        <TableHead>Username</TableHead>
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
                        <TableCell>{{ order.user.name }}</TableCell>
                        <TableCell>{{ order.midtrans_payment_type ?? '-' }}</TableCell>
                        <TableCell>{{ order.created_at }}</TableCell>
                        <TableCell class="font-medium">{{ formatRupiah(order.total) }}</TableCell>
                        <TableCell class="space-x-2">
                            <Button
                                size="sm"
                                @click.prevent="destroyOrder(order.id)"
                                class="hover:cursor-pointer"
                                variant="destructive"
                                title=" shipping information"
                                ><Trash />
                            </Button>
                            <!-- dropdown -->
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button size="sm" class="hover:cursor-pointer" variant="outline"><EllipsisVertical /> </Button>
                                </DropdownMenuTrigger>

                                <DropdownMenuContent class="mr-10 w-48">
                                    <DropdownMenuCheckboxItem @click="showOrderDetail(order)">
                                        <ShoppingCart /> Detail Order
                                    </DropdownMenuCheckboxItem>
                                    <DropdownMenuCheckboxItem @click="showOrderShipping(order)">
                                        <MapPin /> Detail Shipping
                                    </DropdownMenuCheckboxItem>
                                    <DropdownMenuSeparator v-show="order.order_status == 'process'" />
                                    <DropdownMenuCheckboxItem @click.prevent="orderDone(order.id)" v-show="order.order_status == 'process'">
                                        <Check /> Order Done
                                    </DropdownMenuCheckboxItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <!-- Pagination -->
            <div class="mt-2">
                <div class="text-sm text-gray-600">
                    Showing {{ props.orders.meta.per_page }} from {{ props.orders.meta.from }} of {{ props.orders.meta.total }}
                </div>
                <Pagination
                    :v-slot="props.orders.meta.current_page"
                    :items-per-page="props.orders.meta.per_page"
                    :total="props.orders.meta.total"
                    :default-page="props.orders.meta.current_page"
                >
                    <PaginationContent :v-slot="props.orders.meta.links">
                        <Button variant="link" :disabled="props.orders.links.prev == null">
                            <Link :href="props.orders.links.prev ?? ''">
                                <PaginationPrevious />
                            </Link>
                        </Button>

                        <template v-for="(item, index) in props.orders.meta.links" :key="index">
                            <PaginationItem v-if="!isNaN(Number(item.label))" :value="props.orders.meta.current_page" :is-active="item.active">
                                <Link :href="item.url ?? ''">
                                    {{ item.label }}
                                </Link>
                            </PaginationItem>
                        </template>

                        <Button variant="link" :disabled="props.orders.links.next == null">
                            <Link :href="props.orders.links.next ?? ''">
                                <PaginationNext> </PaginationNext>
                            </Link>
                        </Button>
                    </PaginationContent>
                </Pagination>
            </div>
            <!-- END Pagination -->
        </section>

        <!-- modal detail dialog -->
        <Dialog v-model:open="openModalDetail">
            <DialogContent class="max-h-[600px] sm:max-w-[600px]">
                <DialogHeader>
                    <DialogTitle>Order Information</DialogTitle>
                </DialogHeader>
                <div class="h-[450px] overflow-y-scroll px-2 pb-5">
                    <div v-if="selectedOrder" class="grid grid-cols-1 gap-x-3 gap-y-4 py-4 text-[14px] md:grid-cols-2">
                        <div class="rounded-md p-2 shadow-md">
                            <h1 class="text-xl font-semibold">Order Info</h1>
                            <p>Status : {{ selectedOrder.status }}</p>
                            <p>Order Code : {{ selectedOrder.order_code }}</p>
                            <p>Payment Type : {{ selectedOrder.midtrans_payment_type ?? '-' }}</p>
                            <p>Total : {{ formatRupiah(selectedOrder.total) }}</p>
                            <p>Date : {{ selectedOrder.created_at }}</p>
                        </div>
                        <div class="rounded-md p-2 shadow-md">
                            <h1 class="text-xl font-semibold">User Info</h1>
                            <p>Order Status : {{ selectedOrder.order_status }}</p>
                            <p>Pemesan : {{ selectedOrder.user.name }}</p>
                            <p>Email : {{ selectedOrder.user.email }}</p>
                        </div>
                        <div class="rounded-md p-2 shadow-md">
                            <h1 class="text-xl font-semibold">Transaction Info</h1>
                            <p>Transaction Status : {{ selectedOrder.midtrans_transaction_status ?? '-' }}</p>
                            <p>Transaction Id : {{ selectedOrder.midtrans_transaction_id ?? '-' }}</p>
                            <p>Fraud Status : {{ selectedOrder.fraud_status ?? '-' }}</p>
                        </div>
                        <div class="rounded-md p-2 shadow-md">
                            <h1 class="text-xl font-semibold">Shipping Info</h1>
                            <p>Ekspedisi : {{ selectedOrder.shipping_name ?? '-' }}</p>
                            <p>Service : {{ selectedOrder.shipping_service }}</p>
                            <p>Cost : {{ formatRupiah(selectedOrder.shipping_cost) }}</p>
                            <p>Etd : {{ selectedOrder.shipping_etd }}</p>
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
                </div>
                <DialogFooter>
                    <form :action="route('print.detail.order.pdf', selectedOrder?.id)" method="get" target="_blank">
                        <Button> <Printer /> </Button>
                    </form>
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
                    <form :action="route('print.shipping.order.pdf', selectedOrder?.id)" method="get" target="_blank">
                        <Button> <Printer /> </Button>
                    </form>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
