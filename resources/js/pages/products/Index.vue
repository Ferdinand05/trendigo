<script setup lang="ts">
import AlertDialog from '@/components/ui/alert-dialog/AlertDialog.vue';
import AlertDialogAction from '@/components/ui/alert-dialog/AlertDialogAction.vue';
import AlertDialogCancel from '@/components/ui/alert-dialog/AlertDialogCancel.vue';
import AlertDialogContent from '@/components/ui/alert-dialog/AlertDialogContent.vue';
import AlertDialogDescription from '@/components/ui/alert-dialog/AlertDialogDescription.vue';
import AlertDialogFooter from '@/components/ui/alert-dialog/AlertDialogFooter.vue';
import AlertDialogHeader from '@/components/ui/alert-dialog/AlertDialogHeader.vue';
import AlertDialogTitle from '@/components/ui/alert-dialog/AlertDialogTitle.vue';
import AlertDialogTrigger from '@/components/ui/alert-dialog/AlertDialogTrigger.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import Button from '@/components/ui/button/Button.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogClose from '@/components/ui/dialog/DialogClose.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogDescription from '@/components/ui/dialog/DialogDescription.vue';
import DialogFooter from '@/components/ui/dialog/DialogFooter.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import Input from '@/components/ui/input/Input.vue';
import Pagination from '@/components/ui/pagination/Pagination.vue';
import PaginationContent from '@/components/ui/pagination/PaginationContent.vue';
import PaginationItem from '@/components/ui/pagination/PaginationItem.vue';
import PaginationNext from '@/components/ui/pagination/PaginationNext.vue';
import PaginationPrevious from '@/components/ui/pagination/PaginationPrevious.vue';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import SelectLabel from '@/components/ui/select/SelectLabel.vue';
import Separator from '@/components/ui/separator/Separator.vue';
import Table from '@/components/ui/table/Table.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
import TableHead from '@/components/ui/table/TableHead.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import Toggle from '@/components/ui/toggle/Toggle.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { PaginationLinks, PaginationMeta } from '@/types/pagination';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { Clock, Edit, EyeIcon, FilterIcon, Plus, Printer, Search, ToggleLeft, ToggleRight, Trash, Trash2 } from 'lucide-vue-next';
import { reactive, ref, watch } from 'vue';
import { Toaster, toast } from 'vue-sonner';
import 'vue-sonner/style.css';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: '/products',
    },
];
// const page = usePage();
type ProductImages = {
    id: number;
    image_path: string;
    is_primary?: number;
};
type Products = {
    id: number;
    category: string;
    name: string;
    created_at: string;
    description: string;
    stock: number;
    weight: number;
    price: number;
    is_active: boolean | null;
    limit_name?: string;
    product_images: ProductImages[];
};

const props = defineProps<{
    products: {
        data: Products[];
        meta: PaginationMeta;
        links: PaginationLinks;
    };
}>();

const openModalDetail = ref(false);
const dataModal: Products = reactive({
    id: 0,
    name: '',
    description: '',
    category: '',
    price: 0,
    weight: 0,
    stock: 0,
    product_images: [],
    is_active: null,
    created_at: '',
});
const fillDataModal = (product: Products) => {
    dataModal.id = product.id;
    dataModal.name = product.name;
    dataModal.description = product.description;
    dataModal.category = product.category;
    dataModal.price = product.price;
    dataModal.weight = product.weight;
    dataModal.stock = product.stock;
    dataModal.product_images = product.product_images;
    dataModal.is_active = product.is_active;
    dataModal.created_at = product.created_at;

    // buka modal
    openModalDetail.value = true;
};

const imgUrl = '/storage/';

const formDelete = useForm({
    id: 0,
    _method: 'delete',
});

function destroy(id: number) {
    formDelete.id = id;
    formDelete.post(route('products.destroy', id), {
        preserveScroll: true,
        onSuccess: (result: any) => {
            toast('Deleted!', {
                description: `${result.props.flash.message}`,
                action: {
                    label: 'Close',
                    onClick: () => console.log('Closed'),
                },
                icon: Trash2,
            });
        },
    });
}

type UpdateStatus = {
    id: number | null;
    is_active: boolean | null;
};
const formUpdateStatus = useForm<UpdateStatus>({
    id: null,
    is_active: null,
});
function updateStatusProduct(id: number | null, status: boolean | null) {
    console.log(id);
    console.log(status);
    formUpdateStatus.id = id;
    formUpdateStatus.is_active = status;
    formUpdateStatus.post(route('product.update.status'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {},
    });
}

const selectedStatus = ref<string | null>(null);

watch(selectedStatus, (newVal) => {
    filterStatus(newVal);
});

