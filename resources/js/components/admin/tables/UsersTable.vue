<script setup lang="ts">
import DeleteModal from '@/components/admin/DeleteModal.vue';
import CreateButton from '@/components/admin/filters/CreateButton.vue';
import UsersFilter from '@/components/admin/filters/UsersFilter.vue';
import FiltersHeader from '@/components/FiltersHeader.vue';
import { useFetchTable } from '@/composables/useFetchTable';
import { User } from '@/types';
import { actionsCell, sortableHeader, sortablePinnableHeader } from '@/utils/tableColumns';
import { parseDate } from '@internationalized/date';
import { TableColumn } from '@nuxt/ui';
import { h, ref, resolveComponent, useTemplateRef } from 'vue';

//Componentes
const UDropdownMenu = resolveComponent('UDropdownMenu');
const UButton = resolveComponent('UButton');
const UAvatar = resolveComponent('UAvatar');

//Modal
const deleteModal = useTemplateRef('deleteModal');

//Datos
const { fetchData, data, fetching } = useFetchTable<User>('user.index');
fetchData();

//Columnas
const columns: TableColumn<User>[] = [
    {
        accessorKey: 'id',
        header: ({ column }) => sortableHeader(column, 'Id', UButton),
        cell: ({ row }) => `#${row.getValue('id')}`,
    },
    {
        accessorKey: 'name',
        header: ({ column }) => sortablePinnableHeader(column, 'Nombre', UButton),
        cell: ({ row }) => {
            return h('div', { class: 'flex gap-1 items-center' }, [
                h(UAvatar, { src: '#', alt: row.getValue('name') }),
                h('p', [row.getValue('name')]),
            ]);
        },
    },
    {
        accessorKey: 'email',
        header: ({ column }) => sortableHeader(column, 'Email', UButton),
    },
    {
        accessorKey: 'createdAt',
        header: ({ column }) => sortableHeader(column, 'Registro', UButton),
        cell: ({ row }) => {
            return new Date(row.getValue('createdAt')).toLocaleString('es-ES', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
            });
        },
        enableGlobalFilter: false,
        filterFn: (row, columnId, filterValue) => {
            if (filterValue?.end) {
                const date = parseDate((row.getValue(columnId) as string).split('T')[0]);
                return date.compare(filterValue.start) >= 0 && date.compare(filterValue.end) <= 0;
            } else {
                return true;
            }
        },
    },
    {
        id: 'actions',
        cell: ({ row }) => actionsCell(row, UDropdownMenu, UButton, deleteModal, 'user'),
    },
];

//Filtros
const globalFilter = ref('');
const filters = [{ id: 'createdAt', value: null }];
const table = useTemplateRef('table');
</script>

<template>
    <FiltersHeader v-model="globalFilter" filters class="mt-8 flex justify-between">
        <template #rightSide><CreateButton tab="user" /></template>
        <UsersFilter v-model="table" />
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
        delete-route="user.destroy"
        delete-desc="este usuario"
        delete-success-message="Usuario borrado correctamente"
        @item-deleted="fetchData"
    />
</template>
