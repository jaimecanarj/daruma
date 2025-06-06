<script setup lang="ts">
import { User } from '@/types';
import { router, useForm } from '@inertiajs/vue3';
import { TableColumn } from '@nuxt/ui';
import axios from 'axios';
import { h, ref, resolveComponent } from 'vue';

const toast = useToast();

const UDropdownMenu = resolveComponent('UDropdownMenu');
const UButton = resolveComponent('UButton');
const UAvatar = resolveComponent('UAvatar');

//Ejecutar al crear el componente
const users = ref<User[]>([]);

const fetching = ref(false);

const fetchUsers = () => {
    fetching.value = true;
    axios
        .get(route('user.index'))
        .then((response) => {
            users.value = response.data;
        })
        .finally(() => {
            fetching.value = false;
        });
};

fetchUsers();

const globalFilter = ref('');

const columns: TableColumn<User>[] = [
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
        cell: ({ row }) => {
            return h('div', { class: 'flex gap-1 items-center' }, [
                h(UAvatar, { src: '#', alt: row.getValue('name') }),
                h('p', [row.getValue('name')]),
            ]);
        },
    },
    {
        accessorKey: 'email',
        header: ({ column }) => {
            const isSorted = column.getIsSorted();

            return h(UButton, {
                color: 'neutral',
                variant: 'ghost',
                label: 'Email',
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
        accessorKey: 'createdAt',
        header: ({ column }) => {
            const isSorted = column.getIsSorted();

            return h(UButton, {
                color: 'neutral',
                variant: 'ghost',
                label: 'Registro',
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
            return new Date(row.getValue('createdAt')).toLocaleString('es-ES', {
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
                                    router.visit(route('admin.edit', { tab: 'user', id: row.getValue('id') }));
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

const deleteFormOpen = ref(false);
const deleteForm = useForm<{ id: number | undefined }>({ id: undefined });

const deleteUser = () => {
    deleteForm.post(route('user.destroy'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            deleteFormOpen.value = false;
            deleteForm.reset();
            toast.add({ title: 'Usuario borrado correctamente' });
            fetchUsers();
        },
    });
};
</script>

<template>
    <div class="mt-8 flex justify-between">
        <UInput class="w-60" v-model="globalFilter" placeholder="Buscar..." leading-icon="lucide:search" type="search" />
        <ULink to="admin/create/user">
            <UButton trailing-icon="lucide:square-plus" size="xl" class="cursor-pointer">Crear</UButton>
        </ULink>
    </div>
    <UTable
        sticky
        :loading="fetching"
        :data="users"
        :columns="columns"
        :sorting="[{ id: 'name', desc: false }]"
        :global-filter="globalFilter"
        class="mt-6 h-[620px] flex-1"
    />
    <UModal
        v-model:open="deleteFormOpen"
        title="Borrar usuario"
        description="Estás a punto de borrar el usuario, ¿estás seguro?"
        :ui="{ footer: 'justify-end' }"
    >
        <template #footer>
            <UButton label="Cancelar" color="neutral" variant="outline" @click="deleteFormOpen = false" />
            <UButton label="Borrar" color="error" :disabled="deleteForm.processing" @click="deleteUser" />
        </template>
    </UModal>
</template>
