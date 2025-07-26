<script setup lang="ts">
import { Magazine } from '@/types';
import { Head } from '@inertiajs/vue3';

defineProps<{ magazine: Magazine }>();
</script>

<template>
    <Head :title="magazine.name" />
    {{ magazine }}
</template>
