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
import { CircleAlert, FileQuestionIcon } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast, Toaster } from 'vue-sonner';
import 'vue-sonner/style.css';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: '/products',
    },
    {
        title: 'Create Products',
        href: '/products/create',
    },
];

type Categories = {
    id: number;
    name: string;
    slug?: string;
    description?: string;
};

const props = defineProps<{
    categories: Categories[];
}>();

const formCreate = useForm({
    name: '',
    category_id: '',
    price: '',
    stock: '',
    weight: '',
    description: '',
    product_images: null as File[] | null,
});
const previews = ref<string[]>([]);

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
        formCreate.product_images = files;

        // hapus preview lama
        previews.value.forEach((url) => URL.revokeObjectURL(url));
        previews.value = [];

        // buat preview baru
        files.forEach((file) => {
            previews.value.push(URL.createObjectURL(file));
        });
    }
}

function handleSubmit() {
    formCreate.post(route('products.store'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: (result: any) => {
            formCreate.reset();
            toast('Success!', {
                description: `${result.props.flash.message}`,
                action: {
                    label: 'Close',
                    onClick: () => console.log('Closed'),
                },
            });
        },
        onError: (errors) => {
            console.log('Validation errors:', errors);
            // Previews are preserved since product_images is not reset
        },
    });
}
</script>

<template>
    <Head title="Create Products" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Toaster />
        <div class="p-10">
            <div class="mb-8">
                <h3 class="text-2xl font-semibold tracking-wide">Form Create Product</h3>
                <p class="font-light">Create your own product with multiple images.</p>
            </div>
            <form @submit.prevent="handleSubmit()">
                <div class="mb-4">
                    <div class="grid grid-cols-2 gap-x-5">
                        <div class="space-y-2">
                            <Label for="product">Product Name</Label>
                            <Input v-model="formCreate.name" id="product" name="product" type="text" placeholder="Input product name here"></Input>
                            <InputError :message="formCreate.errors.name" />
                        </div>
                        <div class="space-y-2">
                            <Label for="category" name="category">Select category</Label>
                            <Select id="category" v-model="formCreate.category_id">
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
                            <InputError :message="formCreate.errors.category_id" />
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="grid grid-cols-2 gap-x-5">
                        <div class="space-y-2">
                            <Label for="price">Price</Label>
                            <Input v-model="formCreate.price" id="price" name="price" type="number" placeholder="000"></Input>
                            <InputError :message="formCreate.errors.price" />
                        </div>
                        <div class="space-y-2">
                            <Label for="stock" name="stock">Stock</Label>
                            <Input v-model="formCreate.stock" id="stock" name="stock" type="number" placeholder="0"></Input>
                            <InputError :message="formCreate.errors.stock" />
                        </div>
                    </div>
                </div>
                <div class="mb-4 space-y-2">
                    <Label for="weight">Weight (gram)</Label>
                    <Input v-model="formCreate.weight" type="number" placeholder="0 gram" id="weight" name="weight" />
                    <InputError :message="formCreate.errors.weight" />
                </div>
                <div class="mb-4 space-y-2">
                    <Label for="description">Description</Label>
                    <Textarea v-model="formCreate.description" id="description" name="description" placeholder="Describe your product"></Textarea>
                    <InputError :message="formCreate.errors.description" />
                </div>
                <div class="mb-4 space-y-2">
                    <div class="flex items-center gap-x-2">
                        <Label for="images">Upload Product Images</Label>
                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger> <FileQuestionIcon /> </TooltipTrigger>
                                <TooltipContent>
                                    <p>3 Files Max. it can be JPEG,PNG,JPG</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </div>

                    <Input @input="handleImageChange" for="images" type="file" name="product_images" placeholder="Product images" multiple />

                    <!-- preview gambar -->
                    <div class="mt-2 flex flex-wrap gap-4">
                        <div v-for="(src, index) in previews" :key="index" class="relative">
                            <img :src="src" class="w-44 rounded border object-cover" />
                        </div>
                    </div>

                    <!-- error validasi -->
                    <div v-if="Object.keys(formCreate.errors).some((key) => key.startsWith('product_images'))">
                        <InputError
                            v-for="(error, key) in Object.entries(formCreate.errors).filter(([key]) => key.startsWith('product_images'))"
                            :key="key"
                            :message="error[1]"
                        />
                    </div>
                </div>
                <div class="mb-4">
                    <Button type="submit" :disabled="formCreate.processing">Create Product</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
