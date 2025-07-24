<script setup lang="ts">
import PersonIndexSkeleton from '@/components/skeletons/PersonIndexSkeleton.vue';
import { Person } from '@/types';
import { Deferred, Head, router, WhenVisible } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { computed, ref } from 'vue';

const props = defineProps<{
    pagination?: { currentPage: number; lastPage: number; total: number; data: Person[] };
}>();

const localInput = ref('');
const loading = ref(false);

const people = computed(() => props.pagination?.data ?? []);
const reachedEnd = computed(() => {
    if (!props.pagination) return true;
    return props.pagination.currentPage >= props.pagination.lastPage;
});

const handleSearch = () => {
    loading.value = true;
    router.reload({
        data: {
            search: localInput.value,
        },
        reset: ['pagination'],
        preserveUrl: true,
        onSuccess: () => {
            loading.value = false;
        },
    });
};

const debouncedSearch = useDebounceFn(() => {
    handleSearch();
}, 300);
</script>

<template>
    <Head title="Personas" />
    <div class="my-6 flex flex-col justify-between gap-4 sm:flex-row">
        <div class="flex gap-2">
            <!--Barra de búsqueda-->
            <UInput
                class="w-full sm:w-60 md:w-96"
                v-model="localInput"
                placeholder="Buscar..."
                leading-icon="lucide:search"
                type="search"
                :ui="{ base: 'pr-8', trailing: 'pe-1' }"
                @input="debouncedSearch"
            >
                <template v-if="localInput?.length" #trailing>
                    <UButton
                        color="neutral"
                        variant="link"
                        size="sm"
                        icon="i-lucide-circle-x"
                        aria-label="Clear input"
                        @click="
                            localInput = '';
                            handleSearch();
                        "
                    />
                </template>
            </UInput>
        </div>
        <h3>
            <span class="text-2xl font-semibold">{{ pagination?.total ?? 0 }}</span> persona{{ pagination?.total !== 1 ? 's' : '' }}
        </h3>
    </div>
    <USeparator class="my-6" />
    <template v-if="loading">
        <PersonIndexSkeleton />
    </template>
    <template v-else>
        <Deferred data="pagination">
            <template #fallback>
                <PersonIndexSkeleton />
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
                        <p class="line-clamp-2 text-center">{{ person.name }} {{ person.surname }}</p>
                        <p class="line-clamp-2 text-center">{{ person.kanjiName }} {{ person.kanjiSurname }}</p>
                    </div>
                </UCard>
            </div>
            <div v-if="people?.length === 0" class="text-muted text-center text-xl">No hay resultados</div>
        </Deferred>
        <!--Scroll infinito-->
        <WhenVisible
            v-if="!reachedEnd"
            :params="{
                only: ['pagination'],
                data: {
                    page: (pagination?.currentPage || 1) + 1,
                    search: localInput,
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
