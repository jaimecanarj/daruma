import { router } from '@inertiajs/vue3';
import type { Column, Row } from '@tanstack/vue-table';
import { h } from 'vue';

export const sortableHeader = <T>(column: Column<T>, label: string, UButton: any) => {
    const isSorted = column.getIsSorted();

    return h(UButton, {
        color: 'neutral',
        variant: 'ghost',
        label,
        icon: isSorted ? (isSorted === 'asc' ? 'i-lucide-arrow-up-narrow-wide' : 'i-lucide-arrow-down-wide-narrow') : 'i-lucide-arrow-up-down',
        class: '-mx-2.5 text-highlighted font-semibold',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
    });
};

export const sortablePinnableHeader = <T>(column: Column<T>, label: string, UButton: any) => {
    const isSorted = column.getIsSorted();
    const isPinned = column.getIsPinned();

    return h(
        'div',
        { class: 'flex gap-1' },

        [
            h(UButton, {
                color: 'neutral',
                variant: 'ghost',
                label,
                icon: isSorted
                    ? isSorted === 'asc'
                        ? 'i-lucide-arrow-up-narrow-wide'
                        : 'i-lucide-arrow-down-wide-narrow'
                    : 'i-lucide-arrow-up-down',
                class: '-ml-2.5 text-highlighted font-semibold',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            }),
            h(UButton, {
                color: 'neutral',
                variant: 'ghost',
                size: 'icon',
                icon: isPinned ? 'i-lucide-pin-off' : 'i-lucide-pin',
                class: 'px-2 text-highlighted font-semibold',
                onClick() {
                    column.pin(isPinned === 'left' ? false : 'left');
                },
            }),
        ],
    );
};

export const actionsCell = <T>(row: Row<T>, UDropdownMenu: any, UButton: any, deleteModal: any, tab: string) => {
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
                            router.visit(route('admin.edit', { tab, id: row.getValue('id') }));
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
                            deleteModal.value?.toggleDeleteForm();
                            deleteModal.value?.setDeleteFormId(row.getValue('id'));
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
};
