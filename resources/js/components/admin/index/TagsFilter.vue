<script setup lang="ts">
import { useTableFilters } from '@/composables/useTableFilters';
import { Tag } from '@/types';
import { Table } from '@tanstack/table-core';

//Tabla
const table = defineModel<{ tableApi: Table<Tag> } | null>();

//Filtros iniciales
const starterFilters = [{ id: 'type', value: ['theme', 'genre'] }];

const { filters, setListFilter, resetFilters } = useTableFilters(table, starterFilters);
</script>

<template>
    <!--Filtro de tipo-->
    <div class="my-3 flex items-center gap-2">
        <p>Tipo:</p>
        <!--Género-->
        <UButton
            :color="(filters!.find((item) => item.id === 'type')!.value as string[]).includes('genre') ? 'primary' : 'neutral'"
            variant="soft"
            size="sm"
            @click="setListFilter('type', 'genre')"
        >
            Género
        </UButton>
        <!--Tema-->
        <UButton
            :color="(filters!.find((item) => item.id === 'type')!.value as string[]).includes('theme') ? 'secondary' : 'neutral'"
            variant="soft"
            size="sm"
            @click="setListFilter('type', 'theme')"
        >
            Tema
        </UButton>
    </div>
    <USeparator />
    <div class="mt-3 flex w-full justify-end">
        <UButton label="Borrar filtros" variant="outline" color="error" @click="resetFilters" />
    </div>
</template>
