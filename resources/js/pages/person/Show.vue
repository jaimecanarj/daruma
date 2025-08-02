<script setup lang="ts">
import DisplaySelector from '@/components/DisplaySelector.vue';
import MangasCard from '@/components/mangas/MangasCard.vue';
import MangasGrid from '@/components/mangas/MangasGrid.vue';
import PersonSkeleton from '@/components/skeletons/PersonSkeleton.vue';
import { Manga, Person } from '@/types';
import { mangaSortable } from '@/utils/constants';
import { Deferred, Head } from '@inertiajs/vue3';
import { parseDate } from '@internationalized/date';
import { computed, ref } from 'vue';

const props = defineProps<{ person?: Person & { mangas: Manga[] } }>();

const order = ref('dateDesc');
const display = ref<'grid' | 'list'>('grid');

const mangas = computed(() =>
    props.person?.mangas
        ? props.person.mangas.toSorted((a, b) => {
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
    <Deferred data="person">
        <template #fallback><PersonSkeleton :display="display" /> </template>
        <Head :title="`${person?.name} ${person?.surname}`" />
        <!--Cabecera-->
        <div class="absolute top-22 left-1/2 -translate-x-1/2 sm:left-auto sm:ml-8 sm:translate-0">
            <UIcon name="lucide:user" class="bg-elevated size-28 rounded-full p-2" />
        </div>
        <UCard variant="subtle" class="mt-14 flex justify-center rounded-xl sm:justify-start">
            <div class="mt-20 flex flex-col gap-4 sm:mt-0 sm:ml-40 lg:flex-row lg:items-center">
                <p class="line-clamp-2 text-center text-3xl font-bold sm:text-start">{{ person?.name }} {{ person?.surname }}</p>
                <p v-if="person?.kanjiName || person?.kanjiSurname" class="text-muted line-clamp-1 text-center text-2xl font-semibold sm:text-start">
                    「 {{ person?.kanjiName }} {{ person?.kanjiSurname }} 」
                </p>
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
