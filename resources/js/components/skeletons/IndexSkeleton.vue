<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{ display: 'grid' | 'list'; size: 'sm' | 'lg' }>();

const height = computed(() => (props.size === 'sm' ? 'h-28' : 'h-60'));
</script>

<template>
    <!--Skeleton de cuadrícula-->
    <div v-if="display === 'grid'" class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
        <USkeleton v-for="item in 20" :key="item" class="relative aspect-[1/1.4142] h-full w-full rounded-md shadow-md" />
    </div>
    <!--Skeleton de tarjetas-->
    <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <USkeleton v-for="item in 20" :key="item" :class="[height, 'w-full']" />
    </div>
</template>
