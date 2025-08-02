<script setup lang="ts">
import { breakpointsTailwind, refDebounced, useBreakpoints } from '@vueuse/core';
import { ref, watch } from 'vue';

const props = defineProps<{ filters?: boolean; class?: string }>();
const emit = defineEmits(['search']);

const searchInput = defineModel<string>();

const localInput = ref<string>(searchInput.value || '');

const activeBreakpoint = useBreakpoints(breakpointsTailwind).active();

const showFilters = ref<string | undefined>(undefined);

const searchInputDebounced = refDebounced(localInput, 300);

watch(searchInputDebounced, (newValue) => {
    searchInput.value = newValue;
    emit('search');
});

const toggleShowFilters = () => {
    showFilters.value = showFilters.value === '0' ? undefined : '0';
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
                :size="activeBreakpoint ? 'xl' : 'lg'"
                @click="toggleShowFilters"
                >{{ activeBreakpoint ? 'Filtros' : '' }}
            </UButton>
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
