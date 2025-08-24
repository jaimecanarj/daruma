<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { breakpointsTailwind, refDebounced, useBreakpoints } from '@vueuse/core';
import { ref, watch } from 'vue';

const props = defineProps<{
    filters?: boolean;
    class?: string;
    random?: boolean;
    itemsIds?: number[];
    itemsType?: 'manga' | 'person' | 'magazine';
}>();
const emit = defineEmits(['search']);

const searchInput = defineModel<string>();

const localInput = ref<string>(searchInput.value || '');

const activeBreakpoint = useBreakpoints(breakpointsTailwind).smaller('md');

const showFilters = ref<string | undefined>(undefined);

const searchInputDebounced = refDebounced(localInput, 300);

watch(searchInputDebounced, (newValue) => {
    searchInput.value = newValue;
    emit('search');
});

const toggleShowFilters = () => {
    showFilters.value = showFilters.value === '0' ? undefined : '0';
};

const selectRandomItem = () => {
    const randomIndex = Math.floor(Math.random() * (props.itemsIds?.length ?? 0));
    const randomId = props.itemsIds?.[randomIndex];
    let route = '';
    switch (props.itemsType) {
        case 'manga':
            route = `/mangas/${randomId}`;
            break;
        case 'person':
            route = `/people/${randomId}`;
            break;
        case 'magazine':
            route = `/magazines/${randomId}`;
            break;
        default:
    }
    if (route) {
        router.visit(route);
    }
};
</script>

<template>
    <div :class="props.class">
        <div class="flex gap-2">
            <UInput
                class="w-full sm:w-60"
                v-model="localInput"
                placeholder="Buscar..."
                leading-icon="lucide:search"
                type="search"
                :ui="{ base: 'pr-8', trailing: 'pe-1' }"
            >
                <template v-if="localInput?.length" #trailing>
                    <UButton color="neutral" variant="link" size="sm" icon="i-lucide-circle-x" aria-label="Clear input" @click="localInput = ''" />
                </template>
            </UInput>
            <UButton
                v-if="filters"
                :variant="showFilters === '0' ? 'solid' : 'soft'"
                color="neutral"
                trailing-icon="lucide:list-filter"
                :size="activeBreakpoint ? 'lg' : 'xl'"
                @click="toggleShowFilters"
                >{{ activeBreakpoint ? '' : 'Filtros' }}
            </UButton>
            <UButton v-if="random" icon="lucide:dices" variant="outline" color="neutral" class="px-3" @click="selectRandomItem" />
        </div>
        <slot name="rightSide" />
    </div>
    <UAccordion v-if="filters" v-model="showFilters" :items="[{}]" :ui="{ trigger: 'p-0', trailingIcon: 'size-0' }">
        <template #content>
            <UCard variant="subtle" class="mt-4 rounded-xl">
                <slot />
            </UCard>
        </template>
    </UAccordion>
</template>
