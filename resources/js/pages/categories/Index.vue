<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import AlertDialog from '@/components/ui/alert-dialog/AlertDialog.vue';
import AlertDialogAction from '@/components/ui/alert-dialog/AlertDialogAction.vue';
import AlertDialogCancel from '@/components/ui/alert-dialog/AlertDialogCancel.vue';
import AlertDialogContent from '@/components/ui/alert-dialog/AlertDialogContent.vue';
import AlertDialogDescription from '@/components/ui/alert-dialog/AlertDialogDescription.vue';
import AlertDialogFooter from '@/components/ui/alert-dialog/AlertDialogFooter.vue';
import AlertDialogHeader from '@/components/ui/alert-dialog/AlertDialogHeader.vue';
import AlertDialogTitle from '@/components/ui/alert-dialog/AlertDialogTitle.vue';
import AlertDialogTrigger from '@/components/ui/alert-dialog/AlertDialogTrigger.vue';
import Button from '@/components/ui/button/Button.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogDescription from '@/components/ui/dialog/DialogDescription.vue';
import DialogFooter from '@/components/ui/dialog/DialogFooter.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import DialogTrigger from '@/components/ui/dialog/DialogTrigger.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Pagination from '@/components/ui/pagination/Pagination.vue';
import PaginationContent from '@/components/ui/pagination/PaginationContent.vue';
import PaginationItem from '@/components/ui/pagination/PaginationItem.vue';
import PaginationNext from '@/components/ui/pagination/PaginationNext.vue';
import PaginationPrevious from '@/components/ui/pagination/PaginationPrevious.vue';
import Table from '@/components/ui/table/Table.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCaption from '@/components/ui/table/TableCaption.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
import TableHead from '@/components/ui/table/TableHead.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import Tooltip from '@/components/ui/tooltip/Tooltip.vue';
import TooltipContent from '@/components/ui/tooltip/TooltipContent.vue';
import TooltipProvider from '@/components/ui/tooltip/TooltipProvider.vue';
import TooltipTrigger from '@/components/ui/tooltip/TooltipTrigger.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { PaginationLinks, PaginationMeta } from '@/types/pagination';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { Edit, Plus, Search, Trash } from 'lucide-vue-next';
import { ref } from 'vue';
import { Toaster, toast } from 'vue-sonner';
import 'vue-sonner/style.css';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Categories',
        href: '/Categories',
    },
];

interface Categories {
    id: number;
    name: string;
    slug?: string;
    description: string;
    description_limit?: string;
    products_count?: number;
    created_at?: string;
}

const props = defineProps<{
    categories: {
        data: Categories[];
        links: PaginationLinks;
        meta: PaginationMeta;
    };
}>();

const formCreate = useForm({
    name: '',
    description: '',
});

const handleSubmit = () => {
    formCreate.post(route('categories.store'), {
        preserveScroll: true,
        onSuccess: (result: any) => {
            toast('Created!', {
                description: `${result.props.flash.message}`,
                action: {
                    label: 'Close',
                    onClick: () => console.log('Closed'),
                },
            });

            formCreate.reset();
        },
    });
};

const openModalDialog = ref(false);
const formUpdate = useForm({
    id: NaN,
    name: '',
    description: '',
});

function fillDataModal(category: Categories) {
    formUpdate.id = category.id;
    formUpdate.name = category.name;
    formUpdate.description = category.description;
    openModalDialog.value = true;
}

const updateCategory = () => {
    formUpdate.put(route('categories.update', formUpdate.id), {
        preserveScroll: true,
        onSuccess: (result: any) => {
            toast('Updated!', {
                description: `${result.props.flash.message}`,
                action: {
                    label: 'Close',
                    onClick: () => console.log('Closed'),
                },
            });

            formCreate.reset();
            openModalDialog.value = false;
        },
    });
};

const formDestroy = useForm({
    id: NaN,
});
const destroyCategory = (id: number) => {
    formDestroy.id = id;
    formDestroy.delete(route('categories.destroy', id), {
        preserveScroll: true,
        onSuccess: (result: any) => {
            toast('Deleted!', {
                description: `${result.props.flash.message}`,
                action: {
                    label: 'Close',
                    onClick: () => console.log('Closed'),
                },
            });

            formDestroy.reset();
        },
    });
};

const search = ref('');
watchDebounced(search, (keyword) => {
    router.get(
        route('categories.index'),
        {
            search: keyword,
        },
        {
            preserveScroll: true,
            preserveState: true,
        },
    );
});
</script>

