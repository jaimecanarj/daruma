<script setup lang="ts">
import { WhenVisible } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{ currentPage?: number; lastPage?: number; filters?: any }>();

const reachedEnd = computed(() => {
    if (!props.currentPage || !props.lastPage) return true;
    return props.currentPage >= props.lastPage;
});
</script>

<template>
    <WhenVisible
        v-if="!reachedEnd"
        :params="{
            only: ['paginatedResults'],
            data: {
                page: (currentPage || 1) + 1,
                ...filters,
            },
            preserveUrl: true,
        }"
        always
        :buffer="500"
    >
        <template #fallback>
            <div class="my-10 flex items-center justify-center space-x-2">
                <div class="bg-inverted/80 size-6 animate-bounce rounded-full [animation-delay:-0.3s]" />
                <div class="bg-inverted/80 size-6 animate-bounce rounded-full [animation-delay:-0.15s]" />
                <div class="bg-inverted/80 size-6 animate-bounce rounded-full" />
            </div>
        </template>
    </WhenVisible>
</template>
