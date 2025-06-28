<script setup lang="ts">
import DateRangePicker from '@/components/DateRangePicker.vue';
import { useTableFilters } from '@/composables/useTableFilters';
import { User } from '@/types';
import { Table } from '@tanstack/table-core';
import { DateRange } from 'reka-ui';

//Tabla
const table = defineModel<{ tableApi: Table<User> } | null>();

//Filtros iniciales
const starterFilters = [{ id: 'createdAt', value: undefined }];

const { filters, resetFilters } = useTableFilters(table, starterFilters);
</script>

<template>
    <!--Filtro de registro-->
    <div class="my-3 flex items-center gap-1 sm:gap-2">
        <p>Registro:</p>
        <DateRangePicker v-model="filters!.find((item) => item.id === 'createdAt')!.value as DateRange | undefined" />
    </div>
    <USeparator />
    <div class="mt-3 flex w-full justify-end">
        <UButton label="Borrar filtros" variant="outline" color="error" @click="resetFilters" />
    </div>
</template>
