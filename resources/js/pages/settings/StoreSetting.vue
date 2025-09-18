<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import { type BreadcrumbItem } from '@/types';

import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Store settings',
        href: '/settings/store',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Store settings" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Store settings" description="Customize your store" />
                <div>
                    <form action="" method="post" class="space-y-3">
                        <div class="flex w-full justify-around gap-3">
                            <div class="w-full space-y-1">
                                <Label>Name</Label>
                                <Input placeholder="Trendigo" />
                            </div>
                            <div class="w-full space-y-1">
                                <Label>Email</Label>
                                <Input type="email" placeholder="trendigo@store.com" />
                            </div>
                        </div>
                        <div class="space-y-1">
                            <Label>Sub Heading</Label>
                            <Input placeholder="Temukan Tren, Ikuti Gaya" />
                        </div>
                        <div class="space-y-1">
                            <Label>Description</Label>
                            <Textarea placeholder="Toko online tervaforit nomor #1"></Textarea>
                        </div>
                        <div class="space-y-1">
                            <Button type="submit">Save</Button>
                        </div>
                    </form>
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
