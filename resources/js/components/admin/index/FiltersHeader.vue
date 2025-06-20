<script setup lang="ts">
import { breakpointsTailwind, refDebounced, useBreakpoints } from '@vueuse/core';
import { ref, watch } from 'vue';

defineProps<{ tab: string; filters?: boolean }>();

const globalFilter = defineModel<string>();

const localInput = ref<string>(globalFilter.value || '');

const activeBreakpoint = useBreakpoints(breakpointsTailwind).active();

const showFilters = ref(false);

const globalFilterDebounced = refDebounced(localInput, 300);

watch(globalFilterDebounced, (newValue) => {
    globalFilter.value = newValue;
});
</script>

<template>
    <div class="mt-8 flex justify-between">
        <div class="flex gap-2">
            <UInput class="w-48 sm:w-60" v-model="localInput" placeholder="Buscar..." leading-icon="lucide:search" type="search">
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
                class="cursor-pointer"
                @click="showFilters = !showFilters"
                >{{ activeBreakpoint ? 'Filtrar' : '' }}
            </UButton>
        </div>
        <ULink :to="`admin/create/${tab}`">
            <UButton trailing-icon="lucide:square-plus" :size="activeBreakpoint ? 'xl' : 'lg'" class="cursor-pointer">{{
                activeBreakpoint ? 'Crear' : ''
            }}</UButton>
        </ULink>
    </div>
    <UModal v-model:open="showFilters" title="Filtros" :ui="{ overlay: 'z-[1]', content: 'z-[2]' }">
        <template #body>
            <slot />
        </template>
    </UModal>
</template>
