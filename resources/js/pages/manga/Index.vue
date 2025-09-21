<script setup lang="ts">
import DisplaySelector from '@/components/DisplaySelector.vue';
import FiltersHeader from '@/components/FiltersHeader.vue';
import InfiniteScroll from '@/components/InfiniteScroll.vue';
import MangasCard from '@/components/mangas/MangasCard.vue';
import MangasFilters from '@/components/mangas/MangasFilters.vue';
import MangasGrid from '@/components/mangas/MangasGrid.vue';
import IndexSkeleton from '@/components/skeletons/IndexSkeleton.vue';
import { Magazine, Manga, MangaFilters, Person, Tag } from '@/types';
import { Deferred, Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    paginatedResults?: { currentPage: number; lastPage: number; total: number; data: Manga[]; mangasIds: number[] };
    filterOptions?: { tags: Tag[]; people: Person[]; magazines: Magazine[] };
}>();

const display = ref<'grid' | 'list'>('grid');
const loading = ref(false);
const filters = ref<MangaFilters>({
    search: '',
    volumes: undefined,
    date: undefined,
    tags: { include: [], exclude: [] },
    order: 'updateDesc',
    people: [],
    language: undefined,
    magazines: [],
    demographies: [],
    finished: [],
    readingDirection: [],
});

const mangas = computed(() => props.paginatedResults?.data ?? []);

const handleSearch = () => {
    loading.value = true;
    router.reload({
        data: {
            ...filters.value,
            date: filters.value.date?.toString(),
        },
        reset: ['paginatedResults'],
        preserveUrl: true,
        onSuccess: () => {
            loading.value = false;
        },
    });
};
</script>

<template>
    <Head title="Mangas" />
    <!--Header-->
    <FiltersHeader
        v-model="filters.search"
        filters
        class="mt-6 flex flex-col justify-between gap-4 sm:flex-row"
        random
        :items-ids="paginatedResults?.mangasIds"
        items-type="manga"
        @search="handleSearch"
    >
        <template #rightSide>
            <div class="ml-1 flex items-center gap-4">
                <!--Total de mangas-->
                <h3>
                    <span class="text-2xl font-semibold">{{ paginatedResults?.total ?? 0 }}</span> manga{{ paginatedResults?.total !== 1 ? 's' : '' }}
                </h3>
                <!--Selector de display-->
                <DisplaySelector v-model="display" />
            </div>
        </template>
        <MangasFilters v-model="filters" :filter-options="filterOptions" @filter="handleSearch" />
    </FiltersHeader>
    <USeparator class="my-6" />
    <template v-if="loading">
        <IndexSkeleton :display="display" size="lg" />
    </template>
    <template v-else>
        <Deferred data="paginatedResults">
            <template #fallback>
                <IndexSkeleton :display="display" size="lg" />
            </template>
            <!--Vistas-->
            <MangasGrid v-if="display === 'grid'" :mangas="mangas" />
            <MangasCard v-else :mangas="mangas" />
            <div v-if="mangas?.length === 0" class="text-muted text-center text-xl">No hay resultados</div>
        </Deferred>
        <!--Scroll infinito-->
        <InfiniteScroll :current-page="paginatedResults?.currentPage" :last-page="paginatedResults?.lastPage" :filters="filters" />
    </template>
</template>
