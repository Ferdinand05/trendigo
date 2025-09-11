<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();

const mode = localStorage.getItem('appearance');
</script>

<template>
    <div :class="className" v-bind="$attrs">
        <img :src="mode == 'dark' ? '/img/trendigo-logo-dark.svg' : '/img/trendigo-logo.svg'" class="h-full w-full object-cover" />
    </div>
</template>
