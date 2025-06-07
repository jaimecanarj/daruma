<script setup lang="ts">
import { Person } from '@/types';
import { router, useForm } from '@inertiajs/vue3';
import { TableColumn } from '@nuxt/ui';
import { FilterFnOption } from '@tanstack/table-core';
import axios from 'axios';
import { h, ref, resolveComponent } from 'vue';

const toast = useToast();

const UDropdownMenu = resolveComponent('UDropdownMenu');
const UButton = resolveComponent('UButton');

const people = ref<Person[]>([]);

const fetching = ref(false);

const fetchPeople = () => {
    fetching.value = true;
    axios
        .get(route('person.index'))
        .then((response) => {
            people.value = response.data;
        })
        .finally(() => {
            fetching.value = false;
        });
};

fetchPeople();

const globalFilter = ref('');

const deleteFormOpen = ref(false);
const deleteForm = useForm<{ id: number | undefined }>({ id: undefined });

const columns: TableColumn<Person>[] = [
    {
        accessorKey: 'id',
        header: 'Id',
        cell: ({ row }) => `#${row.getValue('id')}`,
    },
    {
        id: 'name',
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
        cell: ({ row }) => {
            const kanjiName = row.original.kanjiName ? ` ( ${row.original.kanjiName} )` : '';
            return h('p', [row.original.name, kanjiName]);
        },
    },
    {
        id: 'surname',
        header: ({ column }) => {
            const isSorted = column.getIsSorted();

            return h(UButton, {
                color: 'neutral',
                variant: 'ghost',
                label: 'Apellido',
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
            const kanjiSurname = row.original.kanjiSurname ? ` ( ${row.original.kanjiSurname} )` : '';
            return h('p', [row.original.surname, kanjiSurname]);
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
                                    router.visit(route('admin.edit', { tab: 'person', id: row.getValue('id') }));
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

const deletePerson = () => {
    deleteForm.post(route('person.destroy'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            deleteFormOpen.value = false;
            deleteForm.reset();
            toast.add({ title: 'Persona borrada correctamente' });
            fetchPeople();
        },
    });
};

const globalFilterFn: FilterFnOption<Person> = (row, columnId, filterValue) => {
    // Si no hay valor de filtro, no filtrar
    if (!filterValue) return true;

    const searchTerm = String(filterValue).toLowerCase();

    // Accede a los valores originales de la fila para evitar problemas
    const name = String(row.original.name || '').toLowerCase();
    const kanjiName = String(row.original.kanjiName || '').toLowerCase();
    const surname = String(row.original.surname || '').toLowerCase();
    const kanjiSurname = String(row.original.kanjiSurname || '').toLowerCase();

    // Devuelve true si alguno de los campos coincide
    return name.includes(searchTerm) || kanjiName.includes(searchTerm) || surname.includes(searchTerm) || kanjiSurname.includes(searchTerm);
};
</script>

<template>
    <div class="mt-8 flex justify-between">
        <UInput class="w-60" v-model="globalFilter" placeholder="Buscar..." leading-icon="lucide:search" type="search" />
        <ULink to="admin/create/person">
            <UButton trailing-icon="lucide:square-plus" size="xl" class="cursor-pointer">Crear</UButton>
        </ULink>
    </div>
    <UTable
        sticky
        :loading="fetching"
        :data="people"
        :columns="columns"
        :sorting="[{ id: 'name', desc: false }]"
        :global-filter="globalFilter"
        :global-filter-options="{ globalFilterFn: globalFilterFn }"
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
            <UButton label="Borrar" color="error" :disabled="deleteForm.processing" @click="deletePerson" />
        </template>
    </UModal>
</template>
