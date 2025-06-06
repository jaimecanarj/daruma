<script setup lang="ts">
import { Tag } from '@/types';
import { router, useForm } from '@inertiajs/vue3';
import type { TableColumn } from '@nuxt/ui';
import axios from 'axios';
import { computed, h, ref, resolveComponent } from 'vue';

const UBadge = resolveComponent('UBadge');
const UDropdownMenu = resolveComponent('UDropdownMenu');
const UButton = resolveComponent('UButton');

const toast = useToast();

//Ejecutar al crear el componente
const tags = ref<Tag[]>([]);

const tagsFiltered = computed(() => {
    return tags.value.filter((tag) => {
        if (tag.type === 'genre') {
            return typeFilter.value.genre;
        }
        if (tag.type === 'theme') {
            return typeFilter.value.theme;
        }
    });
});

const fetching = ref(false);

const fetchTags = () => {
    fetching.value = true;
    axios
        .get(route('tag.index'))
        .then((response) => {
            tags.value = response.data;
        })
        .finally(() => {
            fetching.value = false;
        });
};

fetchTags();

const columns: TableColumn<Tag>[] = [
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
        accessorKey: 'type',
        header: ({ column }) => {
            const isSorted = column.getIsSorted();

            return h(UButton, {
                color: 'neutral',
                variant: 'ghost',
                label: 'Tipo',
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
            const color = {
                genre: 'primary' as const,
                theme: 'secondary' as const,
            }[row.getValue('type') as string];

            return h(UBadge, { class: 'capitalize', variant: 'subtle', color }, () => (row.getValue('type') === 'genre' ? 'Género' : 'Tema'));
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
                                    router.visit(route('admin.edit', { tab: 'tag', id: row.getValue('id') }));
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

const deleteTag = () => {
    deleteForm.post(route('tag.destroy'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            deleteFormOpen.value = false;
            deleteForm.reset();
            toast.add({ title: 'Etiqueta borrada correctamente' });
            fetchTags();
        },
    });
};

const globalFilter = ref('');
const typeFilter = ref({ genre: true, theme: true } as { genre: boolean; theme: boolean });
</script>

<template>
    <div class="mt-8 flex justify-between">
        <div class="flex gap-4">
            <UInput class="w-60" v-model="globalFilter" placeholder="Buscar..." leading-icon="lucide:search" type="search" />
            <UButton
                :color="typeFilter.genre ? 'primary' : 'neutral'"
                variant="soft"
                size="sm"
                class="cursor-pointer"
                @click="typeFilter.genre = !typeFilter.genre"
                >Género</UButton
            >
            <UButton
                :color="typeFilter.theme ? 'secondary' : 'neutral'"
                variant="soft"
                size="sm"
                @click="typeFilter.theme = !typeFilter.theme"
                class="cursor-pointer"
                >Tema</UButton
            >
        </div>
        <ULink to="admin/create/tag">
            <UButton trailing-icon="lucide:square-plus" size="xl" class="cursor-pointer">Crear</UButton>
        </ULink>
    </div>
    <UTable
        sticky
        :loading="fetching"
        :data="tagsFiltered"
        :columns="columns"
        :global-filter="globalFilter"
        :sorting="[{ id: 'name', desc: false }]"
        class="mt-6 h-[620px] flex-1"
    />
    <UModal
        v-model:open="deleteFormOpen"
        title="Borrar etiqueta"
        description="Estás a punto de borrar la etiqueta, ¿estás seguro?"
        :ui="{ footer: 'justify-end' }"
    >
        <template #footer>
            <UButton label="Cancelar" color="neutral" variant="outline" @click="deleteFormOpen = false" />
            <UButton label="Borrar" color="error" :disabled="deleteForm.processing" @click="deleteTag" />
        </template>
    </UModal>
</template>
