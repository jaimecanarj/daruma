<script setup lang="ts">
import MangasFilters from '@/components/mangas/MangasFilters.vue';
import { useMangasStore } from '@/stores/mangasStore';
import { breakpointsTailwind, useBreakpoints, useDebounceFn } from '@vueuse/core';
import { computed, ref } from 'vue';

const activeBreakpoint = useBreakpoints(breakpointsTailwind).active();

const emit = defineEmits(['handleSearch']);

const store = useMangasStore();
const filters = computed(() => store.state);
const loading = defineModel('loading');
const display = defineModel('display');

const showFilters = ref<string | undefined>(undefined);

const toggleShowFilters = () => {
    showFilters.value = showFilters.value === '0' ? undefined : '0';
};

const debouncedSearch = useDebounceFn(() => {
    loading.value = true;
    emit('handleSearch', store.state);
}, 300);
</script>

<template>
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
                            loading = true;
                            emit('handleSearch');
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
        <!--Selector de display-->
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
    <UAccordion v-model="showFilters" :items="[{}]" :ui="{ trigger: 'p-0', trailingIcon: 'size-0' }">
        <template #content>
            <UCard variant="subtle" class="rounded-xl">
                <MangasFilters @filter="emit('handleSearch', store.state)" />
            </UCard>
        </template>
    </UAccordion>
</template>
