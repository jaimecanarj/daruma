<script setup lang="ts">
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core';
import { ref } from 'vue';

defineProps<{ tab: string }>();

const globalFilter = defineModel<string>();

const activeBreakpoint = useBreakpoints(breakpointsTailwind).active();

const showFilters = ref(false);
</script>

<template>
    <div class="mt-8 flex justify-between">
        <div class="flex gap-2">
            <UInput class="w-48 sm:w-60" v-model="globalFilter" placeholder="Buscar..." leading-icon="lucide:search" type="search" />
            <UButton
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
    <template v-if="showFilters">
        <slot />
    </template>
</template>
