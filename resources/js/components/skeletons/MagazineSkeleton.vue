<script setup lang="ts">
import MangasSkeleton from '@/components/skeletons/MangasSkeleton.vue';

defineProps<{ display: 'grid' | 'list' }>();
</script>

<template>
    <div class="absolute top-22 left-1/2 -translate-x-1/2 sm:left-auto sm:ml-8 sm:translate-0">
        <USkeleton class="size-28 rounded-lg p-2" />
    </div>
    <UCard variant="subtle" class="mt-14 flex flex-col justify-center rounded-xl sm:justify-start">
        <div class="mt-20 flex flex-col gap-4 sm:mt-0 sm:ml-40 lg:flex-row lg:items-center">
            <USkeleton class="mx-auto h-9 w-60 sm:mx-0" />
            <USkeleton class="mx-auto h-8 w-40 sm:mx-0" />
        </div>
        <div class="mt-6 flex flex-col items-center gap-2 sm:ml-40 sm:flex-row sm:gap-4">
            <USkeleton class="h-6 w-24" />
            <USkeleton class="h-6 w-24" />
            <USkeleton class="h-6 w-24" />
        </div>
    </UCard>
    <div class="mt-8 flex flex-col justify-between gap-4 sm:flex-row">
        <USkeleton class="h-9 w-32" />
        <div class="flex items-center gap-4">
            <USkeleton class="h-8 w-48" />
            <USkeleton class="ml-auto h-9 w-20" />
        </div>
    </div>
    <USeparator class="my-5" />
    <MangasSkeleton :display="display" />
</template>
