<script setup lang="ts">
import DeleteModal from '@/components/admin/index/DeleteModal.vue';
import FiltersHeader from '@/components/admin/index/FiltersHeader.vue';
import { useFetchTable } from '@/composables/useFetchTable';
import { Tag } from '@/types';
import { actionsCell, sortablePinnableHeader } from '@/utils/tableColumns';
import type { TableColumn } from '@nuxt/ui';
import { h, ref, resolveComponent, useTemplateRef } from 'vue';

const UBadge = resolveComponent('UBadge');
const UDropdownMenu = resolveComponent('UDropdownMenu');
const UButton = resolveComponent('UButton');

const deleteModal = useTemplateRef('deleteModal');

//Ejecutar al crear el componente
const { fetchData, data, fetching } = useFetchTable<Tag>('tag.index');
fetchData();

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
    },
    {
        id: 'actions',
        cell: ({ row }) => actionsCell(row, UDropdownMenu, UButton, deleteModal, 'tag'),
    },
];

const globalFilter = ref('');
</script>

<template>
    <FiltersHeader tab="tag" v-model="globalFilter"></FiltersHeader>
    <UTable
        sticky
        :loading="fetching"
        :data="data"
        :columns="columns"
        :global-filter="globalFilter"
        :sorting="[{ id: 'name', desc: false }]"
        class="mt-6 h-[620px] flex-1"
    />
    <DeleteModal ref="deleteModal" delete-route="tag.destroy" delete-desc="esta etiqueta" delete-success-message="Etiqueta borrada correctamente" />
</template>
