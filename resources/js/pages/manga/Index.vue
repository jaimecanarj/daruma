<script setup lang="ts">
import MangaIndexSkeleton from '@/components/skeletons/MangaIndexSkeleton.vue';
import { Manga } from '@/types';
import { Deferred, Head, router, WhenVisible } from '@inertiajs/vue3';
import { breakpointsTailwind, useBreakpoints, useDebounceFn } from '@vueuse/core';
import { computed, ref } from 'vue';

const props = defineProps<{ pagination: { currentPage: number; lastPage: number; total: number; data: Manga[] }; filters: { search?: string } }>();

const activeBreakpoint = useBreakpoints(breakpointsTailwind).active();
const fullYear = useBreakpoints(breakpointsTailwind).greaterOrEqual('lg');

const localInput = ref(props.filters.search);
const showFilters = ref(false);
const display = ref<'grid' | 'list'>('grid');
const loading = ref(false);

const mangas = computed(() => props.pagination?.data);
const reachedEnd = computed(() => props.pagination?.currentPage >= props.pagination?.lastPage);

const handleSearch = () => {
    router.reload({
        data: { search: localInput.value },
        reset: ['pagination'],
        preserveUrl: true,
        onSuccess: () => {
            loading.value = false;
        },
    });
};

const debouncedSearch = useDebounceFn(() => {
    loading.value = true;
    handleSearch();
}, 300);
</script>

<template>
    <Head>
        <title>Mangas</title>
    </Head>
    <div class="my-6 flex flex-col justify-between gap-4 sm:flex-row">
        <div class="flex gap-2">
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
                            loading = true;
                            handleSearch();
                        "
                    />
                </template>
            </UInput>
            <UButton
                variant="soft"
                color="neutral"
                trailing-icon="lucide:list-filter"
                :size="activeBreakpoint ? 'xl' : 'lg'"
                @click="showFilters = !showFilters"
                >{{ activeBreakpoint ? 'Filtros' : '' }}
            </UButton>
        </div>
        <UButtonGroup class="ml-auto">
            <UButton
                color="neutral"
                :variant="display === 'grid' ? 'solid' : 'subtle'"
                icon="lucide:grid-3x2"
                :size="activeBreakpoint ? 'xl' : 'lg'"
                @click="display = 'grid'"
            />
            <UButton
                color="neutral"
                :variant="display === 'list' ? 'solid' : 'subtle'"
                icon="lucide:layout-list"
                :size="activeBreakpoint ? 'xl' : 'lg'"
                @click="display = 'list'"
            />
        </UButtonGroup>
    </div>
    <!--Filtros-->
    <div v-show="showFilters"></div>
    <USeparator class="my-6" />
    <template v-if="loading">
        <MangaIndexSkeleton :display="display" />
    </template>
    <template v-else>
        <Deferred data="pagination">
            <template #fallback>
                <MangaIndexSkeleton :display="display" />
            </template>
            <!--Vista grid-->
            <div v-if="display === 'grid'" class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                <div v-for="manga in mangas" :key="manga.id" class="group relative aspect-[1/1.4142] cursor-pointer rounded-md shadow-md">
                    <img :src="`storage/${manga.cover}`" class="h-full w-full rounded-md object-cover" alt="Portada de manga" />
                    <div
                        class="absolute inset-0 flex flex-col justify-end bg-black/70 p-3 opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                        @click="router.visit(route('mangas.show', manga.id))"
                    >
                        <h3 class="line-clamp-2 text-lg font-bold text-white/90 select-none">{{ manga.name }}</h3>
                    </div>
                </div>
            </div>
            <!--Vista tarjetas-->
            <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <UCard v-for="manga in mangas" :key="manga.id" variant="subtle" :ui="{ body: 'p-2 sm:p-3' }">
                    <div class="flex gap-2 overflow-hidden">
                        <div class="max-w-[150px] basis-1/4">
                            <img
                                :src="`storage/${manga.cover}`"
                                class="aspect-[1/1.4142] w-full cursor-pointer rounded-sm"
                                alt="Portada de manga"
                                @click="router.visit(route('mangas.show', manga.id))"
                            />
                        </div>
                        <div class="flex min-w-0 basis-3/4 flex-col gap-1 px-1">
                            <div class="flex w-full items-center">
                                <h3
                                    class="min-w-0 flex-1 cursor-pointer truncate text-lg font-bold"
                                    @click="router.visit(route('mangas.show', manga.id))"
                                >
                                    {{ manga.name }}
                                </h3>
                                <UIcon :name="manga.finished ? 'lucide:badge-check' : 'lucide:badge'" class="ml-4 size-5 flex-shrink-0" />
                            </div>
                            <div class="flex gap-2 text-sm lg:text-base">
                                <p v-if="manga.startDate !== undefined">
                                    <UIcon name="lucide:calendar" class="mr-0.5 mb-1 inline" /> {{ manga.startDate.slice(fullYear ? 0 : 2, 4) }} -
                                    <span v-if="manga.endDate">{{ manga.endDate.slice(fullYear ? 0 : 2, 4) }}</span>
                                </p>
                                <p v-if="manga.tankoubon">
                                    <UIcon name="lucide:library-big" class="mr-0.5 mb-1 inline" /> {{ manga.tankoubon }}
                                    {{ activeBreakpoint ? (manga.tankoubon > 1 ? 'vols' : 'vol') : '' }}
                                </p>
                                <p v-if="manga.chapters">
                                    <UIcon name="lucide:book-open-text" class="mr-0.5 mb-1 inline" />{{ manga.chapters }}
                                    {{ activeBreakpoint ? (manga.chapters > 1 ? 'caps' : 'cap') : '' }}
                                </p>
                            </div>
                            <div class="flex h-[132px] flex-col sm:h-44">
                                <div class="flex max-h-8 flex-shrink-0 flex-wrap items-center gap-x-2 overflow-hidden">
                                    <p v-for="tag of manga.tags" :key="tag.id" class="text-[0.6666rem] font-bold uppercase">
                                        {{ tag.name }}
                                    </p>
                                </div>
                                <div class="relative flex-grow overflow-hidden text-sm">
                                    <p>{{ manga.sinopsis }}</p>
                                    <!--Colores de from escritos en hexadecimal pues es imposible obtener los colores relativos-->
                                    <div
                                        class="pointer-events-none absolute bottom-0 left-0 h-5 w-full bg-gradient-to-t from-[#fafaf9] to-transparent dark:from-[#231f1d]"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </UCard>
            </div>
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
