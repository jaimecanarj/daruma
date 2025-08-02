<script setup lang="ts">
import DeleteModal from '@/components/admin/DeleteModal.vue';
import CreateButton from '@/components/admin/filters/CreateButton.vue';
import MagazinesFilter from '@/components/admin/filters/MagazinesFilter.vue';
import FiltersHeader from '@/components/FiltersHeader.vue';
import { useFetchTable } from '@/composables/useFetchTable';
import { Magazine } from '@/types';
import { actionsCell, sortableHeader, sortablePinnableHeader } from '@/utils/tableColumns';
import { parseDate } from '@internationalized/date';
import { TableColumn } from '@nuxt/ui';
import { h, ref, resolveComponent, useTemplateRef } from 'vue';

//Componentes
const UDropdownMenu = resolveComponent('UDropdownMenu');
const UButton = resolveComponent('UButton');

//Modal
const deleteModal = useTemplateRef('deleteModal');

//Datos
const { fetchData, data, fetching } = useFetchTable<Magazine>('magazine.index');
fetchData();

//Columnas
const columns: TableColumn<Magazine>[] = [
    {
        accessorKey: 'id',
        header: ({ column }) => sortableHeader(column, 'Id', UButton),
        cell: ({ row }) => `#${row.getValue('id')}`,
    },
    {
        accessorKey: 'name',
        header: ({ column }) => sortablePinnableHeader(column, 'Nombre', UButton),
    },
    {
        accessorKey: 'publisher',
        header: 'Editorial',
        enableGlobalFilter: false,
    },
    {
        accessorKey: 'demography',
        header: 'Demografía',
        cell: ({ row }) => {
            return h('p', [(row.getValue('demography') as string).charAt(0).toUpperCase(), (row.getValue('demography') as string).slice(1)]);
        },
        enableGlobalFilter: false,
        filterFn: (row, columnId, filterValue) => {
            return !!filterValue.includes(row.getValue(columnId));
        },
    },
    {
        accessorKey: 'frequency',
        header: 'Periodicidad',
        cell: ({ row }) => {
            let label = '';
            switch (row.getValue('frequency')) {
                case 'weekly':
                    label = 'Semanal';
                    break;
                case 'biweekly':
                    label = 'Bisemanal';
                    break;
                case 'monthly':
                    label = 'Mensual';
                    break;
                case 'bimonthly':
                    label = 'Bimensual';
                    break;
                case 'quarterly':
                    label = 'Trimestral';
                    break;
                case 'irregular':
                    label = 'Irregular';
                    break;
            }
            return h('p', [label]);
        },
        enableGlobalFilter: false,
        filterFn: (row, columnId, filterValue) => {
            if (filterValue === 'all') return true;
            return filterValue === row.getValue(columnId);
        },
    },
    {
        accessorKey: 'date',
        header: ({ column }) => sortableHeader(column, 'Fecha', UButton),
        cell: ({ row }) => {
            return row.getValue('date')
                ? new Date(row.getValue('date')).toLocaleString('es-ES', {
                      day: 'numeric',
                      month: 'long',
                      year: 'numeric',
                  })
                : '';
        },
        enableGlobalFilter: false,
        filterFn: (row, columnId, filterValue) => {
            if (filterValue?.end) {
                if (!row.getValue(columnId)) return false;
                const date = parseDate(row.getValue(columnId) as string);
                return date.compare(filterValue.start) >= 0 && date.compare(filterValue.end) <= 0;
            } else {
                return true;
            }
        },
    },
    {
        id: 'actions',
        cell: ({ row }) => actionsCell(row, UDropdownMenu, UButton, deleteModal, 'magazine'),
    },
];

//Filtros
const globalFilter = ref('');
const filters = [
    { id: 'demography', value: ['shounen', 'shoujo', 'seinen', 'josei'] },
    { id: 'frequency', value: 'all' },
    { id: 'date', value: null },
];
const table = useTemplateRef('table');
</script>

<template>
    <FiltersHeader v-model="globalFilter" filters class="mt-8 flex justify-between gap-2">
        <template #rightSide><CreateButton tab="magazine" /></template>
        <MagazinesFilter v-model="table" />
    </FiltersHeader>
    <UTable
        ref="table"
        sticky
        :loading="fetching"
        :data="data"
        :columns="columns"
        :column-filters="filters"
        :sorting="[{ id: 'name', desc: false }]"
        :global-filter="globalFilter"
        class="mt-6 h-[610px] flex-1"
    />
    <DeleteModal
        ref="deleteModal"
        delete-route="magazine.destroy"
        delete-desc="esta revista"
        delete-success-message="Revista borrada correctamente"
        @item-deleted="fetchData"
    />
</template>
