<script setup lang="ts">
import DateRangePicker from '@/components/DateRangePicker.vue';
import { User } from '@/types';
import { Table } from '@tanstack/table-core';
import { DateRange } from 'reka-ui';
import { ref, watch } from 'vue';

const table = defineModel<{ tableApi: Table<User> } | null>();

const filters = ref([{ id: 'createdAt', value: table.value?.tableApi?.getColumn('createdAt')?.getFilterValue() as DateRange }]);

//Actualizo el valor del filtro cuando se selecciona una fecha de fin o cuando se borran las fechas
watch(
    filters,
    () => {
        const filter = filters.value!.find((item) => item.id === 'createdAt')!;
        if (filter.value?.end) {
            table.value?.tableApi?.getColumn('createdAt')?.setFilterValue(filter.value);
        } else if (!filter.value) {
            table.value?.tableApi?.getColumn('createdAt')?.setFilterValue(null);
        }
    },
    { deep: true },
);
</script>

<template>
    <!--Filtro de registro-->
    <div class="flex items-center gap-1 sm:gap-2">
        <p>Registro:</p>
        <DateRangePicker v-model="filters!.find((item) => item.id === 'createdAt')!.value as DateRange" />
    </div>
</template>