<template>
    <Head title="Categories" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Toaster />
        <div class="p-10">
            <div class="flex justify-between">
                <div class="relative mb-2 w-full max-w-sm items-center md:max-w-md">
                    <Input v-model="search" id="search" type="text" placeholder="Search..." class="pl-10" />
                    <span class="absolute inset-y-0 start-0 flex items-center justify-center px-2">
                        <Search class="size-5 text-muted-foreground" />
                    </span>
                </div>

                <div class="mb-3 flex justify-end">
                    <Dialog>
                        <DialogTrigger>
                            <Button @click.prevent="formCreate.resetAndClearErrors()"><Plus /> Create new Category</Button>
                        </DialogTrigger>
                        <DialogContent>
                            <DialogHeader>
                                <DialogTitle>Create a new Category</DialogTitle>
                                <DialogDescription>Create new category for your products</DialogDescription>
                            </DialogHeader>
                            <form @submit.prevent="handleSubmit()">
                                <div class="space-y-4">
                                    <div class="space-y-2">
                                        <Label for="name">Category name</Label>
                                        <Input id="name" v-model="formCreate.name"></Input>
                                        <InputError :message="formCreate.errors.name" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="desc">Description</Label>
                                        <Textarea id="desc" v-model="formCreate.description" />
                                        <InputError :message="formCreate.errors.description" />
                                    </div>
                                </div>
                                <DialogFooter>
                                    <Button type="submit" :disabled="formCreate.processing">Create Category</Button>
                                </DialogFooter>
                            </form>
                        </DialogContent>
                    </Dialog>
                </div>
            </div>
            <Table>
                <TableCaption>A list of your recent Categories.</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead>No</TableHead>
                        <TableHead>Name</TableHead>
                        <TableHead>Description</TableHead>
                        <TableHead>Count</TableHead>
                        <TableHead>Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="(category, index) in props.categories.data" :key="category.id">
                        <TableCell>{{ index + 1 }}</TableCell>
                        <TableCell class="font-medium">{{ category.name }}</TableCell>
                        <TableCell>
                            <TooltipProvider>
                                <Tooltip>
                                    <TooltipTrigger>{{ category.description_limit }}</TooltipTrigger>
                                    <TooltipContent>
                                        {{ category.description }}
                                    </TooltipContent>
                                </Tooltip>
                            </TooltipProvider>
                        </TableCell>
                        <TableCell>{{ category.products_count }}</TableCell>
                        <TableCell class="space-x-2">
                            <Button size="sm" variant="secondary" @click="fillDataModal(category)"><Edit /></Button>
                            <!-- delete user -->
                            <AlertDialog>
                                <AlertDialogTrigger as-child>
                                    <Button size="sm" variant="destructive">
                                        <Trash />
                                    </Button>
                                </AlertDialogTrigger>
                                <AlertDialogContent>
                                    <AlertDialogHeader>
                                        <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
                                        <AlertDialogDescription> You are going to delete category "{{ category.name }}" </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                                        <AlertDialogAction class="bg-red-600 hover:bg-red-700" @click.prevent="destroyCategory(category.id)">
                                            Delete
                                        </AlertDialogAction>
                                    </AlertDialogFooter>
                                </AlertDialogContent>
                            </AlertDialog>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <!-- Pagination -->
            <div class="mt-2">
                <div class="text-sm text-gray-600">
                    Showing {{ props.categories.data.length }} from {{ props.categories.meta.from }} of {{ props.categories.meta.total }}
                </div>
                <Pagination
                    :v-slot="props.categories.meta.current_page"
                    :items-per-page="props.categories.meta.per_page"
                    :total="props.categories.meta.total"
                    :default-page="props.categories.meta.current_page"
                >
                    <PaginationContent :v-slot="props.categories.meta.links">
                        <Button variant="link" :disabled="props.categories.links.prev == null">
                            <Link :href="props.categories.links.prev ?? ''">
                                <PaginationPrevious />
                            </Link>
                        </Button>

                        <template v-for="(item, index) in props.categories.meta.links" :key="index">
                            <PaginationItem v-if="!isNaN(Number(item.label))" :value="props.categories.meta.current_page" :is-active="item.active">
                                <Link :href="item.url ?? ''">
                                    {{ item.label }}
                                </Link>
                            </PaginationItem>
                        </template>

                        <Button variant="link" :disabled="props.categories.links.next == null">
                            <Link :href="props.categories.links.next ?? ''">
                                <PaginationNext> </PaginationNext>
                            </Link>
                        </Button>
                    </PaginationContent>
                </Pagination>
            </div>
            <!-- END Pagination -->

            <!-- modal edit/update -->
            <Dialog v-model:open="openModalDialog">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Edit Category</DialogTitle>
                        <DialogDescription> Make changes to your category here. Click save when you're done. </DialogDescription>
                    </DialogHeader>
                    <form @submit.prevent="updateCategory()">
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <Label for="name">Category name</Label>
                                <Input id="name" v-model="formUpdate.name"></Input>
                                <InputError :message="formUpdate.errors.name" />
                            </div>
                            <div class="space-y-2">
                                <Label for="desc">Description</Label>
                                <Textarea id="desc" v-model="formUpdate.description" />
                                <InputError :message="formUpdate.errors.description" />
                            </div>
                        </div>
                        <DialogFooter>
                            <Button type="submit" :disabled="formUpdate.processing">Save</Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
