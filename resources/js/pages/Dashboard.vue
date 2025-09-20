<script setup lang="ts">
import Badge from '@/components/ui/badge/Badge.vue';
import BarChart from '@/components/ui/chart-bar/BarChart.vue';
import Table from '@/components/ui/table/Table.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
import TableHead from '@/components/ui/table/TableHead.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Order } from '@/types/orders';
import { ProductInterface } from '@/types/products';
import { Head, Link } from '@inertiajs/vue3';
import { DollarSign, ListChecks, Package } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const props = defineProps<{
    totalRevenue: number;
    percentage: number;
    orderProcess: number;
    chartData: [];
    productOutOfStock: ProductInterface[];
    todayOrder: Order[];
}>();

const data = props.chartData;

function formatRupiah(value: number) {
    return new Intl.NumberFormat('id-ID').format(value);
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <div class="space-y-6 px-6 py-8">
                        <div class="flex justify-between text-2xl font-light">
                            <div class="">Order Process</div>
                            <div><ListChecks /></div>
                        </div>
                        <div class="space-y-2">
                            <div class="text-3xl font-bold">{{ props.orderProcess }} Order</div>
                            <div class="text-[14px] text-gray-600 dark:text-gray-400">The order needs to be completed.</div>
                        </div>
                    </div>
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <div class="space-y-6 px-6 py-8">
                        <div class="flex justify-between text-2xl font-light">
                            <div class="">Product Out Of Stock</div>
                            <div><Package class="text-red-600" /></div>
                        </div>
                        <div class="space-y-2">
                            <div class="px-3">
                                <ul>
                                    <li class="list-decimal text-sm font-medium" v-for="(product, index) in productOutOfStock" :key="index">
                                        {{ product.name }}
                                    </li>
                                </ul>
                            </div>
                            <div class="text-[14px] text-gray-600 dark:text-gray-400">Products out of stock. Stock less than 5</div>
                        </div>
                    </div>
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <div class="space-y-6 px-6 py-8">
                        <div class="flex justify-between text-2xl font-light">
                            <div class="">Total Revenue</div>
                            <div><DollarSign class="text-blue-600" /></div>
                        </div>
                        <div class="space-y-2">
                            <div class="text-3xl font-bold">Rp. {{ formatRupiah(props.totalRevenue) }}</div>
                            <div class="text-[14px] text-gray-600 dark:text-gray-400">+ {{ props.percentage }} % from last month</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative min-h-[100vh] w-full flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <div class="flex w-full flex-col gap-3 p-2 md:flex-row">
                    <div class="w-full rounded-xl border p-4">
                        <div>
                            <div class="font-semibold">Chart Revenue and Total Order this year</div>
                            <div class="mb-3 text-sm text-muted-foreground">Month representated by number</div>
                        </div>
                        <BarChart
                            class="w-full"
                            :data="data"
                            index="month"
                            type="grouped"
                            :categories="['total_revenue', 'total_orders']"
                            :colors="['lightblue', 'lightgreen']"
                            :rounded-corners="4"
                            :y-formatter="
                                (tick, i) => {
                                    return typeof tick === 'number' ? `$ ${new Intl.NumberFormat('us').format(tick).toString()}` : '';
                                }
                            "
                        />
                    </div>
                    <div class="w-full rounded-xl border p-4">
                        <div>
                            <div class="font-semibold">Today's Order List</div>
                            <div class="mb-3 text-sm text-muted-foreground">
                                <Link :href="route('orders.index')" class="text-blue-400 hover:underline">Click here</Link> to manage all orders
                            </div>
                        </div>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-[100px]"> Invoice </TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Order Status</TableHead>
                                    <TableHead>Payment Type</TableHead>
                                    <TableHead> Total </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="(order, index) in todayOrder" :key="index">
                                    <TableCell class="font-medium"> {{ order.order_code }} </TableCell>
                                    <TableCell
                                        ><Badge
                                            :variant="order.status == 'pending' ? 'secondary' : 'default'"
                                            class="uppercase"
                                            :class="{
                                                'bg-red-600': order.status == 'expire',
                                                'bg-green-600': order.status == 'paid',
                                            }"
                                            >{{ order.status }}</Badge
                                        ></TableCell
                                    >
                                    <TableCell
                                        ><Badge
                                            :variant="order.order_status == 'process' ? 'secondary' : 'default'"
                                            class="uppercase"
                                            :class="{
                                                'bg-red-600': order.order_status == 'cancel',
                                                'bg-green-600': order.order_status == 'done',
                                            }"
                                            >{{ order.order_status }}</Badge
                                        ></TableCell
                                    >
                                    <TableCell>{{ order.midtrans_payment_type }}</TableCell>
                                    <TableCell> {{ formatRupiah(order.total) }} </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
