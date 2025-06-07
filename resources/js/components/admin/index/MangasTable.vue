<script setup lang="ts">
import { Manga } from '@/types';
import { router, useForm } from '@inertiajs/vue3';
import { TableColumn } from '@nuxt/ui';
import axios from 'axios';
import { h, ref, resolveComponent } from 'vue';

const toast = useToast();

const UDropdownMenu = resolveComponent('UDropdownMenu');
const UButton = resolveComponent('UButton');
const UIcon = resolveComponent('UIcon');

const mangas = ref<Manga[]>([]);

const fetching = ref(false);

const fetchMangas = () => {
    fetching.value = true;
    axios
        .get(route('manga.index'))
        .then((response) => {
            mangas.value = response.data;
        })
        .finally(() => {
            fetching.value = false;
        });
};

fetchMangas();

const globalFilter = ref('');

const deleteFormOpen = ref(false);
const deleteForm = useForm<{ id: number | undefined }>({ id: undefined });

const deleteManga = () => {
    deleteForm.post(route('manga.destroy'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            deleteFormOpen.value = false;
            deleteForm.reset();
            toast.add({ title: 'Manga borrado correctamente' });
            fetchMangas();
        },
    });
};

const columns: TableColumn<Manga>[] = [
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
        accessorKey: 'volumes',
        header: ({ column }) => {
            const isSorted = column.getIsSorted();

            return h(UButton, {
                color: 'neutral',
                variant: 'ghost',
                label: 'Tomos',
                icon: isSorted
                    ? isSorted === 'asc'
                        ? 'i-lucide-arrow-up-narrow-wide'
                        : 'i-lucide-arrow-down-wide-narrow'
                    : 'i-lucide-arrow-up-down',
                class: '-ml-2.5',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            });
        },
    },
    {
        id: 'date',
        accessorKey: 'startDate',
        header: ({ column }) => {
            const isSorted = column.getIsSorted();

            return h(UButton, {
                color: 'neutral',
                variant: 'ghost',
                label: 'Fechas',
                icon: isSorted
                    ? isSorted === 'asc'
                        ? 'i-lucide-arrow-up-narrow-wide'
                        : 'i-lucide-arrow-down-wide-narrow'
                    : 'i-lucide-arrow-up-down',
                class: '-ml-2.5',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            });
        },
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
    },
    {
        accessorKey: 'readingDirection',
        header: 'Dir. de lectura',
        cell: ({ row }) => {
            return h('p', [row.original.readingDirection === 'ltr' ? 'Izquierda' : 'Derecha']);
        },
    },
    {
        accessorKey: 'finished',
        header: '',
        cell: ({ row }) => {
            return h(UIcon, { class: 'size-5', name: row.original.finished ? 'i-lucide-badge-check' : 'i-lucide-badge' });
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
                                    router.visit(route('admin.edit', { tab: 'manga', id: row.getValue('id') }));
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
</script>

<template>
    <div class="mt-8 flex justify-between">
        <UInput class="w-60" v-model="globalFilter" placeholder="Buscar..." leading-icon="lucide:search" type="search" />
        <ULink to="admin/create/manga">
            <UButton trailing-icon="lucide:square-plus" size="xl" class="cursor-pointer">Crear</UButton>
        </ULink>
    </div>
    <UTable
        sticky
        :loading="fetching"
        :data="mangas"
        :columns="columns"
        :sorting="[{ id: 'name', desc: false }]"
        :global-filter="globalFilter"
        class="mt-6 h-[620px] flex-1"
    />
    <UModal
        v-model:open="deleteFormOpen"
        title="Borrar usuario"
        description="Estás a punto de borrar la persona, ¿estás seguro?"
        :ui="{ footer: 'justify-end' }"
    >
        <template #footer>
            <UButton label="Cancelar" color="neutral" variant="outline" @click="deleteFormOpen = false" />
            <UButton label="Borrar" color="error" :disabled="deleteForm.processing" @click="deleteManga" />
        </template>
    </UModal>
</template>
