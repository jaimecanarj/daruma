<script setup lang="ts">
import FiltersHeader from '@/components/FiltersHeader.vue';
import InfiniteScroll from '@/components/InfiniteScroll.vue';
import MagazinesFilters from '@/components/magazines/MagazinesFilters.vue';
import GridSkeleton from '@/components/skeletons/GridSkeleton.vue';
import { Magazine, MagazineFilters } from '@/types';
import { Deferred, Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    paginatedResults?: { currentPage: number; lastPage: number; total: number; data: Magazine[] };
    filterOptions?: { publishers: string[] };
}>();

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
    <FiltersHeader v-model="filters.search" filters class="mt-6 flex flex-col justify-between gap-4 sm:flex-row" @search="handleSearch">
        <template #rightSide>
            <h3>
                <span class="text-2xl font-semibold">{{ paginatedResults?.total ?? 0 }}</span> revista{{ paginatedResults?.total !== 1 ? 's' : '' }}
            </h3>
        </template>
        <MagazinesFilters v-model="filters" :filter-options="filterOptions" @filter="handleSearch" />
    </FiltersHeader>
    <USeparator class="my-6" />
    <!--Contenido-->
    <template v-if="loading">
        <GridSkeleton />
    </template>
    <template v-else>
        <Deferred data="paginatedResults">
            <template #fallback>
                <GridSkeleton />
            </template>
            <!--Tarjetas-->
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                <UCard
                    v-for="magazine of magazines"
                    :key="magazine.id"
                    variant="subtle"
                    class="hover:bg-accented cursor-pointer shadow-md"
                    @click="router.visit(route('magazines.show', magazine.id))"
                >
                    <div class="relative flex aspect-[1/1.4142] flex-col items-center justify-center pb-6">
                        <div class="flex w-full flex-col items-center">
                            <UIcon name="lucide:book-image" class="bg-accented my-3 size-20 rounded-lg p-2" />
                            <p class="1. line-clamp-2 h-[2.5rem] text-center leading-tight font-semibold">{{ magazine.name }}</p>
                        </div>
                        <div class="absolute bottom-0">
                            <p class="line-clamp-1 text-center font-light">{{ magazine.publisher }}</p>
                        </div>
                    </div>
                </UCard>
            </div>
            <div v-if="magazines?.length === 0" class="text-muted text-center text-xl">No hay resultados</div>
        </Deferred>
        <!--Scroll infinito-->
        <InfiniteScroll :current-page="paginatedResults?.currentPage" :last-page="paginatedResults?.lastPage" :filters="filters" />
    </template>
</template>
