<script setup lang="ts">
import MagazinesFilters from '@/components/magazines/MagazinesFilters.vue';
import GridSkeleton from '@/components/skeletons/GridSkeleton.vue';
import { useMagazinesStore } from '@/stores/magazinesStore';
import { Magazine, MagazineFilters } from '@/types';
import { Deferred, Head, router, WhenVisible } from '@inertiajs/vue3';
import { breakpointsTailwind, useBreakpoints, useDebounceFn } from '@vueuse/core';
import { computed, ref, watch } from 'vue';

const activeBreakpoint = useBreakpoints(breakpointsTailwind).active();

const props = defineProps<{
    pagination?: { currentPage: number; lastPage: number; total: number; data: Magazine[] };
    filtersData?: { publishers: string[] };
}>();

const loading = ref(false);
const store = useMagazinesStore();
const filters = computed(() => store.state);

const magazines = computed(() => props.pagination?.data ?? []);
const reachedEnd = computed(() => {
    if (!props.pagination) return true;
    return props.pagination.currentPage >= props.pagination.lastPage;
});

const showFilters = ref<string | undefined>(undefined);

const toggleShowFilters = () => {
    showFilters.value = showFilters.value === '0' ? undefined : '0';
};

watch(
    () => props.filtersData,
    (newFiltersData) => {
        if (newFiltersData) {
            store.filters = { ...newFiltersData };
        }
    },
    { deep: true },
);
const handleSearch = (filters: MagazineFilters) => {
    loading.value = true;
    router.reload({
        data: {
            ...filters,
            date: filters.date?.toString(),
        },
        reset: ['pagination'],
        preserveUrl: true,
        onSuccess: () => {
            loading.value = false;
        },
    });
};

const debouncedSearch = useDebounceFn(() => {
    handleSearch(store.state);
}, 300);
</script>

<template>
    <Head title="Revistas" />
    <div class="my-6 flex flex-col justify-between gap-4 sm:flex-row">
        <div class="flex gap-2">
            <!--Barra de búsqueda-->
            <UInput
                class="w-full sm:w-60 md:w-96"
                v-model="filters.search"
                placeholder="Buscar..."
                leading-icon="lucide:search"
                type="search"
                :ui="{ base: 'pr-8', trailing: 'pe-1' }"
                @input="debouncedSearch"
            >
                <template v-if="filters.search?.length" #trailing>
                    <UButton
                        color="neutral"
                        variant="link"
                        size="sm"
                        icon="i-lucide-circle-x"
                        aria-label="Clear input"
                        @click="
                            filters.search = '';
                            handleSearch(filters);
                        "
                    />
                </template>
            </UInput>
            <!--Botón de filtros-->
            <UButton
                variant="soft"
                color="neutral"
                trailing-icon="lucide:list-filter"
                :size="activeBreakpoint ? 'xl' : 'lg'"
                @click="toggleShowFilters"
                >{{ activeBreakpoint ? 'Filtros' : '' }}
            </UButton>
        </div>
        <h3>
            <span class="text-2xl font-semibold">{{ pagination?.total ?? 0 }}</span> revista{{ pagination?.total !== 1 ? 's' : '' }}
        </h3>
    </div>
    <!--Filtros-->
    <MagazinesFilters v-model:show-filters="showFilters" @filter="handleSearch" />
    <USeparator class="my-6" />
    <template v-if="loading">
        <GridSkeleton />
    </template>
    <template v-else>
        <Deferred data="pagination">
            <template #fallback>
                <GridSkeleton />
            </template>
            <!--Vista-->
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                <UCard
                    v-for="magazine of magazines"
                    :key="magazine.id"
                    variant="subtle"
                    class="hover:bg-accented cursor-pointer shadow-md"
                    @click="router.visit(route('magazines.show', magazine.id))"
                >
                    <div class="relative mt-4 flex aspect-[1/1.4142] flex-col items-center">
                        <UIcon name="lucide:book-image" class="bg-accented my-3 size-20 rounded-lg p-2" />
                        <p class="line-clamp-2 text-center font-semibold">{{ magazine.name }}</p>
                        <p class="line-clamp-2 text-center font-light">{{ magazine.publisher }}</p>
                    </div>
                </UCard>
            </div>
            <div v-if="magazines?.length === 0" class="text-muted text-center text-xl">No hay resultados</div>
        </Deferred>
        <!--Scroll infinito-->
        <WhenVisible
            v-if="!reachedEnd"
            :params="{
                only: ['pagination'],
                data: {
                    page: (pagination?.currentPage || 1) + 1,
                    filters,
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
</template>
