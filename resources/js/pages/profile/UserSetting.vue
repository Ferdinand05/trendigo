<script lang="ts" setup>
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import Footer from '@/components/Footer.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import Navbar from '@/components/Navbar.vue';
import ProfileUserLayout from '@/components/ProfileUserLayout.vue';
import { Head } from '@inertiajs/vue3';
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
                <HeadingSmall title="Pengaturan" description="Atur tampilan yang anda suka"></HeadingSmall>
            </div>
            <AppearanceTabs />
        </ProfileUserLayout>
    </section>

    <div class="mt-28">
        <Footer />
    </div>
</template>
