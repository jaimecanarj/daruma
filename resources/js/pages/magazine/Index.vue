<script setup lang="ts">
import DisplaySelector from '@/components/DisplaySelector.vue';
import FiltersHeader from '@/components/FiltersHeader.vue';
import InfiniteScroll from '@/components/InfiniteScroll.vue';
import MagazinesCard from '@/components/magazines/MagazinesCard.vue';
import MagazinesFilters from '@/components/magazines/MagazinesFilters.vue';
import MagazinesGrid from '@/components/magazines/MagazinesGrid.vue';
import IndexSkeleton from '@/components/skeletons/IndexSkeleton.vue';
import { Magazine, MagazineFilters } from '@/types';
import { Deferred, Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    paginatedResults?: { currentPage: number; lastPage: number; total: number; data: Magazine[] };
    filterOptions?: { publishers: string[] };
}>();

const display = ref<'grid' | 'list'>('grid');
const loading = ref(false);
const filters = ref<MagazineFilters>({
    search: '',
    publishers: [],
    date: undefined,
    demographies: [],
    frequencies: [],
    order: 'nameAsc',
});

const magazines = computed(() => props.paginatedResults?.data ?? []);
const magazinesIds = computed(() => magazines.value.map((magazine) => magazine.id));

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
    <Head title="Revistas" />
    <!--Header-->
    <FiltersHeader
        v-model="filters.search"
        filters
        class="mt-6 flex flex-col justify-between gap-4 sm:flex-row"
        @search="handleSearch"
        :items-ids="magazinesIds"
        items-type="magazine"
    >
        <template #rightSide>
            <div class="ml-1 flex items-center gap-4">
                <!--Total de personas-->
                <h3>
                    <span class="text-2xl font-semibold">{{ paginatedResults?.total ?? 0 }}</span> revista{{
                        paginatedResults?.total !== 1 ? 's' : ''
                    }}
                </h3>
                <!--Selector de display-->
                <DisplaySelector v-model="display" />
            </div>
        </template>
        <MagazinesFilters v-model="filters" :filter-options="filterOptions" @filter="handleSearch" />
    </FiltersHeader>
    <USeparator class="my-6" />
    <!--Contenido-->
    <template v-if="loading">
        <IndexSkeleton :display="display" size="sm" />
    </template>
    <template v-else>
        <Deferred data="paginatedResults">
            <template #fallback>
                <IndexSkeleton :display="display" size="sm" />
            </template>
            <!--Tarjetas-->
            <MagazinesGrid v-if="display === 'grid'" :magazines="magazines" />
            <MagazinesCard v-else :magazines="magazines" />
            <div v-if="magazines?.length === 0" class="text-muted text-center text-xl">No hay resultados</div>
        </Deferred>
        <!--Scroll infinito-->
        <InfiniteScroll :current-page="paginatedResults?.currentPage" :last-page="paginatedResults?.lastPage" :filters="filters" />
    </template>
</template>
