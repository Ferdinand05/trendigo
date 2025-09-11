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
import { Head, useForm } from '@inertiajs/vue3';
import { Edit, Plus, Trash } from 'lucide-vue-next';
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
</script>

<template>
    <Head title="Categories" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Toaster />
        <div class="p-10">
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
