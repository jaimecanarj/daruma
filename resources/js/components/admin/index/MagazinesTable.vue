<script setup lang="ts">
import { Magazine } from '@/types';
import { router, useForm } from '@inertiajs/vue3';
import { TableColumn } from '@nuxt/ui';
import axios from 'axios';
import { h, ref, resolveComponent } from 'vue';

const toast = useToast();

const UDropdownMenu = resolveComponent('UDropdownMenu');
const UButton = resolveComponent('UButton');

const magazines = ref<Magazine[]>([]);

const fetching = ref(false);

const fetchMagazines = () => {
    fetching.value = true;
    axios
        .get(route('magazine.index'))
        .then((response) => {
            magazines.value = response.data;
        })
        .finally(() => {
            fetching.value = false;
        });
};

fetchMagazines();

const globalFilter = ref('');

const deleteFormOpen = ref(false);
const deleteForm = useForm<{ id: number | undefined }>({ id: undefined });

const columns: TableColumn<Magazine>[] = [
    {
        accessorKey: 'id',
        header: 'Id',
        cell: ({ row }) => `#${row.getValue('id')}`,
    },
    {
        accessorKey: 'name',
        header: ({ column }) => {
            const isSorted = column.getIsSorted();
            const isPinned = column.getIsPinned();

            return h(
                'div',
                { class: 'flex gap-1' },

                [
                    h(UButton, {
                        color: 'neutral',
                        variant: 'ghost',
                        label: 'Nombre',
                        icon: isSorted
                            ? isSorted === 'asc'
                                ? 'i-lucide-arrow-up-narrow-wide'
                                : 'i-lucide-arrow-down-wide-narrow'
                            : 'i-lucide-arrow-up-down',
                        class: '-ml-2.5',
                        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
                    }),
                    h(UButton, {
                        color: 'neutral',
                        variant: 'ghost',
                        size: 'icon',
                        icon: isPinned ? 'i-lucide-pin-off' : 'i-lucide-pin',
                        class: 'px-2',
                        onClick() {
                            column.pin(isPinned === 'left' ? false : 'left');
                        },
                    }),
                ],
            );
        },
    },
    {
        accessorKey: 'publisher',
        header: ({ column }) => {
            const isSorted = column.getIsSorted();

            return h(UButton, {
                color: 'neutral',
                variant: 'ghost',
                label: 'Editorial',
                icon: isSorted
                    ? isSorted === 'asc'
                        ? 'i-lucide-arrow-up-narrow-wide'
                        : 'i-lucide-arrow-down-wide-narrow'
                    : 'i-lucide-arrow-up-down',
                class: '-mx-2.5',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            });
        },
    },
    {
        accessorKey: 'demography',
        header: ({ column }) => {
            const isSorted = column.getIsSorted();

            return h(UButton, {
                color: 'neutral',
                variant: 'ghost',
                label: 'Demografía',
                icon: isSorted
                    ? isSorted === 'asc'
                        ? 'i-lucide-arrow-up-narrow-wide'
                        : 'i-lucide-arrow-down-wide-narrow'
                    : 'i-lucide-arrow-up-down',
                class: '-mx-2.5',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            });
        },
        cell: ({ row }) => {
            return h('p', [(row.getValue('demography') as string).charAt(0).toUpperCase(), (row.getValue('demography') as string).slice(1)]);
        },
    },
    {
        accessorKey: 'frequency',
        header: ({ column }) => {
            const isSorted = column.getIsSorted();

            return h(UButton, {
                color: 'neutral',
                variant: 'ghost',
                label: 'Periodicidad',
                icon: isSorted
                    ? isSorted === 'asc'
                        ? 'i-lucide-arrow-up-narrow-wide'
                        : 'i-lucide-arrow-down-wide-narrow'
                    : 'i-lucide-arrow-up-down',
                class: '-mx-2.5',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            });
        },
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
    },
    {
        accessorKey: 'date',
        header: ({ column }) => {
            const isSorted = column.getIsSorted();

            return h(UButton, {
                color: 'neutral',
                variant: 'ghost',
                label: 'Fecha',
                icon: isSorted
                    ? isSorted === 'asc'
                        ? 'i-lucide-arrow-up-narrow-wide'
                        : 'i-lucide-arrow-down-wide-narrow'
                    : 'i-lucide-arrow-up-down',
                class: '-mx-2.5',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            });
        },
        cell: ({ row }) => {
            return new Date(row.getValue('date')).toLocaleString('es-ES', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
            });
        },
    },
    {
        id: 'actions',
        cell: ({ row }) => {
            return h(
                'div',
                { class: 'text-right' },
                h(
                    UDropdownMenu,
                    {
                        content: {
                            align: 'end',
                        },
                        items: [
                            {
                                label: 'Editar',
                                icon: 'lucide:square-pen',
                                onSelect() {
                                    router.visit(route('admin.edit', { tab: 'magazine', id: row.getValue('id') }));
                                },
                            },
                            {
                                type: 'separator',
                            },
                            {
                                label: 'Eliminar',
                                icon: 'lucide:trash-2',
                                color: 'error',
                                onSelect() {
                                    deleteFormOpen.value = true;
                                    deleteForm.id = row.getValue('id');
                                },
                            },
                        ],
                        'aria-label': 'Actions dropdown',
                    },
                    () =>
                        h(UButton, {
                            icon: 'i-lucide-ellipsis-vertical',
                            color: 'neutral',
                            variant: 'ghost',
                            class: 'ml-auto',
                            'aria-label': 'Actions dropdown',
                        }),
                ),
            );
        },
    },
];

const deleteMagazine = () => {
    deleteForm.post(route('magazine.destroy'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            deleteFormOpen.value = false;
            deleteForm.reset();
            toast.add({ title: 'Revista borrada correctamente' });
            fetchMagazines();
        },
    });
};
</script>

<template>
    <div class="mt-8 flex justify-between">
        <UInput class="w-60" v-model="globalFilter" placeholder="Buscar..." leading-icon="lucide:search" type="search" />
        <ULink to="admin/create/magazine">
            <UButton trailing-icon="lucide:square-plus" size="xl" class="cursor-pointer">Crear</UButton>
        </ULink>
    </div>
    <UTable
        sticky
        :loading="fetching"
        :data="magazines"
        :columns="columns"
        :sorting="[{ id: 'name', desc: false }]"
        :global-filter="globalFilter"
        class="mt-6 h-[620px] flex-1"
    />
    <UModal
        v-model:open="deleteFormOpen"
        title="Borrar usuario"
        description="Estás a punto de borrar la revista, ¿estás seguro?"
        :ui="{ footer: 'justify-end' }"
    >
        <template #footer>
            <UButton label="Cancelar" color="neutral" variant="outline" @click="deleteFormOpen = false" />
            <UButton label="Borrar" color="error" :disabled="deleteForm.processing" @click="deleteMagazine" />
        </template>
    </UModal>
</template>
