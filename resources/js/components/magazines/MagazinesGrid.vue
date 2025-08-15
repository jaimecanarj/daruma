<script setup lang="ts">
import { Magazine } from '@/types';

defineProps<{ magazines?: Magazine[] }>();
</script>

<template>
    <div class="grid grid-cols-3 gap-4 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7">
        <ULink v-for="magazine of magazines" :key="magazine.id" :to="`/magazines/${magazine.id}`">
            <UCard variant="subtle" class="hover:bg-accented cursor-pointer shadow-md select-none">
                <div class="relative flex aspect-[1/1.4142] flex-col items-center justify-center pb-6">
                    <div class="flex w-full flex-col items-center">
                        <UIcon name="lucide:book-image" class="bg-accented my-3 size-16 rounded-lg p-2 sm:size-20" />
                        <p class="line-clamp-2 text-center text-sm leading-tight font-semibold sm:h-[2.5rem] sm:text-base">
                            {{ magazine.name }}
                        </p>
                    </div>
                    <div class="absolute bottom-0">
                        <p class="line-clamp-1 text-center text-xs font-light lg:text-sm">{{ magazine.publisher }}</p>
                    </div>
                </div>
            </UCard>
        </ULink>
    </div>
</template>
