<script setup lang="ts">
import DatePicker from '@/components/formComponents/DatePicker.vue';
import LabelWithCounter from '@/components/formComponents/labelWithCounter.vue';
import { useMangasStore } from '@/stores/mangasStore';
import { demographies, languageOptions, mangaFiltersInitialState, mangaSortable, readingDirections } from '@/utils/constants';
import { computed } from 'vue';

const store = useMangasStore();
const state = computed(() => store.state);

//Valores de los selects
const people = computed(() =>
    store.filters.people
        ? store.filters.people
              .map((person) => ({ label: `${person.name} ${person.surname ?? ''}`, value: person.id }))
              .sort((a, b) => a.label.localeCompare(b.label))
        : [],
);
const magazines = computed(() =>
    store.filters.magazines
        ? store.filters.magazines.map((magazine) => ({ label: magazine.name, value: magazine.id })).sort((a, b) => a.label.localeCompare(b.label))
        : [],
);
const tags = computed(() =>
    store.filters.tags
        ? store.filters.tags
              .map((tag) => ({ label: tag.name, value: tag.id, category: tag.type, color: tag.type === 'genre' ? 'primary' : 'secondary' }))
              .sort((a, b) => a.label.localeCompare(b.label))
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
    store.state = structuredClone(mangaFiltersInitialState);
    emit('filter');
};

const emit = defineEmits(['filter']);
</script>

<template>
    <div class="grid grid-cols-1 gap-x-8 gap-y-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <!--Tomos-->
        <div class="w-full">
            <p>Tomos</p>
            <UInput v-model="state.volumes" class="w-full" placeholder="Número de tomos" icon="lucide:book" />
        </div>
        <!--Fecha-->
        <div class="w-full">
            <p>Fecha</p>
            <DatePicker v-model="state.date" class="w-full" />
        </div>
        <!--Etiquetas-->
        <div class="w-full">
            <p>Etiquetas</p>
            <USelectMenu :items="tags" placeholder="Cualquier etiqueta" icon="lucide:tag" class="w-full" disabled />
        </div>
        <!--Orden-->
        <div class="w-full">
            <p>Orden</p>
            <USelect v-model="state.order" :items="mangaSortable" :icon="sortIcon" placeholder="Cualquier orden" class="w-full" />
        </div>
        <!--Autores-->
        <div class="w-full">
            <LabelWithCounter label="Autores" :size="state.people.length" />
            <USelectMenu v-model="state.people" :items="people" multiple placeholder="Cualquier autor" icon="lucide:users" class="w-full" />
        </div>
        <!--Idioma-->
        <div class="w-full">
            <p>Idioma</p>
            <USelectMenu v-model="state.language" :items="languageOptions" class="w-full" placeholder="Cualquier idioma" :search-input="false">
                <template #leading="{ modelValue, ui }">
                    <span v-if="modelValue" class="size-5 text-center">
                        {{ modelValue?.emoji }}
                    </span>
                    <UIcon v-else name="i-lucide-earth" :class="ui.leadingIcon()" />
                </template>
                <template #item-leading="{ item }">
                    <span class="size-5 text-center">
                        {{ item.emoji }}
                    </span>
                </template>
            </USelectMenu>
        </div>
        <!--Revistas-->
        <div class="w-full">
            <LabelWithCounter label="Revistas" :size="state.magazines.length" />
            <USelectMenu
                v-model="state.magazines"
                :items="magazines"
                multiple
                placeholder="Cualquier revista"
                icon="lucide:newspaper"
                class="w-full"
            />
        </div>
        <!--Demografía-->
        <div class="w-full">
            <LabelWithCounter label="Demografía" :size="state.demographies.length" />
            <USelect
                v-model="state.demographies"
                :items="demographies"
                multiple
                placeholder="Cualquier demografía"
                icon="lucide:square-user-round"
                class="w-full"
            />
        </div>
        <!--Finalizado-->
        <div class="w-full">
            <LabelWithCounter label="Estado" :size="state.finished.length" />
            <USelect
                v-model="state.finished"
                :items="[
                    { label: 'Completo', value: true },
                    { label: 'Incompleto', value: false },
                ]"
                multiple
                placeholder="Cualquier estado"
                icon="lucide:badge-check"
                class="w-full"
            />
        </div>
        <!--Dirección de lectura-->
        <div class="w-full">
            <LabelWithCounter label="Dirección de lectura" :size="state.readingDirection.length" />
            <USelect
                v-model="state.readingDirection"
                :items="readingDirections"
                multiple
                placeholder="Cualquier dirección"
                icon="lucide:arrow-left-right"
                class="w-full"
            />
        </div>
    </div>
    <USeparator class="my-3" />
    <div class="flex w-full justify-end gap-3">
        <UButton label="Restablecer filtros" variant="subtle" color="error" :disabled="!isFiltering" @click="resetFilters" />
        <UButton label="Buscar" icon="lucide:search" variant="solid" @click="emit('filter')" />
    </div>
    <!--etiquetas rehacer entero, con selector de incluir/excluir -->
    <!--Añadir manera de borrar seleccion de un select?-->
    <!--Hacer un formulario para que al pulsar enter, se envie la peticion-->
</template>
