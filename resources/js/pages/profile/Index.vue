<script lang="ts" setup>
import Footer from '@/components/Footer.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import Navbar from '@/components/Navbar.vue';
import ProfileUserLayout from '@/components/ProfileUserLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import { User } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = page.props.auth.user as User;

const form = useForm({
    name: user.name,
    email: user.email,
});

function submit() {
    form.patch(route('profile.user.save'), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Profile User" />
    <Navbar />
    <div class="w-full">
        <img src="/img/pattern/waves1.svg" alt="" class="absolute w-full" />
    </div>
    <section class="mx-auto max-w-7xl px-8 pt-20 md:pt-32">
        <ProfileUserLayout>
            <div>
                <HeadingSmall title="Profil" description="Ubah email dan username"></HeadingSmall>
            </div>
            <div>
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input id="name" class="mt-1 block w-full" v-model="form.name" required autocomplete="name" placeholder="Full name" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="Email address"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>
                    <div class="my-2 text-sm text-muted-foreground">Anda bergabung pada {{ new Date(page.props.auth.user.created_at) }}</div>
                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">Save</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
                        </Transition>
                    </div>
                </form>
            </div>
        </ProfileUserLayout>
    </section>

    <div class="mt-20">
        <Footer />
    </div>
</template>
