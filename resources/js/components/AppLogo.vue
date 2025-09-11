<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from './AppLogoIcon.vue';
</script>

<template>
    <div class="flex aspect-square size-8 items-center justify-center rounded-md">
        <AppLogoIcon class="size-12 object-cover" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <Link href="/"> <span class="mb-0.5 truncate leading-tight font-semibold uppercase">Trendigo Dashboard</span> </Link>
    </div>
</template>
