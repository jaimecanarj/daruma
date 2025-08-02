<script setup lang="ts">
import DisplaySelector from '@/components/DisplaySelector.vue';
import MangasCard from '@/components/mangas/MangasCard.vue';
import MangasGrid from '@/components/mangas/MangasGrid.vue';
import MagazineSkeleton from '@/components/skeletons/MagazineSkeleton.vue';
import { Magazine, Manga } from '@/types';
import { frequencies, mangaSortable } from '@/utils/constants';
import { Deferred, Head } from '@inertiajs/vue3';
import { parseDate } from '@internationalized/date';
import { computed, ref } from 'vue';

const props = defineProps<{ magazine?: Magazine & { mangas: Manga[] } }>();

const order = ref('dateDesc');
const display = ref<'grid' | 'list'>('grid');

const mangas = computed(() =>
    props.magazine?.mangas
        ? props.magazine.mangas.toSorted((a, b) => {
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

const sortIcon = computed(() => mangaSortable.find((item) => item.value === order.value)?.icon);
</script>

<template>
    <Deferred data="magazine">
        <template #fallback><MagazineSkeleton :display="display" /> </template>
        <Head :title="magazine?.name" />
        <!--Cabecera-->
        <div class="absolute top-22 left-1/2 -translate-x-1/2 sm:left-auto sm:ml-8 sm:translate-0">
            <UIcon name="lucide:book-image" class="bg-elevated size-28 rounded-lg p-2" />
        </div>
        <UCard variant="subtle" class="mt-14 flex flex-col justify-center rounded-xl sm:justify-start">
            <div class="mt-20 flex flex-col gap-4 sm:mt-0 sm:ml-40 lg:flex-row lg:items-center">
                <p class="line-clamp-2 text-center text-3xl font-bold sm:text-start">{{ magazine?.name }}</p>
                <p class="text-muted line-clamp-1 text-center text-2xl sm:text-start">「 {{ magazine?.publisher }} 」</p>
            </div>
            <div class="mt-6 flex flex-col items-center gap-2 sm:ml-40 sm:flex-row sm:gap-4">
                <div class="flex items-center gap-1">
                    <UIcon name="lucide:square-user-round" class="size-5" />
                    <p class="capitalize">{{ magazine?.demography }}</p>
                </div>
                <div v-if="magazine?.date" class="flex items-center gap-1">
                    <UIcon name="lucide:calendar" class="size-5" />
                    <p>
                        {{
                            new Intl.DateTimeFormat('es-ES', {
                                day: 'numeric',
                                month: 'short',
                                year: 'numeric',
                            }).format(new Date(magazine.date))
                        }}
                    </p>
                </div>
                <div class="flex items-center gap-1">
                    <UIcon name="lucide:calendar-1" class="size-5" />
                    <p>{{ frequencies.find((frequency) => frequency.value === magazine?.frequency)?.label }}</p>
                </div>
            </div>
        </UCard>
        <!--Mangas-->
        <div class="mt-8 flex flex-col justify-between gap-4 sm:flex-row">
            <h2 class="text-3xl font-semibold">Mangas</h2>
            <div class="flex items-center gap-4">
                <USelect v-model="order" :items="mangaSortable" placeholder="Ordenar por" :icon="sortIcon" class="w-48" />
                <DisplaySelector v-model="display" />
            </div>
        </div>
        <USeparator class="my-5" />
        <MangasGrid v-if="display === 'grid'" :mangas="mangas" />
        <MangasCard v-else :mangas="mangas" />
        <div v-if="mangas?.length === 0" class="text-muted text-center text-xl">No hay resultados</div>
    </Deferred>
</template>
