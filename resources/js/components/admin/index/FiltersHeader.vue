<script setup lang="ts">
import { breakpointsTailwind, refDebounced, useBreakpoints } from '@vueuse/core';
import { ref, watch } from 'vue';

defineProps<{ tab: string; filters?: boolean }>();

const globalFilter = defineModel<string>();

const localInput = ref<string>(globalFilter.value || '');

const activeBreakpoint = useBreakpoints(breakpointsTailwind).active();

const showFilters = ref<string | undefined>(undefined);

const globalFilterDebounced = refDebounced(localInput, 300);

watch(globalFilterDebounced, (newValue) => {
    globalFilter.value = newValue;
});

const toggleShowFilters = () => {
    showFilters.value = showFilters.value === '0' ? undefined : '0';
};
</script>

<template>
    <div class="mt-8 flex justify-between">
        <div class="flex gap-2">
            <UInput
                class="w-48 sm:w-60"
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
                variant="soft"
                color="neutral"
                trailing-icon="lucide:list-filter"
                :size="activeBreakpoint ? 'xl' : 'lg'"
                @click="toggleShowFilters"
                >{{ activeBreakpoint ? 'Filtros' : '' }}
            </UButton>
        </div>
        <ULink :to="`/admin/create/${tab}`">
            <UButton trailing-icon="lucide:square-plus" :size="activeBreakpoint ? 'xl' : 'lg'" class="font-semibold">{{
                activeBreakpoint ? 'Crear' : ''
            }}</UButton>
        </ULink>
    </div>
    <UAccordion v-model="showFilters" :items="[{}]">
        <template #trailing>{{ '' }}</template>
        <template #content>
            <UCard variant="subtle" class="rounded-xl">
                <slot />
            </UCard>
        </template>
    </UAccordion>
</template>
