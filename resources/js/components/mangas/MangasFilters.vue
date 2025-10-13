<script setup lang="ts">
import BaseFilters from '@/components/BaseFilters.vue';
import DatePicker from '@/components/formComponents/DatePicker.vue';
import FilterSelect from '@/components/formComponents/FilterSelect.vue';
import TagsFilter from '@/components/mangas/TagsFilter.vue';
import { Auth, Magazine, MangaFilters, Person, Tag } from '@/types';
import {
    demographies,
    languages,
    mangaFiltersInitialState,
    mangaSorting,
    mangaUserStatuses,
    readingDirections,
    sortingIcons,
} from '@/utils/constants';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{ filterOptions?: { tags: Tag[]; people: Person[]; magazines: Magazine[] } }>();
const emit = defineEmits(['filter']);

const page = usePage();

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

const sorting = computed(() => mangaSorting.map((item) => ({ ...item, icon: sortingIcons.find((icon) => icon.value === item.value)?.icon })));

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
            <UInput
                v-model="filters.volumes"
                class="w-full"
                placeholder="Número de tomos"
                icon="lucide:book"
                :ui="{ leadingIcon: filters.volumes ? 'text-default' : '' }"
            />
        </div>
        <!--Fecha-->
        <div class="w-full">
            <p class="m-0.5">Fecha</p>
            <DatePicker v-model="filters.date" filter class="w-full" />
        </div>
        <!--Etiquetas-->
        <TagsFilter v-model="filters.tags" :items="tags" />
        <!--Orden-->
        <div class="w-full">
            <p class="m-0.5">Orden</p>
            <USelect
                v-model="filters.order"
                :items="sorting"
                :icon="sortIcon"
                placeholder="Cualquier orden"
                class="w-full"
                :ui="{ leadingIcon: 'text-default' }"
            />
        </div>
        <!--Autores-->
        <FilterSelect v-model="filters.people" :items="people" label="autor" icon="lucide:users" searchable />
        <!--Idioma-->
        <FilterSelect v-model="filters.language" :items="languages" label="idioma" icon="lucide:earth" />
        <!--Revistas-->
        <FilterSelect v-model="filters.magazines" :items="magazines" label="revista" icon="lucide:newspaper" searchable />
        <!--Demografía-->
        <FilterSelect v-model="filters.demographies" :items="demographies" label="demografía" icon="lucide:square-user-round" />
        <!--Finalizado-->
        <FilterSelect v-model="filters.finished" :items="status" label="estado" icon="lucide:badge-check" />
        <!--Dirección de lectura-->
        <FilterSelect v-model="filters.readingDirection" :items="readingDirections" label="dirección de lectura" icon="lucide:arrow-left-right" />
        <FilterSelect
            v-if="(page.props.auth as Auth).user"
            v-model="filters.userStatus"
            :items="mangaUserStatuses"
            label="seguimiento"
            icon="lucide:bookmark"
        />
    </BaseFilters>
    <!--etiquetas rehacer entero, con selector de incluir/excluir -->
</template>