function filterStatus(val: string | null) {
    router.get(
        route('products.index'),
        {
            status: val,
        },
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
}

// state untuk search
const search = ref('');

watchDebounced(
    search,
    (value) => {
        console.log(value);
        searchKeyword(value);
    },
    {
        debounce: 600,
        maxWait: 1200,
    },
);

function searchKeyword(keyword: string | null) {
    router.get(
        route('products.index'),
        {
            search: keyword,
        },
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
}
</script>

<template>
    <Head title="Products" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Toaster />
        <div class="p-10">
            <div class="mb-2 flex justify-between">
                <Select v-model="selectedStatus" id="category">
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

                <Link :href="route('products.create')">
                    <Button><Plus /> Create new product</Button>
                </Link>
            </div>
            <!-- search and print laporan -->
            <div class="flex items-center justify-between">
                <div class="relative mb-2 w-full max-w-sm items-center md:max-w-md">
                    <Input v-model="search" id="search" type="text" placeholder="Search..." class="pl-10" />
                    <span class="absolute inset-y-0 start-0 flex items-center justify-center px-2">
                        <Search class="size-5 text-muted-foreground" />
                    </span>
                </div>
                <div>
                    <form :action="route('print.products')" method="get" target="_blank">
                        <Button type="submit" class="hover:cursor-pointer">Export PDF <Printer /></Button>
                    </form>
                </div>
            </div>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>No</TableHead>
                        <TableHead>Name</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Category</TableHead>
                        <TableHead>Price</TableHead>
                        <TableHead>Stock </TableHead>
                        <TableHead>Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="(product, index) in props.products.data" :key="product.id">
                        <TableCell>
                            {{ index + 1 + (props.products.meta.current_page - 1) * props.products.meta.per_page }}
                        </TableCell>
                        <TableCell>{{ product.limit_name }}</TableCell>
                        <TableCell>
                            <Toggle
                                v-model="product.is_active"
                                class="cursor-pointer"
                                @click="updateStatusProduct(product.id, product.is_active)"
                                :disabled="formUpdateStatus.processing"
                                :title="product.is_active == true ? 'active' : 'inactive'"
                            >
                                <div v-if="product.is_active == true"><ToggleRight class="text-blue-600" /></div>
                                <div v-else><ToggleLeft class="text-red-500" /></div>
                            </Toggle>
                        </TableCell>
                        <TableCell>{{ product.category }}</TableCell>
                        <TableCell>{{ product.price }}</TableCell>
                        <TableCell>{{ product.stock }}</TableCell>
                        <TableCell>
                            <div class="flex gap-x-2">
                                <Button size="sm" variant="outline" @click.prevent="fillDataModal(product)"> <EyeIcon /> </Button>
                                <Link :href="route('products.edit', product.id)">
                                    <Button size="sm" variant="secondary"> <Edit /> </Button>
                                </Link>

                                <!-- delete user -->
                                <AlertDialog>
                                    <AlertDialogTrigger as-child>
                                        <Button size="sm" variant="destructive" :disabled="formDelete.processing">
                                            <Trash />
                                        </Button>
                                    </AlertDialogTrigger>
                                    <AlertDialogContent>
                                        <AlertDialogHeader>
                                            <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
                                            <AlertDialogDescription> You are going to delete user "{{ product.name }}" </AlertDialogDescription>
                                        </AlertDialogHeader>
                                        <AlertDialogFooter>
                                            <AlertDialogCancel>Cancel</AlertDialogCancel>
                                            <AlertDialogAction @click.prevent="destroy(product.id)" class="bg-red-600 hover:bg-red-700">
                                                Delete
                                            </AlertDialogAction>
                                        </AlertDialogFooter>
                                    </AlertDialogContent>
                                </AlertDialog>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <!-- pagination -->
            <div class="mt-2">
                <div class="text-sm font-medium text-gray-600">
                    Showing {{ props.products.meta.per_page }} from {{ props.products.meta.from }} of {{ props.products.meta.total }}
                </div>
                <Pagination
                    :v-slot="props.products.meta.current_page"
                    :items-per-page="props.products.meta.per_page"
                    :total="props.products.meta.total"
                    :default-page="props.products.meta.current_page"
                >
                    <PaginationContent :v-slot="props.products.meta.links">
                        <Button variant="link" :disabled="props.products.links.prev == null">
                            <Link :href="props.products.links.prev ?? ''">
                                <PaginationPrevious />
                            </Link>
                        </Button>

                        <template v-for="(item, index) in props.products.meta.links" :key="index">
                            <PaginationItem v-if="!isNaN(Number(item.label))" :value="props.products.meta.current_page" :is-active="item.active">
                                <Link :href="item.url ?? ''">
                                    {{ item.label }}
                                </Link>
                            </PaginationItem>
                        </template>

                        <Button variant="link" :disabled="props.products.links.next == null">
                            <Link :href="props.products.links.next ?? ''">
                                <PaginationNext> </PaginationNext>
                            </Link>
                        </Button>
                    </PaginationContent>
                </Pagination>
            </div>
            <!-- dialog modal detail -->
            <Dialog v-model:open="openModalDetail">
                <DialogContent class="sm:max-w-sm md:max-w-xl">
                    <DialogHeader>
                        <DialogTitle class="text-gray-600">Product Details</DialogTitle>
                        <DialogDescription class="text-xl font-bold text-gray-900">{{ dataModal.name }}</DialogDescription>
                        <DialogDescription class="text-gray-600">{{ dataModal.description }} </DialogDescription>
                        <div>
                            <Badge>{{ dataModal.category }}</Badge>
                        </div>
                    </DialogHeader>
                    <Separator />
                    <ul class="flex justify-around font-medium">
                        <li>Rp. {{ dataModal.price }}</li>
                        <li>{{ dataModal.stock }} Unit</li>
                        <li>{{ dataModal.weight }} gram</li>
                    </ul>
                    <Separator />
                    <div class="flex justify-around gap-3">
                        <div v-for="image in dataModal.product_images" :key="image.id">
                            <img class="h-44 object-cover" :src="imgUrl + image.image_path" :alt="dataModal.name" />
                        </div>
                    </div>
                    <div class="flex items-center gap-x-1 text-gray-500">
                        <span><Clock class="w-4" /></span>
                        <small> {{ dataModal.created_at }} </small>
                    </div>
                    <DialogFooter class="sm:justify-start">
                        <DialogClose as-child>
                            <Button type="button" variant="secondary"> Close </Button>
                        </DialogClose>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
