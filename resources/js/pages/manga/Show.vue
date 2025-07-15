<script setup lang="ts">
import { Manga } from '@/types';
import { Head } from '@inertiajs/vue3';

defineProps<{ manga: Manga }>();
</script>

<template>
    <Head :title="manga.name" />
    {{ manga }}
</template>
