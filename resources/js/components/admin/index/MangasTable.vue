<script setup lang="ts">
import DeleteModal from '@/components/admin/index/DeleteModal.vue';
import FiltersHeader from '@/components/admin/index/FiltersHeader.vue';
import MangasFilter from '@/components/admin/index/MangasFilter.vue';
import { useFetchTable } from '@/composables/useFetchTable';
import { Manga } from '@/types';
import { actionsCell, sortableHeader, sortablePinnableHeader } from '@/utils/tableColumns';
import { parseDate } from '@internationalized/date';
import { TableColumn } from '@nuxt/ui';
import { h, ref, resolveComponent, useTemplateRef } from 'vue';

//Componentes
const UDropdownMenu = resolveComponent('UDropdownMenu');
const UButton = resolveComponent('UButton');
const UIcon = resolveComponent('UIcon');

//Modal
const deleteModal = useTemplateRef('deleteModal');

//Datos
const { fetchData, data, fetching } = useFetchTable<Manga>('manga.index');
fetchData();

//Columnas
const columns: TableColumn<Manga>[] = [
    {
        accessorKey: 'id',
        header: 'Id',
        cell: ({ row }) => `#${row.getValue('id')}`,
    },
    {
        accessorKey: 'name',
        header: ({ column }) => sortablePinnableHeader(column, 'Nombre', UButton),
    },
    {
        accessorKey: 'volumes',
        header: ({ column }) => sortableHeader(column, 'Tomos', UButton),
        enableGlobalFilter: false,
        filterFn: (row, columnId, filterValue) => {
            if (filterValue === 0) return !row.getValue(columnId); //Si 0, devuelve mangas sin tomos definido
            if (!filterValue) return true; //Si no hay filtro, devuelve todos
            return row.getValue(columnId) === filterValue;
        },
    },
    {
        id: 'date',
        accessorKey: 'startDate',
        header: ({ column }) => sortableHeader(column, 'Fechas', UButton),
        cell: ({ row }) => {
            let startDate = '';
            let endDate = '';
            if (row.original.startDate) {
                startDate = new Date(row.original.startDate).toLocaleString('es-ES', {
                    month: '2-digit',
                    year: 'numeric',
                });
            }

            if (row.original.endDate) {
                endDate = new Date(row.original.endDate).toLocaleString('es-ES', {
                    month: '2-digit',
                    year: 'numeric',
                });
            }

            const dash = row.original.startDate || row.original.endDate ? ' - ' : '';

            return h('p', [startDate, dash, endDate]);
        },
        sortingFn: (rowA, rowB) => {
            // Obtener las fechas de inicio de cada fila
            const startDateA = rowA.original.startDate ? new Date(rowA.original.startDate).getTime() : 0;
            const startDateB = rowB.original.startDate ? new Date(rowB.original.startDate).getTime() : 0;

            // Si ambas fechas existen, compararlas
            if (startDateA && startDateB) {
                return startDateA - startDateB;
            }

            // Si solo una fecha existe, colocar las filas con fecha al principio
            if (startDateA && !startDateB) return -1;
            if (!startDateA && startDateB) return 1;

            // Si ninguna tiene fecha de inicio, no se ordena
            return 0;
        },
        enableGlobalFilter: false,
        filterFn: (row, columnId, filterValue) => {
            //Si el manga engloba a las fechas seleccionadas, devolver true también
            if (filterValue?.end) {
                if (!row.original.startDate) return false;
                const startDate = parseDate(row.original.startDate as string);
                if (!row.original.endDate) {
                    return startDate.compare(filterValue.end) <= 0;
                } else {
                    const endDate = parseDate(row.original.endDate as string);
                    return startDate.compare(filterValue.end) <= 0 && endDate.compare(filterValue.start) >= 0;
                }
            } else {
                return true;
            }
        },
    },
    {
        accessorKey: 'language',
        header: 'Idioma',
        cell: ({ row }) => {
            let language = '';
            switch (row.original.language) {
                case 'es':
                    language = 'Español';
                    break;
                case 'en':
                    language = 'Inglés';
                    break;
                case 'jp':
                    language = 'Japonés';
                    break;
            }
            return h('p', [language]);
        },
        enableGlobalFilter: false,
        filterFn: (row, columnId, filterValue) => {
            if (filterValue === 'all') return true;
            return filterValue === row.getValue(columnId);
        },
    },
    {
        accessorKey: 'readingDirection',
        header: 'Dir. de lectura',
        cell: ({ row }) => {
            return h('p', [row.original.readingDirection === 'ltr' ? 'Occidental' : 'Oriental']);
        },
        enableGlobalFilter: false,
        filterFn: (row, columnId, filterValue) => {
            return !!filterValue.includes(row.getValue(columnId));
        },
    },
    {
        accessorKey: 'finished',
        header: '',
        cell: ({ row }) => {
            return h(UIcon, { class: 'size-5', name: row.original.finished ? 'i-lucide-badge-check' : 'i-lucide-badge' });
        },
        enableGlobalFilter: false,
        filterFn: (row, columnId, filterValue) => {
            if (filterValue.length === 0) return false;
            if (filterValue.length > 1) return true;
            if (filterValue.includes('finished')) {
                return !!row.getValue(columnId);
            } else {
                return !row.getValue(columnId);
            }
        },
    },
    {
        id: 'actions',
        cell: ({ row }) => actionsCell(row, UDropdownMenu, UButton, deleteModal, 'manga'),
    },
];

//Filtros
const globalFilter = ref('');
const filters = [
    { id: 'volumes', value: undefined },
    { id: 'date', value: null },
    { id: 'language', value: 'all' },
    { id: 'readingDirection', value: ['ltr', 'rtl'] },
    { id: 'finished', value: ['finished', 'unfinished'] },
];
const table = useTemplateRef('table');
</script>

<template>
    <FiltersHeader tab="manga" v-model="globalFilter" filters><MangasFilter v-model="table" /></FiltersHeader>
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
        delete-route="manga.destroy"
        delete-desc="este manga"
        delete-success-message="Manga borrado correctamente"
        @item-deleted="fetchData"
    />
</template>
