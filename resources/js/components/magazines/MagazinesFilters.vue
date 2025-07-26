<script setup lang="ts">
import DatePicker from '@/components/DatePicker.vue';
import { useMagazinesStore } from '@/stores/magazinesStore';
import { demographies, frequencies, magazineFiltersInitialState, magazineSortable, mangaSortable } from '@/utils/constants';
import { computed } from 'vue';

const store = useMagazinesStore();
const state = computed(() => store.state);

defineModel('show-filters');

//Valores de los selects
const publishers = computed(() =>
    store.filters.publishers
        ? store.filters.publishers.map((publisher) => ({ label: publisher, value: publisher })).sort((a, b) => a.label.localeCompare(b.label))
        : [],
);

const sortIcon = computed(() => mangaSortable.find((item) => item.value === state.value.order)?.icon);
const isFiltering = computed(() => {
    return Object.entries(state.value).some(([key, value]) => {
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
    store.state = structuredClone(magazineFiltersInitialState);
    emit('filter', store.state);
};

const emit = defineEmits(['filter']);
</script>

<template>
    <UAccordion v-model="showFilters" :items="[{}]" :ui="{ trigger: 'p-0', trailingIcon: 'size-0' }">
        <template #content>
            <UCard variant="subtle" class="rounded-xl">
                <div class="grid grid-cols-1 gap-x-8 gap-y-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <!--Demografía-->
                    <div class="w-full">
                        <p>
                            Demografía
                            <span v-if="state.demographies.length > 0" class="text-primary ml-2 text-sm">+{{ state.demographies.length }}</span>
                        </p>
                        <USelect
                            v-model="state.demographies"
                            :items="demographies"
                            multiple
                            placeholder="Cualquier demografía"
                            icon="lucide:square-user-round"
                            class="w-full"
                        />
                    </div>
                    <!--Fecha-->
                    <div class="w-full">
                        <p>Fecha</p>
                        <DatePicker v-model="state.date" class="w-full" />
                    </div>
                    <!--Periodicidad-->
                    <div class="w-full">
                        <p>
                            Periodicidad
                            <span v-if="state.frequencies.length > 0" class="text-primary ml-2 text-sm">+{{ state.frequencies.length }}</span>
                        </p>
                        <USelect
                            v-model="state.frequencies"
                            :items="frequencies"
                            multiple
                            placeholder="Cualquier periodicidad"
                            icon="lucide:calendar-1"
                            class="w-full"
                        />
                    </div>
                    <!--Orden-->
                    <div class="w-full">
                        <p>Orden</p>
                        <USelect v-model="state.order" :items="magazineSortable" :icon="sortIcon" placeholder="Cualquier orden" class="w-full" />
                    </div>
                    <!--Editorial-->
                    <div class="w-full">
                        <p>
                            Editorial
                            <span v-if="state.publishers.length > 0" class="text-primary ml-2 text-sm">+{{ state.publishers.length }}</span>
                        </p>
                        <USelect
                            v-model="state.publishers"
                            :items="publishers"
                            multiple
                            placeholder="Cualquier editorial"
                            icon="lucide:building"
                            class="w-full"
                        />
                    </div>
                </div>
                <USeparator class="my-3" />
                <div class="flex w-full justify-end gap-3">
                    <UButton label="Restablecer filtros" variant="subtle" color="error" :disabled="!isFiltering" @click="resetFilters" />
                    <UButton label="Buscar" icon="lucide:search" variant="solid" @click="emit('filter', store.state)" />
                </div>
            </UCard>
        </template>
    </UAccordion>
</template>
