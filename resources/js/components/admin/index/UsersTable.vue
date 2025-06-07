<script setup lang="ts">
import DeleteModal from '@/components/admin/index/DeleteModal.vue';
import FiltersHeader from '@/components/admin/index/FiltersHeader.vue';
import { useFetchTable } from '@/composables/useFetchTable';
import { User } from '@/types';
import { actionsCell, sortableHeader, sortablePinnableHeader } from '@/utils/tableColumns';
import { TableColumn } from '@nuxt/ui';
import { h, ref, resolveComponent, useTemplateRef } from 'vue';

const UDropdownMenu = resolveComponent('UDropdownMenu');
const UButton = resolveComponent('UButton');
const UAvatar = resolveComponent('UAvatar');

const deleteModal = useTemplateRef('deleteModal');

//Ejecutar al crear el componente
const { fetchData, data, fetching } = useFetchTable<User>('user.index');
fetchData();

const globalFilter = ref('');

const columns: TableColumn<User>[] = [
    {
        accessorKey: 'id',
        header: 'Id',
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
    },
    {
        id: 'actions',
        cell: ({ row }) => actionsCell(row, UDropdownMenu, UButton, deleteModal, 'user'),
    },
];
</script>

<template>
    <FiltersHeader tab="user" v-model="globalFilter"></FiltersHeader>
    <UTable
        sticky
        :loading="fetching"
        :data="data"
        :columns="columns"
        :sorting="[{ id: 'name', desc: false }]"
        :global-filter="globalFilter"
        class="mt-6 h-[620px] flex-1"
    />
    <DeleteModal ref="deleteModal" delete-route="user.destroy" delete-desc="este usuario" delete-success-message="Usuario borrado correctamente" />
</template>
