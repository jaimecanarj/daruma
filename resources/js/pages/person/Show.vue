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
        <div class="absolute top-22 left-1/2 -translate-x-1/2 sm:left-auto sm:ml-8 sm:translate-0">
            <UIcon name="lucide:user" class="bg-elevated size-28 rounded-full p-2" />
        </div>
        <UCard variant="subtle" class="mt-14 flex justify-center rounded-xl sm:justify-start">
            <div class="mt-20 flex flex-col gap-4 sm:mt-0 sm:ml-40 lg:flex-row lg:items-center">
                <p class="line-clamp-2 text-center text-3xl font-bold sm:text-start">{{ person?.name }} {{ person?.surname }}</p>
                <p v-if="person?.kanjiName || person?.kanjiSurname" class="text-muted line-clamp-1 text-center text-2xl sm:text-start">
                    「 {{ person?.kanjiName }} {{ person?.kanjiSurname }} 」
                </p>
            </div>
        </UCard>
        <!--Mangas-->
        <MangasDisplay :mangas="person?.mangas" :display="display" title />
    </Deferred>
</template>
