<script setup lang="ts">
import DateRangePicker from '@/components/DateRangePicker.vue';
import { useTableFilters } from '@/composables/useTableFilters';
import { Manga } from '@/types';
import { languageOptions, readingDirections } from '@/utils/constants';
import { Table } from '@tanstack/table-core';
import { DateRange } from 'reka-ui';

//Tabla
const table = defineModel<{ tableApi: Table<Manga> } | null>();

//Filtros iniciales
const starterFilters = [
    { id: 'volumes', value: undefined },
    { id: 'date', value: null },
    { id: 'language', value: 'all' },
    { id: 'readingDirection', value: ['ltr', 'rtl'] },
    { id: 'finished', value: ['finished', 'unfinished'] },
];

const { filters, setListFilter, setFilter, resetFilters } = useTableFilters(table, starterFilters);
</script>

<template>
    <!--Tomos-->
    <div class="my-3 flex items-center gap-2">
        <p>Tomos:</p>
        <UInputNumber
            v-model="filters!.find((item) => item.id === 'volumes')!.value as number | null"
            orientation="vertical"
            class="w-20"
            @change="setFilter('volumes')"
        />
    </div>
    <!--Fecha-->
    <div class="my-3 flex items-center gap-2">
        <p>Fecha:</p>
        <DateRangePicker v-model="filters!.find((item) => item.id === 'date')!.value as DateRange | undefined" />
    </div>
    <!--Idioma-->
    <div class="my-3 flex items-center gap-2">
        <p>Idioma:</p>
        <USelect
            v-model="filters!.find((item) => item.id === 'language')!.value as string"
            :items="[{ label: 'Todos', value: 'all' }, ...languageOptions]"
            class="w-48"
            :ui="{ content: 'z-[3]' }"
            @change="setFilter('language')"
        />
    </div>
    <!--Dirección de lectura-->
    <div class="my-3 flex items-center gap-2">
        <p>Dir. de lectura:</p>
        <UButton
            v-for="readingDirection of readingDirections"
            :key="readingDirection.value"
            :label="readingDirection.label"
            :color="
                (filters!.find((item) => item.id === 'readingDirection')!.value as string[]).includes(readingDirection.value) ? 'primary' : 'neutral'
            "
            variant="soft"
            size="sm"
            @click="setListFilter('readingDirection', readingDirection.value)"
        />
    </div>
    <!--Finalizado-->
    <div class="my-3 flex items-center gap-2">
        <p>Finalizado:</p>
        <UButton
            label="Completo"
            :color="(filters!.find((item) => item.id === 'finished')!.value as string[]).includes('finished') ? 'primary' : 'neutral'"
            variant="soft"
            size="sm"
            @click="setListFilter('finished', 'finished')"
        />
        <UButton
            label="Incompleto"
            :color="(filters!.find((item) => item.id === 'finished')!.value as string[]).includes('unfinished') ? 'primary' : 'neutral'"
            variant="soft"
            size="sm"
            @click="setListFilter('finished', 'unfinished')"
        />
    </div>
    <USeparator />
    <div class="mt-3 flex w-full justify-end">
        <UButton label="Borrar filtros" variant="outline" color="error" @click="resetFilters" />
    </div>
</template>
