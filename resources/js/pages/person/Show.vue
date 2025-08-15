<script setup lang="ts">
import MangasDisplay from '@/components/MangasDisplay.vue';
import PersonSkeleton from '@/components/skeletons/PersonSkeleton.vue';
import { Manga, Person } from '@/types';
import { Deferred, Head } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps<{ person?: Person & { mangas: Manga[] } }>();

const display = ref<'grid' | 'list'>('grid');
</script>

<template>
    <Deferred data="person">
        <template #fallback><PersonSkeleton :display="display" /> </template>
        <Head :title="`${person?.name} ${person?.surname}`" />
        <!--Cabecera-->
        <UCard variant="subtle" class="mt-20 overflow-visible rounded-xl sm:mt-16">
            <div class="flex flex-col gap-4 sm:flex-row">
                <!--Portada-->
                <div class="mx-auto -mt-16 w-32 sm:mx-0 sm:-mt-14">
                    <UIcon name="lucide:user" class="bg-elevated size-28 rounded-full p-2" />
                </div>
                <!--Datos-->
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center">
                    <p class="line-clamp-2 text-center text-3xl font-bold sm:text-start">{{ person?.name }} {{ person?.surname }}</p>
                    <p v-if="person?.kanjiName || person?.kanjiSurname" class="text-muted line-clamp-1 text-center text-2xl sm:text-start">
                        「 {{ person?.kanjiName }} {{ person?.kanjiSurname }} 」
                    </p>
                </div>
            </div>
        </UCard>
        <!--Mangas-->
        <MangasDisplay :mangas="person?.mangas" v-model:display="display" title />
    </Deferred>
</template>
