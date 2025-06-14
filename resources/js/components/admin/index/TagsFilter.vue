<script setup lang="ts">
import { Tag } from '@/types';
import { Table } from '@tanstack/table-core';
import { ref } from 'vue';

const table = defineModel<{ tableApi: Table<Tag> } | null>();

const filters = ref([{ id: 'type', value: table.value?.tableApi?.getColumn('type')?.getFilterValue() as string[] }]);
const setFilter = (id: string, value: string) => {
    const filter = filters.value!.find((item) => item.id === id)!;
    if (filter.value.includes(value)) {
        filter.value = filter.value.filter((item) => item !== value);
    } else {
        filter.value.push(value);
    }

    table.value?.tableApi?.getColumn(id)?.setFilterValue(filter.value);
};
</script>

<template>
    <!--Filtro de tipo-->
    <div class="flex items-center gap-2">
        <p>Tipo:</p>
        <!--Género-->
        <UButton
            :color="filters!.find((item) => item.id === 'type')!.value.includes('genre') ? 'primary' : 'neutral'"
            variant="soft"
            class="cursor-pointer"
            @click="setFilter('type', 'genre')"
            >Género</UButton
        >
        <!--Tema-->
        <UButton
            :color="filters!.find((item) => item.id === 'type')!.value.includes('theme') ? 'secondary' : 'neutral'"
            variant="soft"
            @click="setFilter('type', 'theme')"
            class="cursor-pointer"
            >Tema</UButton
        >
    </div>
</template>
