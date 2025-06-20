<script setup lang="ts">
import DeleteModal from '@/components/admin/index/DeleteModal.vue';
import FiltersHeader from '@/components/admin/index/FiltersHeader.vue';
import { useFetchTable } from '@/composables/useFetchTable';
import { Person } from '@/types';
import { actionsCell, sortableHeader, sortablePinnableHeader } from '@/utils/tableColumns';
import { TableColumn } from '@nuxt/ui';
import { FilterFnOption } from '@tanstack/table-core';
import { h, ref, resolveComponent, useTemplateRef } from 'vue';

//Componentes
const UDropdownMenu = resolveComponent('UDropdownMenu');
const UButton = resolveComponent('UButton');

//Modal
const deleteModal = useTemplateRef('deleteModal');

//Datos
const { fetchData, data, fetching } = useFetchTable<Person>('person.index');
fetchData();

//Columnas
const columns: TableColumn<Person>[] = [
    {
        accessorKey: 'id',
        header: 'Id',
        cell: ({ row }) => `#${row.getValue('id')}`,
    },
    {
        id: 'name',
        header: ({ column }) => sortablePinnableHeader(column, 'Nombre', UButton),
        cell: ({ row }) => {
            const kanjiName = row.original.kanjiName ? ` ( ${row.original.kanjiName} )` : '';
            return h('p', [row.original.name, kanjiName]);
        },
    },
    {
        id: 'surname',
        header: ({ column }) => sortableHeader(column, 'Apellido', UButton),
        cell: ({ row }) => {
            const kanjiSurname = row.original.kanjiSurname ? ` ( ${row.original.kanjiSurname} )` : '';
            return h('p', [row.original.surname, kanjiSurname]);
        },
    },
    {
        id: 'actions',
        cell: ({ row }) => actionsCell(row, UDropdownMenu, UButton, deleteModal, 'person'),
    },
];

//Filtros
const globalFilter = ref('');
const globalFilterFn: FilterFnOption<Person> = (row, columnId, filterValue) => {
    // Si no hay valor de filtro, no filtrar
    if (!filterValue) return true;

    const searchTerm = String(filterValue).toLowerCase();

    // Accede a los valores originales de la fila para evitar problemas
    const name = String(row.original.name || '').toLowerCase();
    const kanjiName = String(row.original.kanjiName || '').toLowerCase();
    const surname = String(row.original.surname || '').toLowerCase();
    const kanjiSurname = String(row.original.kanjiSurname || '').toLowerCase();

    // Devuelve true si alguno de los campos coincide
    return name.includes(searchTerm) || kanjiName.includes(searchTerm) || surname.includes(searchTerm) || kanjiSurname.includes(searchTerm);
};
</script>

<template>
    <FiltersHeader tab="person" v-model="globalFilter" />
    <UTable
        sticky
        :loading="fetching"
        :data="data"
        :columns="columns"
        :sorting="[{ id: 'name', desc: false }]"
        :global-filter="globalFilter"
        :global-filter-options="{ globalFilterFn: globalFilterFn }"
        class="mt-6 h-[610px] flex-1"
    />
    <DeleteModal ref="deleteModal" delete-route="person.destroy" delete-desc="esta persona" delete-success-message="Persona borrada correctamente" />
</template>
