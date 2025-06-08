<script setup lang="ts">
import DeleteModal from '@/components/admin/index/DeleteModal.vue';
import FiltersHeader from '@/components/admin/index/FiltersHeader.vue';
import { useFetchTable } from '@/composables/useFetchTable';
import { Manga } from '@/types';
import { actionsCell, sortableHeader, sortablePinnableHeader } from '@/utils/tableColumns';
import { TableColumn } from '@nuxt/ui';
import { h, ref, resolveComponent, useTemplateRef } from 'vue';

const UDropdownMenu = resolveComponent('UDropdownMenu');
const UButton = resolveComponent('UButton');
const UIcon = resolveComponent('UIcon');

const deleteModal = useTemplateRef('deleteModal');

//Ejecutar al crear el componente
const { fetchData, data, fetching } = useFetchTable<Manga>('manga.index');
fetchData();

const globalFilter = ref('');

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
                endDate = new Date(row.original.startDate).toLocaleString('es-ES', {
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
    },
    {
        accessorKey: 'readingDirection',
        header: 'Dir. de lectura',
        cell: ({ row }) => {
            return h('p', [row.original.readingDirection === 'ltr' ? 'Occidental' : 'Oriental']);
        },
        enableGlobalFilter: false,
    },
    {
        accessorKey: 'finished',
        header: '',
        cell: ({ row }) => {
            return h(UIcon, { class: 'size-5', name: row.original.finished ? 'i-lucide-badge-check' : 'i-lucide-badge' });
        },
        enableGlobalFilter: false,
    },
    {
        id: 'actions',
        cell: ({ row }) => actionsCell(row, UDropdownMenu, UButton, deleteModal, 'manga'),
    },
];
</script>

<template>
    <FiltersHeader tab="tag" v-model="globalFilter"></FiltersHeader>
    <UTable
        sticky
        :loading="fetching"
        :data="data"
        :columns="columns"
        :sorting="[{ id: 'name', desc: false }]"
        :global-filter="globalFilter"
        class="mt-6 h-[620px] flex-1"
    />
    <DeleteModal ref="deleteModal" delete-route="manga.destroy" delete-desc="este manga" delete-success-message="Manga borrado correctamente" />
</template>
