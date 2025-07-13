<script setup lang="ts">
import { Manga } from '@/types';

defineProps<{ manga: Manga }>();
</script>

<template>
    {{ manga }}
</template>
