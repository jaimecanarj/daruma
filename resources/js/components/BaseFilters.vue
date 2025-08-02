<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    initialState: Record<string, any>;
}>();

const filters = defineModel<Record<string, any>>({ default: {} });

// Comprueba si hay algún filtro activo
const isFiltering = computed(() => {
    return Object.entries(filters.value).some(([key, value]) => {
        // No considerar order como filtro
        if (key === 'order') return false;

        // Para arrays, verificar si tienen elementos
        if (Array.isArray(value)) {
            return value.length > 0;
        }

        // Para objetos, verificar si tienen propiedades
        if (typeof value === 'object' && value !== null) {
            return Object.keys(value).length > 0;
        }

        // Para otros valores, verificar si son truthy
        return !!value;
    });
});

const resetFilters = () => {
    filters.value = structuredClone(props.initialState);
    emit('filter');
};

const emit = defineEmits(['filter']);
</script>

<template>
    <div class="grid grid-cols-1 gap-x-8 gap-y-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <slot />
    </div>
    <USeparator class="my-3" />
    <div class="flex w-full justify-end gap-3">
        <UButton label="Restablecer filtros" variant="subtle" color="error" :disabled="!isFiltering" @click="resetFilters" />
        <UButton label="Buscar" icon="lucide:search" variant="solid" @click="emit('filter')" />
    </div>
</template>
