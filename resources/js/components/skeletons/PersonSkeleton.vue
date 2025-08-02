<script setup lang="ts">
import MangasSkeleton from '@/components/skeletons/MangasSkeleton.vue';

defineProps<{ display: 'grid' | 'list' }>();
</script>

<template>
    <div class="absolute top-22 left-1/2 -translate-x-1/2 sm:left-auto sm:ml-8 sm:translate-0">
        <USkeleton class="size-28 rounded-full p-2" />
    </div>
    <UCard variant="subtle" class="mt-14 flex justify-center rounded-xl sm:justify-start">
        <div class="mt-20 flex flex-col gap-4 sm:mt-0 sm:ml-40 lg:flex-row lg:items-center">
            <USkeleton class="h-9 w-60" />
            <USkeleton class="h-8 w-40" />
        </div>
    </UCard>
    <div class="mt-8">
        <USkeleton class="h-9 w-32" />
    </div>
    <USeparator class="my-5" />
    <MangasSkeleton :display="display" />
</template>
