<script lang="ts" setup>
import { Link, usePage } from '@inertiajs/vue3';
import { MailWarning } from 'lucide-vue-next';
import Heading from './Heading.vue';
import Alert from './ui/alert/Alert.vue';
import AlertDescription from './ui/alert/AlertDescription.vue';
import AlertTitle from './ui/alert/AlertTitle.vue';
import Button from './ui/button/Button.vue';
import Separator from './ui/separator/Separator.vue';
const page = usePage();
</script>

<template>
    <div>
        <Heading title="Profil anda" description="Atur profil dan pesanan anda" />
    </div>
    <div class="max-w-2xl">
        <Alert v-show="page.props.auth.user.email_verified_at == null" variant="destructive">
            <MailWarning class="h-4 w-4" />
            <AlertTitle>Oops!</AlertTitle>
            <AlertDescription class="flex gap-x-2">
                <div>Verifikasi email anda untuk menerima notifikasi.</div>
                <a href="" target="_blank" class="text-blue-500 hover:underline">Klik disini!</a>
            </AlertDescription>
        </Alert>
    </div>
    <div class="mt-7 flex flex-col lg:flex-row lg:space-x-12">
        <aside class="w-full max-w-xl lg:w-48">
            <nav class="flex flex-col space-y-1 space-x-0">
                <Button variant="ghost" class="w-full justify-start hover:cursor-pointer">
                    <Link :href="route('profile.show.order')" prefetch="hover" class="w-full text-left">Pesanan</Link>
                </Button>
                <Button variant="ghost" class="w-full justify-start hover:cursor-pointer">
                    <Link :href="route('profile.user.index')" class="w-full text-left" prefetch="hover">Profil</Link>
                </Button>
                <Button variant="ghost" class="w-full justify-start hover:cursor-pointer">
                    <Link :href="route('profile.user.password')" prefetch="hover" class="w-full text-left">Password</Link>
                </Button>
                <Button variant="ghost" class="w-full justify-start hover:cursor-pointer">
                    <Link :href="route('profile.show.setting')" class="w-full text-left" prefetch="hover">Pengaturan</Link>
                </Button>
            </nav>
        </aside>
        <Separator class="my-6 lg:hidden" />

        <div class="flex-1 md:max-w-4xl">
            <section class="space-y-8">
                <slot />
            </section>
        </div>
    </div>
</template>
