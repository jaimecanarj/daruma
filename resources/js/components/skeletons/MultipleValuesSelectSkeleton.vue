<script setup lang="ts">
defineProps<{ types?: boolean }>();
</script>

<template>
    <div class="flex items-center gap-2">
        <div class="flex flex-1 flex-col gap-2 md:flex-row">
            <USelect disabled class="w-full" />
            <USelect v-if="types" class="min-w-48" disabled />
        </div>
        <UButton icon="lucide:plus" color="neutral" class="size-12 justify-center md:size-auto" disabled />
    </div>
</template>
