<script setup lang="ts">
import DateRangePicker from '@/components/DateRangePicker.vue';
import { Magazine } from '@/types';
import { demographies, frequencies } from '@/utils/constants';
import { Table } from '@tanstack/table-core';
import { DateRange } from 'reka-ui';
import { ref, watch } from 'vue';

const table = defineModel<{ tableApi: Table<Magazine> } | null>();

const filters = ref([
    { id: 'demography', value: table.value?.tableApi?.getColumn('demography')?.getFilterValue() as string[] },
    { id: 'frequency', value: table.value?.tableApi?.getColumn('frequency')?.getFilterValue() as string },
    { id: 'date', value: table.value?.tableApi?.getColumn('date')?.getFilterValue() as DateRange },
]);

const setFilter = (id: string, value: string) => {
    const filter = filters.value!.find((item) => item.id === id)! as { value: string[] };
    if (filter.value.includes(value)) {
        filter.value = filter.value.filter((item) => item !== value);
    } else {
        filter.value.push(value);
    }

    table.value?.tableApi?.getColumn(id)?.setFilterValue(filter.value);
};

//Actualizo el valor del filtro date cuando se selecciona una fecha de fin o cuando se borran las fechas
watch(
    filters,
    () => {
        const filter = filters.value!.find((item) => item.id === 'date')! as { value: DateRange };
        if (filter.value?.end) {
            table.value?.tableApi?.getColumn('date')?.setFilterValue(filter.value);
        } else if (!filter.value) {
            table.value?.tableApi?.getColumn('date')?.setFilterValue(null);
        }
    },
    { deep: true },
);
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
            class="cursor-pointer"
            @click="setFilter('demography', demography.value)"
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
            @change="table?.tableApi?.getColumn('frequency')?.setFilterValue(filters!.find((item) => item.id === 'frequency')!.value as string)"
        />
    </div>
    <!--Filtro de fecha-->
    <div class="my-3 flex items-center gap-1 sm:gap-2">
        <p>Fecha:</p>
        <DateRangePicker v-model="filters!.find((item) => item.id === 'date')!.value as DateRange" />
    </div>
</template>
