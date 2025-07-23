<script setup lang="ts">
import MangasCard from '@/components/mangas/MangasCard.vue';
import MangasGrid from '@/components/mangas/MangasGrid.vue';
import MangasHeader from '@/components/mangas/MangasHeader.vue';
import MangaIndexSkeleton from '@/components/skeletons/MangaIndexSkeleton.vue';
import { useMangasStore } from '@/stores/mangasStore';
import { Magazine, Manga, MangaFilters, Person, Tag } from '@/types';
import { Deferred, Head, router, WhenVisible } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps<{
    pagination?: { currentPage: number; lastPage: number; total: number; data: Manga[] };
    filtersData?: { tags: Tag[]; people: Person[]; magazines: Magazine[] };
}>();

const display = ref<'grid' | 'list'>('grid');
const loading = ref(false);
const store = useMangasStore();
const localFilters = computed(() => store.state);

const mangas = computed(() => props.pagination?.data);
const reachedEnd = computed(() => {
    if (!props.pagination) return true;
    return props.pagination.currentPage >= props.pagination.lastPage;
});

watch(
    () => props.filtersData,
    (newFiltersData) => {
        if (newFiltersData) {
            store.filters = { ...newFiltersData };
        }
    },
    { deep: true },
);

const handleSearch = (filters: MangaFilters) => {
    router.reload({
        data: {
            ...filters,
            date: filters.date?.toString(),
            magazines: filters.magazines?.map((magazine) => magazine.value),
            people: filters.people?.map((person) => person.value),
        },
        reset: ['pagination'],
        preserveUrl: true,
        onSuccess: () => {
            loading.value = false;
        },
    });
};
</script>

<template>
    <Head title="Mangas" />
    <MangasHeader v-model:loading="loading" v-model:display="display" @handle-search="handleSearch" />
    <USeparator class="my-6" />
    <template v-if="loading">
        <MangaIndexSkeleton :display="display" />
    </template>
    <template v-else>
        <Deferred data="pagination">
            <template #fallback>
                <MangaIndexSkeleton :display="display" />
            </template>
            <!--Vistas-->
            <MangasGrid v-if="display === 'grid'" :mangas="mangas" />
            <MangasCard v-else :mangas="mangas" />
            <div v-if="mangas?.length === 0" class="text-muted text-center text-xl">No hay resultados</div>
        </Deferred>
        <!--Scroll infinito-->
        <WhenVisible
            v-if="!reachedEnd"
            :params="{
                only: ['pagination'],
                data: {
                    page: (pagination?.currentPage || 1) + 1,
                    filters: localFilters,
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
