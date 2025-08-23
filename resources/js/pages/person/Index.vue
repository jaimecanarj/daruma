<script setup lang="ts">
import DisplaySelector from '@/components/DisplaySelector.vue';
import FiltersHeader from '@/components/FiltersHeader.vue';
import InfiniteScroll from '@/components/InfiniteScroll.vue';
import PeopleCard from '@/components/people/PeopleCard.vue';
import PeopleGrid from '@/components/people/PeopleGrid.vue';
import IndexSkeleton from '@/components/skeletons/IndexSkeleton.vue';
import { Person } from '@/types';
import { Deferred, Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    paginatedResults?: { currentPage: number; lastPage: number; total: number; data: Person[] };
}>();

const display = ref<'grid' | 'list'>('grid');
const filters = ref({ search: '' });
const loading = ref(false);

const people = computed(() => props.paginatedResults?.data ?? []);
const peopleIds = computed(() => people.value.map((person) => person.id));

const handleSearch = () => {
    loading.value = true;
    router.reload({
        data: {
            ...filters.value,
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
    <Head title="Personas" />
    <!--Header-->
    <FiltersHeader
        v-model="filters.search"
        class="mt-6 flex flex-col justify-between gap-4 sm:flex-row"
        @search="handleSearch"
        :items-ids="peopleIds"
        items-type="person"
    >
        <template #rightSide>
            <div class="ml-1 flex items-center gap-4">
                <!--Total de personas-->
                <h3>
                    <span class="text-2xl font-semibold">{{ paginatedResults?.total ?? 0 }}</span> persona{{
                        paginatedResults?.total !== 1 ? 's' : ''
                    }}
                </h3>
                <!--Selector de display-->
                <DisplaySelector v-model="display" />
            </div>
        </template>
    </FiltersHeader>
    <USeparator class="my-6" />
    <template v-if="loading">
        <IndexSkeleton :display="display" size="sm" />
    </template>
    <template v-else>
        <Deferred data="paginatedResults">
            <template #fallback>
                <IndexSkeleton :display="display" size="sm" />
            </template>
            <!--Vista-->
            <PeopleGrid v-if="display === 'grid'" :people="people" />
            <PeopleCard v-else :people="people" />
            <div v-if="people?.length === 0" class="text-muted text-center text-xl">No hay resultados</div>
        </Deferred>
        <!--Scroll infinito-->
        <InfiniteScroll :current-page="paginatedResults?.currentPage" :last-page="paginatedResults?.lastPage" :filters="filters" />
    </template>
</template>
