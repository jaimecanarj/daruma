<script setup lang="ts">
import DisplaySelector from '@/components/DisplaySelector.vue';
import MangasCard from '@/components/mangas/MangasCard.vue';
import MangasGrid from '@/components/mangas/MangasGrid.vue';
import { Manga } from '@/types';
import { mangaSorting, sortingIcons } from '@/utils/constants';
import { parseDate } from '@internationalized/date';
import { computed, ref } from 'vue';

const props = defineProps<{ mangas?: Manga[]; title?: boolean }>();

const order = ref('dateDesc');
const display = defineModel<'grid' | 'list'>({ default: 'grid' });

const mangas = computed(() =>
    props?.mangas
        ? props?.mangas.toSorted((a, b) => {
              switch (order.value) {
                  case 'updateDesc':
                      return parseDate(b.updatedAt).compare(parseDate(a.updatedAt));
                  case 'updateAsc':
                      return parseDate(a.updatedAt).compare(parseDate(b.updatedAt));
                  case 'nameDesc':
                      return b.name.localeCompare(a.name);
                  case 'nameAsc':
                      return a.name.localeCompare(b.name);
                  case 'dateDesc':
                      return parseDate(b.startDate).compare(parseDate(a.startDate));

                  case 'dateAsc':
                      return parseDate(a.startDate).compare(parseDate(b.startDate));
                  case 'volumesDesc':
                      return b.tankoubon - a.tankoubon;
                  case 'volumesAsc':
                      return a.tankoubon - b.tankoubon;
                  default:
                      return a.name.localeCompare(b.name);
              }
          })
        : [],
);

const sortIcon = computed(() => sortingIcons.find((item) => item.value === order.value)?.icon);
</script>

<template>
    <div :class="[{ 'justify-end': !title }, 'mt-8 flex w-full flex-col justify-between gap-4 sm:flex-row']">
        <h2 v-if="title" class="text-3xl font-semibold">Mangas</h2>
        <div class="flex items-center gap-4">
            <USelect v-model="order" :items="mangaSorting" placeholder="Ordenar por" :icon="sortIcon" class="w-48" />
            <DisplaySelector v-model="display" />
        </div>
    </div>
    <USeparator class="my-5" />
    <MangasGrid v-if="display === 'grid'" :mangas="mangas" />
    <MangasCard v-else :mangas="mangas" />
    <div v-if="mangas?.length === 0" class="text-muted text-center text-xl">No hay resultados</div>
</template>
