<script setup lang="ts">
import BarChart from '@/components/ui/chart-bar/BarChart.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { DollarSign, Package, Users } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const props = defineProps<{
    totalUser: number;
    totalProduct: number;
    totalRevenue: number;
    percentage: number;
}>();

const data = [
    { name: 'Jan', total: Math.floor(Math.random() * 2000) + 500, predicted: Math.floor(Math.random() * 2000) + 500 },
    { name: 'Feb', total: Math.floor(Math.random() * 2000) + 500, predicted: Math.floor(Math.random() * 2000) + 500 },
    { name: 'Mar', total: Math.floor(Math.random() * 2000) + 500, predicted: Math.floor(Math.random() * 2000) + 500 },
    { name: 'Apr', total: Math.floor(Math.random() * 2000) + 500, predicted: Math.floor(Math.random() * 2000) + 500 },
    { name: 'May', total: Math.floor(Math.random() * 2000) + 500, predicted: Math.floor(Math.random() * 2000) + 500 },
    { name: 'Jun', total: Math.floor(Math.random() * 2000) + 500, predicted: Math.floor(Math.random() * 2000) + 500 },
    { name: 'Jul', total: Math.floor(Math.random() * 2000) + 500, predicted: Math.floor(Math.random() * 2000) + 500 },
    { name: 'Agu', total: Math.floor(Math.random() * 2000) + 500, predicted: Math.floor(Math.random() * 2000) + 500 },
    { name: 'Sept', total: Math.floor(Math.random() * 2000) + 500, predicted: Math.floor(Math.random() * 2000) + 500 },
    { name: 'Okt', total: Math.floor(Math.random() * 2000) + 500, predicted: Math.floor(Math.random() * 2000) + 500 },
];

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
                            <div class="">Total User</div>
                            <div><Users /></div>
                        </div>
                        <div class="space-y-2">
                            <div class="text-3xl font-bold">{{ props.totalUser }} Users</div>
                            <div class="text-[14px] text-gray-600">Registered user</div>
                        </div>
                    </div>
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <div class="space-y-6 px-6 py-8">
                        <div class="flex justify-between text-2xl font-light">
                            <div class="">Total Product</div>
                            <div><Package /></div>
                        </div>
                        <div class="space-y-2">
                            <div class="text-3xl font-bold">{{ props.totalProduct }} Product</div>
                            <div class="text-[14px] text-gray-600">Products for sale</div>
                        </div>
                    </div>
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <div class="space-y-6 px-6 py-8">
                        <div class="flex justify-between text-2xl font-light">
                            <div class="">Total Revenue</div>
                            <div><DollarSign /></div>
                        </div>
                        <div class="space-y-2">
                            <div class="text-3xl font-bold">Rp. {{ formatRupiah(props.totalRevenue) }}</div>
                            <div class="text-[14px] text-gray-600">+ {{ props.percentage }} % from last month</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <div class="p-4">
                    <BarChart
                        :data="data"
                        index="name"
                        :categories="['total', 'predicted']"
                        :y-formatter="
                            (tick, i) => {
                                return typeof tick === 'number' ? `$ ${new Intl.NumberFormat('us').format(tick).toString()}` : '';
                            }
                        "
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
