<script setup lang="ts">
import DateRangePicker from '@/components/DateRangePicker.vue';
import { useTableFilters } from '@/composables/useTableFilters';
import { Magazine } from '@/types';
import { demographies, frequencies } from '@/utils/constants';
import { Table } from '@tanstack/table-core';
import { DateRange } from 'reka-ui';

//Tabla
const table = defineModel<{ tableApi: Table<Magazine> } | null>();

//Filtros iniciales
const starterFilters = [
    { id: 'demography', value: ['shounen', 'shoujo', 'seinen', 'josei'] },
    { id: 'frequency', value: 'all' },
    { id: 'date', value: undefined },
];

const { filters, setListFilter, setFilter, resetFilters } = useTableFilters(table, starterFilters);
</script>

<template>
    <!--Filtro de demografía-->
    <div class="my-3 flex flex-wrap items-center gap-1 sm:gap-2">
        <p>Demografía:</p>
        <UButton
            v-for="demography of demographies"
            :key="demography.value"
            :color="(filters!.find((item) => item.id === 'demography')!.value as string[]).includes(demography.value) ? 'primary' : 'neutral'"
            variant="soft"
            size="sm"
            @click="setListFilter('demography', demography.value)"
        >
            {{ demography.label }}
        </UButton>
    </div>
    <!--Filtro de periodicidad-->
    <div class="my-3 flex flex-wrap items-center gap-1 sm:gap-2">
        <p>Periodicidad:</p>
        <USelect
            v-model="filters!.find((item) => item.id === 'frequency')!.value as string"
            :items="[{ label: 'Todas', value: 'all' }, ...frequencies]"
            class="w-48"
            :ui="{ content: 'z-[3]' }"
            @change="setFilter('frequency')"
        />
    </div>
    <!--Filtro de fecha-->
    <div class="my-3 flex items-center gap-1 sm:gap-2">
        <p>Fecha:</p>
        <DateRangePicker v-model="filters!.find((item) => item.id === 'date')!.value as DateRange" />
    </div>
    <USeparator />
    <div class="mt-3 flex w-full justify-end">
        <UButton label="Borrar filtros" variant="outline" color="error" @click="resetFilters" />
    </div>
</template>
