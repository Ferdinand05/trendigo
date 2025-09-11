<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Select from '@/components/ui/select/Select.vue';
import SelectContent from '@/components/ui/select/SelectContent.vue';
import SelectGroup from '@/components/ui/select/SelectGroup.vue';
import SelectItem from '@/components/ui/select/SelectItem.vue';
import SelectTrigger from '@/components/ui/select/SelectTrigger.vue';
import SelectValue from '@/components/ui/select/SelectValue.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import Tooltip from '@/components/ui/tooltip/Tooltip.vue';
import TooltipContent from '@/components/ui/tooltip/TooltipContent.vue';
import TooltipProvider from '@/components/ui/tooltip/TooltipProvider.vue';
import TooltipTrigger from '@/components/ui/tooltip/TooltipTrigger.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { CircleAlert, FileQuestionIcon, Rocket } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast, Toaster } from 'vue-sonner';
import 'vue-sonner/style.css';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: '/products',
    },
    {
        title: 'Edit Products',
        href: '/products/{id}/edit',
    },
];

type ProductImages = {
    id: number;
    image_path: string;
    is_primary?: number;
    product_id?: number;
    created_at: string;
    updated_at: string;
};
type Product = {
    id: number;
    category_id: number;
    name: string;
    created_at: string;
    description: string;
    stock: number;
    weight: number;
    price: number;
    is_active: number;
    images: ProductImages[] | File[];
};

type Categories = {
    id: number;
    name: string;
    slug?: string;
    description?: string;
};
const props = defineProps<{
    categories: Categories[];
    product: Product;
}>();

const previews = ref<string[]>([]);

const formEdit = useForm({
    id: props.product.id,
    name: props.product.name,
    category_id: props.product.category_id,
    price: props.product.price,
    weight: props.product.weight,
    stock: props.product.stock,
    description: props.product.description,
    product_images: null as File[] | null,
    _method: 'put',
});

function handleImageChange(e: Event) {
    const fileList = (e.target as HTMLInputElement).files ?? '';
    if (fileList?.length > 3) {
        toast('Failed!', {
            description: `Ooops! Can only upload up to 3 files`,
            action: {
                label: 'Close',
                onClick: () => console.log('Closed'),
            },
            icon: CircleAlert,
        });
    } else {
        if (!fileList) return;

        // convert FileList -> File[]
        const files = Array.from(fileList);

        // simpan ke form
        formEdit.product_images = files;

        // hapus preview lama
        previews.value.forEach((url) => URL.revokeObjectURL(url));
        previews.value = [];

        // buat preview baru
        files.forEach((file) => {
            previews.value.push(URL.createObjectURL(file));
        });
    }
}

const update = () => {
    formEdit.post(route('products.update', formEdit.id), {
        preserveScroll: true,
        showProgress: true,
        onSuccess: (result: any) => {
            toast('Updated!', {
                description: `${result.props.flash.message}`,
                action: {
                    label: 'Close',
                    onClick: () => console.log('Closed'),
                },
                icon: Rocket,
            });
        },
    });
};
</script>

<template>
    <Head title="Edit Products" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Toaster />
        <div class="p-10">
            <div class="mb-8">
                <h3 class="text-2xl font-semibold tracking-wide">Form Edit Product</h3>
                <p class="font-light">Edit your own product with multiple images.</p>
            </div>
            <form @submit.prevent="update()">
                <div class="mb-4">
                    <div class="grid grid-cols-2 gap-x-5">
                        <div class="space-y-2">
                            <Label for="product">Product Name</Label>
                            <Input id="product" v-model="formEdit.name" name="product" type="text" placeholder="Input product name here"></Input>
                            <InputError :message="formEdit.errors.name" />
                        </div>
                        <div class="space-y-2">
                            <Label for="category" name="category">Select category</Label>
                            <Select id="category" v-model="formEdit.category_id">
                                <SelectTrigger id="category" class="w-full">
                                    <SelectValue placeholder="Select a category" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectItem v-for="category in props.categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="formEdit.errors.category_id" />
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="grid grid-cols-2 gap-x-5">
                        <div class="space-y-2">
                            <Label for="price">Price</Label>
                            <Input v-model="formEdit.price" id="price" name="price" type="number" placeholder="000"></Input>
                            <InputError :message="formEdit.errors.price" />
                        </div>
                        <div class="space-y-2">
                            <Label for="stock" name="stock">Stock</Label>
                            <Input v-model="formEdit.stock" id="stock" name="stock" type="number" placeholder="0"></Input>
                            <InputError :message="formEdit.errors.stock" />
                        </div>
                    </div>
                </div>
                <div class="mb-4 space-y-2">
                    <Label for="weight">Weight (gram)</Label>
                    <Input v-model="formEdit.weight" type="number" placeholder="0 gram" id="weight" name="weight" />
                    <InputError :message="formEdit.errors.weight" />
                </div>
                <div class="mb-4 space-y-2">
                    <Label for="description">Description</Label>
                    <Textarea v-model="formEdit.description" id="description" name="description" placeholder="Describe your product"> </Textarea>
                    <InputError :message="formEdit.errors.description" />
                </div>
                <div class="mb-4 space-y-2">
                    <div class="flex items-center gap-x-2">
                        <Label for="images">Upload Product Images </Label>
                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger> <FileQuestionIcon /> </TooltipTrigger>
                                <TooltipContent>
                                    <p>3 Files Max. it can be JPEG,PNG,JPG</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </div>
                    <Input multiple @change="handleImageChange($event)" for="images" type="file" placeholder="Product images" />
                    <InputError :message="formEdit.errors.product_images" />
                </div>
                <div class="mb-4 flex gap-x-2" v-if="previews.length">
                    <div v-for="(src, index) in previews" :key="index">
                        <img class="w-44 border object-cover" :src="src" alt="" />
                    </div>
                </div>
                <div class="mb-4 flex gap-x-2" v-else>
                    <div v-for="image in props.product.images as ProductImages[]" :key="image.id">
                        <img class="w-44 border object-cover" :src="'/storage/' + image.image_path" alt="" />
                    </div>
                </div>
                <div class="mb-4">
                    <Button type="submit">Save Changes</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
