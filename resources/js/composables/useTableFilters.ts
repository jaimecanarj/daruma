// useTableFilters.ts
import { Table } from '@tanstack/table-core';
import { DateRange } from 'reka-ui';
import { ref, Ref, watch } from 'vue';

export type FilterValue = string | string[] | number | DateRange | null | undefined;

export interface FilterItem {
    id: string;
    value: FilterValue;
}

export function useTableFilters<T>(table: { value: { tableApi: Table<T> } | null | undefined }, starterFilters: FilterItem[]) {
    // Inicializar filtros basados en los valores de la tabla
    const filters = ref(
        starterFilters.map((filter) => ({
            id: filter.id,
            value: table.value?.tableApi?.getColumn(filter.id)?.getFilterValue(),
        })),
    ) as Ref<FilterItem[]>;

    // Función para actualizar filtros de tipo array de strings
    const setListFilter = (id: string, value: string) => {
        const filter = filters.value.find((item) => item.id === id);
        if (!filter) return;

        if (Array.isArray(filter.value)) {
            if (filter.value.includes(value)) {
                filter.value = filter.value.filter((item) => item !== value);
            } else {
                filter.value.push(value);
            }

            table.value?.tableApi?.getColumn(id)?.setFilterValue(filter.value);
        }
    };

    // Función para actualizar cualquier tipo de filtro
    const setFilter = (id: string) => {
        const filter = filters.value.find((item) => item.id === id);
        if (!filter) return;

        table.value?.tableApi?.getColumn(id)?.setFilterValue(filter.value);
    };

    // Función para restablecer filtros
    const resetFilters = () => {
        starterFilters.forEach((filter) => {
            table.value?.tableApi?.getColumn(filter.id)?.setFilterValue(filter.value);
            const existingFilter = filters.value.find((item) => item.id === filter.id);
            if (existingFilter) {
                existingFilter.value = filter.value;
            }
        });
    };

    // Observador para filtros de fecha
    watch(
        filters,
        () => {
            const dateFilters = filters.value.filter((filter) => filter.value && typeof filter.value === 'object' && 'start' in filter.value);

            dateFilters.forEach((filter) => {
                const dateRange = filter.value as DateRange;
                if (dateRange?.end) {
                    table.value?.tableApi?.getColumn(filter.id)?.setFilterValue(dateRange);
                } else if (!dateRange) {
                    table.value?.tableApi?.getColumn(filter.id)?.setFilterValue(null);
                }
            });
        },
        { deep: true },
    );

    return {
        filters,
        setListFilter,
        setFilter,
        resetFilters,
    };
}
