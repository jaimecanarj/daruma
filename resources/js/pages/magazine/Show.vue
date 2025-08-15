<script setup lang="ts">
import MangasDisplay from '@/components/MangasDisplay.vue';
import MagazineSkeleton from '@/components/skeletons/MagazineSkeleton.vue';
import { Magazine, Manga } from '@/types';
import { frequencies } from '@/utils/constants';
import { Deferred, Head } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps<{ magazine?: Magazine & { mangas: Manga[] } }>();

const display = ref<'grid' | 'list'>('grid');
</script>

<template>
    <Deferred data="magazine">
        <template #fallback><MagazineSkeleton :display="display" /> </template>
        <Head :title="magazine?.name" />
        <!--Cabecera-->
        <UCard variant="subtle" class="mt-20 overflow-visible rounded-xl sm:mt-16">
            <div class="flex flex-col gap-4 sm:flex-row">
                <!--Portada-->
                <div class="mx-auto -mt-16 w-32 sm:mx-0 sm:-mt-14">
                    <UIcon name="lucide:book-image" class="bg-elevated size-28 rounded-lg p-2" />
                </div>
                <!--Datos-->
                <div class="flex flex-col justify-center sm:justify-start">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center">
                        <p class="line-clamp-2 text-center text-3xl font-bold sm:text-start">{{ magazine?.name }}</p>
                        <p class="text-muted line-clamp-1 text-center text-2xl sm:text-start">「 {{ magazine?.publisher }} 」</p>
                    </div>
                    <div class="mt-6 flex flex-col items-center gap-2 sm:flex-row sm:gap-4">
                        <div class="flex items-center gap-1">
                            <UIcon name="lucide:square-user-round" class="size-5" />
                            <p class="capitalize">{{ magazine?.demography }}</p>
                        </div>
                        <div v-if="magazine?.date" class="flex items-center gap-1">
                            <UIcon name="lucide:calendar" class="size-5" />
                            <p>
                                {{
                                    new Intl.DateTimeFormat('es-ES', {
                                        day: 'numeric',
                                        month: 'short',
                                        year: 'numeric',
                                    }).format(new Date(magazine.date))
                                }}
                            </p>
                        </div>
                        <div class="flex items-center gap-1">
                            <UIcon name="lucide:calendar-1" class="size-5" />
                            <p>{{ frequencies.find((frequency) => frequency.value === magazine?.frequency)?.label }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </UCard>
        <!--Mangas-->
        <MangasDisplay :mangas="magazine?.mangas" v-model:display="display" title />
    </Deferred>
</template>
