<script setup lang="ts">
import BaseFilters from '@/components/BaseFilters.vue';
import DatePicker from '@/components/formComponents/DatePicker.vue';
import FilterSelect from '@/components/formComponents/FilterSelect.vue';
import { MagazineFilters } from '@/types';
import { demographies, frequencies, magazineFiltersInitialState, magazineSortable } from '@/utils/constants';
import { computed } from 'vue';

const props = defineProps<{ filterOptions?: { publishers: string[] } }>();
const emit = defineEmits(['filter']);

const filters = defineModel<MagazineFilters>({ default: magazineFiltersInitialState });

//Valores de los selects
const publishers = computed(() => (props.filterOptions?.publishers ? props.filterOptions.publishers.toSorted((a, b) => a.localeCompare(b)) : []));

const sortIcon = computed(() => magazineSortable.find((item) => item.value === filters.value.order)?.icon);
</script>

<template>
    <BaseFilters v-model="filters" :initial-state="magazineFiltersInitialState" @filter="emit('filter')">
        <!--Demografía-->
        <FilterSelect v-model="filters.demographies" :items="demographies" label="demografía" icon="lucide:square-user-round" />
        <!--Fecha-->
        <div class="w-full">
            <p class="m-0.5">Fecha</p>
            <DatePicker v-model="filters.date" class="w-full" />
        </div>
        <!--Periodicidad-->
        <FilterSelect v-model="filters.frequencies" :items="frequencies" label="periodicidad" icon="lucide:calendar-1" />
        <!--Orden-->
        <div class="w-full">
            <p class="m-0.5">Orden</p>
            <USelect v-model="filters.order" :items="magazineSortable" :icon="sortIcon" placeholder="Cualquier orden" class="w-full" />
        </div>
        <!--Editorial-->
        <FilterSelect v-model="filters.publishers" :items="publishers" label="editorial" icon="lucide:building" />
    </BaseFilters>
</template>
