<script setup lang="ts">
import { Manga } from '@/types';
import { router } from '@inertiajs/vue3';
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core';

const activeBreakpoint = useBreakpoints(breakpointsTailwind).active();
const fullYear = useBreakpoints(breakpointsTailwind).greaterOrEqual('lg');

defineProps<{ mangas?: Manga[] }>();
</script>

<template>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <UCard v-for="manga in mangas" :key="manga.id" variant="subtle" class="shadow-md" :ui="{ body: 'p-2 sm:p-3' }">
            <div class="flex gap-2 overflow-hidden">
                <!--Imagen-->
                <div class="max-w-[150px] basis-1/4">
                    <img
                        :src="`/storage/${manga.cover}`"
                        class="aspect-[1/1.4142] w-full cursor-pointer rounded-sm"
                        alt="Portada de manga"
                        @click="router.visit(route('mangas.show', manga.id))"
                    />
                </div>
                <!--Datos-->
                <div class="flex min-w-0 basis-3/4 flex-col gap-1 px-1">
                    <!--Nombre-->
                    <div class="flex w-full items-center">
                        <h3 class="min-w-0 flex-1 cursor-pointer truncate text-lg font-bold" @click="router.visit(route('mangas.show', manga.id))">
                            {{ manga.name }}
                        </h3>
                        <UIcon :name="manga.finished ? 'lucide:badge-check' : 'lucide:badge'" class="ml-4 size-5 flex-shrink-0" />
                    </div>
                    <!--Fechas, tomos, capítulos-->
                    <div class="flex gap-2 text-sm lg:text-base">
                        <p v-if="manga.startDate !== undefined">
                            <UIcon name="lucide:calendar" class="mr-0.5 mb-1 inline" /> {{ manga.startDate.slice(fullYear ? 0 : 2, 4) }} -
                            <span v-if="manga.endDate">{{ manga.endDate.slice(fullYear ? 0 : 2, 4) }}</span>
                        </p>
                        <p v-if="manga.tankoubon">
                            <UIcon name="lucide:library-big" class="mr-0.5 mb-1 inline" /> {{ manga.tankoubon }}
                            {{ activeBreakpoint ? (manga.tankoubon > 1 ? 'vols' : 'vol') : '' }}
                        </p>
                        <p v-if="manga.chapters">
                            <UIcon name="lucide:book-open-text" class="mr-0.5 mb-1 inline" />{{ manga.chapters }}
                            {{ activeBreakpoint ? (manga.chapters > 1 ? 'caps' : 'cap') : '' }}
                        </p>
                    </div>
                    <!--Etiquetas y sinopsis-->
                    <div class="flex h-[132px] flex-col sm:h-44">
                        <div class="flex max-h-8 flex-shrink-0 flex-wrap items-center gap-x-2 overflow-hidden">
                            <p v-for="tag of manga.tags" :key="tag.id" class="text-[0.6666rem] font-bold uppercase">
                                {{ tag.name }}
                            </p>
                        </div>
                        <div class="relative flex-grow overflow-hidden text-sm">
                            <p>{{ manga.sinopsis }}</p>
                            <!--Colores de from escritos en hexadecimal pues es imposible obtener los colores relativos-->
                            <div
                                class="pointer-events-none absolute bottom-0 left-0 h-5 w-full bg-gradient-to-t from-[#fafaf9] to-transparent dark:from-[#231f1d]"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </UCard>
    </div>
</template>
