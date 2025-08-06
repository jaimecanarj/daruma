<script setup lang="ts">
import IndexSkeleton from '@/components/skeletons/IndexSkeleton.vue';

defineProps<{ display: 'grid' | 'list' }>();
</script>

<template>
    <div class="absolute top-22 left-1/2 -translate-x-1/2 sm:left-auto sm:ml-8 sm:translate-0">
        <USkeleton class="size-28 rounded-full p-2" />
    </div>
    <UCard variant="subtle" class="mt-14 flex justify-center rounded-xl sm:justify-start">
        <div class="mt-20 flex flex-col gap-4 sm:mt-0 sm:ml-40 lg:flex-row lg:items-center">
            <USkeleton class="mx-auto h-9 w-60 sm:mx-0" />
            <USkeleton class="mx-auto h-8 w-40 sm:mx-0" />
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
    <IndexSkeleton :display="display" size="lg" />
</template>
