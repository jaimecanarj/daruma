<script setup lang="ts">
import DeleteModal from '@/components/admin/index/DeleteModal.vue';
import FiltersHeader from '@/components/admin/index/FiltersHeader.vue';
import { useFetchTable } from '@/composables/useFetchTable';
import { Magazine } from '@/types';
import { actionsCell, sortableHeader, sortablePinnableHeader } from '@/utils/tableColumns';
import { TableColumn } from '@nuxt/ui';
import { h, ref, resolveComponent, useTemplateRef } from 'vue';

const UDropdownMenu = resolveComponent('UDropdownMenu');
const UButton = resolveComponent('UButton');

const deleteModal = useTemplateRef('deleteModal');

const { fetchData, data, fetching } = useFetchTable<Magazine>('magazine.index');
fetchData();

const globalFilter = ref('');

const columns: TableColumn<Magazine>[] = [
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
    },
    {
        id: 'actions',
        cell: ({ row }) => actionsCell(row, UDropdownMenu, UButton, deleteModal, 'magazine'),
    },
];
</script>

<template>
    <FiltersHeader tab="magazine" v-model="globalFilter"></FiltersHeader>
    <UTable
        sticky
        :loading="fetching"
        :data="data"
        :columns="columns"
        :sorting="[{ id: 'name', desc: false }]"
        :global-filter="globalFilter"
        class="mt-6 h-[620px] flex-1"
    />
    <DeleteModal
        ref="deleteModal"
        delete-route="magazine.destroy"
        delete-desc="esta revista"
        delete-success-message="Revista borrada correctamente"
    />
</template>
