<script setup lang="ts">
defineProps<{ display: 'grid' | 'list' }>();
</script>

<template>
    <div v-if="display === 'grid'" class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
        <div v-for="manga in 20" :key="manga" class="relative aspect-[1/1.4142] rounded-md shadow-md">
            <USkeleton class="h-full w-full" />
        </div>
    </div>
    <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <UCard v-for="manga in 20" :key="manga" variant="subtle" :ui="{ body: 'p-2 sm:p-3' }">
            <div class="flex gap-2">
                <div class="max-w-[150px] basis-1/4">
                    <USkeleton class="aspect-[1/1.4142] rounded-sm" />
                </div>
                <div class="flex min-w-0 basis-3/4 flex-col gap-2 px-1">
                    <USkeleton class="h-6 w-full" />
                    <USkeleton class="h-4 w-full" />
                    <USkeleton class="h-4 w-full" />
                    <USkeleton class="h-36 w-full" />
                </div>
            </div>
        </UCard>
    </div>
</template>
