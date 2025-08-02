<script setup lang="ts">
import FiltersHeader from '@/components/FiltersHeader.vue';
import InfiniteScroll from '@/components/InfiniteScroll.vue';
import GridSkeleton from '@/components/skeletons/GridSkeleton.vue';
import { Person } from '@/types';
import { Deferred, Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    paginatedResults?: { currentPage: number; lastPage: number; total: number; data: Person[] };
}>();

const searchInput = ref('');
const loading = ref(false);

const people = computed(() => props.paginatedResults?.data ?? []);

const handleSearch = () => {
    loading.value = true;
    router.reload({
        data: {
            search: searchInput.value,
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
    <FiltersHeader v-model="searchInput" class="my-6 flex flex-col justify-between gap-4 sm:flex-row" @search="handleSearch">
        <template #rightSide>
            <h3>
                <span class="text-2xl font-semibold">{{ paginatedResults?.total ?? 0 }}</span> persona{{ paginatedResults?.total !== 1 ? 's' : '' }}
            </h3>
        </template>
    </FiltersHeader>
    <USeparator class="my-6" />
    <template v-if="loading">
        <GridSkeleton />
    </template>
    <template v-else>
        <Deferred data="paginatedResults">
            <template #fallback>
                <GridSkeleton />
            </template>
            <!--Vista-->
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                <UCard
                    v-for="person of people"
                    :key="person.id"
                    variant="subtle"
                    class="hover:bg-accented cursor-pointer shadow-md"
                    @click="router.visit(route('people.show', person.id))"
                >
                    <div class="relative mt-4 flex aspect-[1/1.4142] flex-col items-center">
                        <UIcon name="lucide:user" class="bg-accented my-3 size-20 rounded-full" />
                        <p class="line-clamp-2 text-center font-semibold">{{ person.name }} {{ person.surname }}</p>
                        <p class="line-clamp-2 text-center">{{ person.kanjiName }} {{ person.kanjiSurname }}</p>
                    </div>
                </UCard>
            </div>
            <div v-if="people?.length === 0" class="text-muted text-center text-xl">No hay resultados</div>
        </Deferred>
        <!--Scroll infinito-->
        <InfiniteScroll :current-page="paginatedResults?.currentPage" :last-page="paginatedResults?.lastPage" :filters="{ search: searchInput }" />
    </template>
</template>
