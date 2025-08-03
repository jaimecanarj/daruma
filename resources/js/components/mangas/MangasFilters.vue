<script setup lang="ts">
import BaseFilters from '@/components/BaseFilters.vue';
import DatePicker from '@/components/formComponents/DatePicker.vue';
import FilterSelect from '@/components/formComponents/FilterSelect.vue';
import { Magazine, MangaFilters, Person, Tag } from '@/types';
import { demographies, languages, mangaFiltersInitialState, mangaSorting, readingDirections, sortingIcons } from '@/utils/constants';
import { computed } from 'vue';

const props = defineProps<{ filterOptions?: { tags: Tag[]; people: Person[]; magazines: Magazine[] } }>();
const emit = defineEmits(['filter']);

const filters = defineModel<MangaFilters>({ default: mangaFiltersInitialState });

//Valores de los selects
const people = computed(() =>
    props.filterOptions?.people
        ? props.filterOptions.people
              .map((person) => ({ label: `${person.name} ${person.surname ?? ''}`, value: person.id }))
              .sort((a, b) => a.label.localeCompare(b.label))
        : [],
);

const magazines = computed(() =>
    props.filterOptions?.magazines
        ? props.filterOptions.magazines
              .map((magazine) => ({ label: magazine.name, value: magazine.id }))
              .sort((a, b) => a.label.localeCompare(b.label))
        : [],
);

const tags = computed(() =>
    props.filterOptions?.tags
        ? props.filterOptions.tags
              .map((tag) => ({ label: tag.name, value: tag.id, category: tag.type, color: tag.type === 'genre' ? 'primary' : 'secondary' }))
              .sort((a, b) => a.label.localeCompare(b.label))
        : [],
);

const sortIcon = computed(() => sortingIcons.find((item) => item.value === filters.value.order)?.icon);

const status = [
    { label: 'Completo', value: true },
    { label: 'Incompleto', value: false },
];
</script>

<template>
    <BaseFilters v-model="filters" :initial-state="mangaFiltersInitialState" @filter="emit('filter')">
        <!--Tomos-->
        <div class="w-full">
            <p class="m-0.5">Tomos</p>
            <UInput v-model="filters.volumes" class="w-full" placeholder="Número de tomos" icon="lucide:book" />
        </div>
        <!--Fecha-->
        <div class="w-full">
            <p class="m-0.5">Fecha</p>
            <DatePicker v-model="filters.date" class="w-full" />
        </div>
        <!--Etiquetas-->
        <div class="w-full">
            <p class="m-0.5">Etiquetas</p>
            <USelectMenu :items="tags" placeholder="Cualquier etiqueta" icon="lucide:tag" class="w-full" disabled />
        </div>
        <!--Orden-->
        <div class="w-full">
            <p class="m-0.5">Orden</p>
            <USelect v-model="filters.order" :items="mangaSorting" :icon="sortIcon" placeholder="Cualquier orden" class="w-full" />
        </div>
        <!--Autores-->
        <FilterSelect v-model="filters.people" :items="people" label="autor" icon="lucide:users" />
        <!--Idioma-->
        <FilterSelect v-model="filters.language" :items="languages" label="idioma" icon="lucide:earth" />
        <!--Revistas-->
        <FilterSelect v-model="filters.magazines" :items="magazines" label="revista" icon="lucide:newspaper" />
        <!--Demografía-->
        <FilterSelect v-model="filters.demographies" :items="demographies" label="demografía" icon="lucide:square-user-round" />
        <!--Finalizado-->
        <FilterSelect v-model="filters.finished" :items="status" label="estado" icon="lucide:badge-check" />
        <!--Dirección de lectura-->
        <FilterSelect v-model="filters.readingDirection" :items="readingDirections" label="dirección de lectura" icon="lucide:arrow-left-right" />
    </BaseFilters>
    <!--etiquetas rehacer entero, con selector de incluir/excluir -->
</template>
