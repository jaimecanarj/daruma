<script setup lang="ts">
import { Chapter, Volume } from '@/types';
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core';
import { computed } from 'vue';

const props = defineProps<{ volumes?: Volume[]; chapters?: Chapter[] }>();

const volumeList = computed(() => {
    if (!props.volumes) return [];
    return props.volumes.map((volume) => ({
        ...volume,
        chapters: props.chapters ? props.chapters.filter((chapter) => chapter.volumeOrder === volume.order).sort((a, b) => a.order - b.order) : [],
    }));
});

const volumeContent = computed(() => {
    return useBreakpoints(breakpointsTailwind).greater('lg').value
        ? { align: 'start' as const, side: 'right' as const }
        : { align: 'center' as const, side: 'bottom' as const };
});
</script>

<template>
    <div class="mt-6 grid grid-cols-3 gap-4 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7">
        <UPopover v-for="volume in volumeList" :key="volume.order" :content="volumeContent" arrow>
            <div class="group relative aspect-[1/1.4142] cursor-pointer rounded-md shadow-md">
                <!--Imagen-->
                <img :src="`/storage/${volume.cover}`" class="size-full rounded-md object-cover select-none" alt="Portada" />
                <!--Efecto hover-->
                <div
                    class="absolute inset-0 flex flex-col items-center justify-center bg-black/70 p-3 opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                >
                    <h3 class="line-clamp-2 text-center text-base font-bold text-white/90 select-none sm:text-lg">{{ volume.name }}</h3>
                </div>
            </div>
            <template #content>
                <div class="w-80 p-4 sm:w-96">
                    <div class="flex items-center justify-between gap-4">
                        <h3 class="text-lg font-semibold sm:text-xl">{{ volume.name }}</h3>
                        <div class="text-muted flex items-center gap-1 text-sm">
                            <UIcon name="lucide:sticky-note" />
                            <p>{{ volume.pages }} págs</p>
                        </div>
                    </div>
                    <USeparator class="my-2" />
                    <div v-if="volume.chapters.length > 0" class="h-60 overflow-scroll">
                        <div v-for="chapter of volume.chapters" :key="chapter.order" class="flex items-center gap-1">
                            <UIcon name="lucide:circle-small" class="size-6 flex-shrink-0" />
                            <p class="line-clamp-1">{{ chapter.name }}</p>
                        </div>
                    </div>
                </div>
            </template>
        </UPopover>
    </div>
</template>
