<script setup lang="ts">
import DeleteModal from '@/components/admin/index/DeleteModal.vue';
import FiltersHeader from '@/components/admin/index/FiltersHeader.vue';
import TagsFilter from '@/components/admin/index/TagsFilter.vue';
import { useFetchTable } from '@/composables/useFetchTable';
import { Tag } from '@/types';
import { actionsCell, sortablePinnableHeader } from '@/utils/tableColumns';
import type { TableColumn } from '@nuxt/ui';
import { h, ref, resolveComponent, useTemplateRef } from 'vue';

//Componentes
const UBadge = resolveComponent('UBadge');
const UDropdownMenu = resolveComponent('UDropdownMenu');
const UButton = resolveComponent('UButton');

//Modal
const deleteModal = useTemplateRef('deleteModal');

//Datos
const { fetchData, data, fetching } = useFetchTable<Tag>('tag.index');
fetchData();

//Columnas
const columns: TableColumn<Tag>[] = [
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
        accessorKey: 'type',
        header: 'Tipo',
        cell: ({ row }) => {
            const color = {
                genre: 'primary' as const,
                theme: 'secondary' as const,
            }[row.getValue('type') as string];

            return h(UBadge, { class: 'capitalize', variant: 'subtle', color }, () => (row.getValue('type') === 'genre' ? 'Género' : 'Tema'));
        },
        enableGlobalFilter: false,
        filterFn: (row, columnId, filterValue) => {
            return !!filterValue.includes(row.getValue(columnId));
        },
    },
    {
        id: 'actions',
        cell: ({ row }) => actionsCell(row, UDropdownMenu, UButton, deleteModal, 'tag'),
    },
];

//Filtros
const globalFilter = ref('');
const filters = [{ id: 'type', value: ['theme', 'genre'] }];
const table = useTemplateRef('table');
</script>

<template>
    <FiltersHeader tab="tag" v-model="globalFilter">
        <TagsFilter v-model="table" />
    </FiltersHeader>
    <UTable
        ref="table"
        sticky
        :loading="fetching"
        :data="data"
        :columns="columns"
        :global-filter="globalFilter"
        :column-filters="filters"
        :sorting="[{ id: 'name', desc: false }]"
        class="mt-6 h-[620px] flex-1"
    />
    <DeleteModal ref="deleteModal" delete-route="tag.destroy" delete-desc="esta etiqueta" delete-success-message="Etiqueta borrada correctamente" />
</template>
