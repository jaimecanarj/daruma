<script setup lang="ts">
import MangasDisplay from '@/components/MangasDisplay.vue';
import VolumesDisplay from '@/components/VolumesDisplay.vue';
import { Manga } from '@/types';
import { authorJobs, languages } from '@/utils/constants';
import { Deferred, Head } from '@inertiajs/vue3';
import { DateFormatter } from '@internationalized/date';
import { computed, ref } from 'vue';

const props = defineProps<{ manga?: Manga }>();

const display = ref<'grid' | 'list'>('grid');

const mangaTabs = computed(() => {
    const tabs = [
        {
            label: 'Tomos',
            icon: 'lucide:book',
            slot: 'volumes',
        },
    ];
    if (props?.manga?.mangasRelated?.length) {
        tabs.push({ label: 'Mangas relacionados', icon: 'lucide:book-copy', slot: 'related' });
    }
    return tabs;
});

const dates = computed(() => {
    if (props?.manga) {
        const df = new DateFormatter('es-ES', { dateStyle: 'medium' });
        const startDate = df.format(new Date(props.manga?.startDate));
        const endDate = props.manga?.endDate ? df.format(new Date(props.manga?.endDate)) : 'Actualidad';

        // Función para formatear el año
        const formatYear = (dateString: string) => {
            return dateString.replace(/(\d{4})/g, '<span class="font-semibold text-lg">$1</span>');
        };

        const formattedStartDate = formatYear(startDate);
        const formattedEndDate = endDate === '<span class="font-semibold text-lg">Actualidad</span>' ? endDate : formatYear(endDate);

        return `${formattedStartDate} - ${formattedEndDate}`;
    } else {
        return null;
    }
});

const readingDirection = computed(() => {
    return props.manga?.readingDirection === 'ltr' ? 'lucide:panel-left-open' : 'lucide:panel-right-open';
});

const language = computed(() => {
    return languages.find((item) => item.value === props.manga?.language)?.emoji;
});

const status = computed(() => {
    return props.manga?.finished ? 'lucide:badge-check' : 'lucide:badge';
});

const redirect = (web: string) => {
    switch (web) {
        case 'mal':
            window.location.href = `https://myanimelist.net/manga/${props.manga?.mal}`;
            break;
        case 'listadoManga':
            window.location.href = `https://listadomanga.es/coleccion.php?id=${props.manga?.listadoManga}`;
            break;
    }
};

const covers = computed(() => {
    if (props.manga?.volumesData) {
        const covers = [];
        for (const volume of props.manga.volumesData) {
            covers.push(volume.cover);
        }
        return covers;
    } else if (props.manga?.cover) {
        return [props.manga.cover];
    } else {
        return [];
    }
});
</script>

<template>
    <Deferred data="manga">
        <template #fallback> </template>
        <Head :title="manga?.name" />
        <!--Cabecera-->
        <UCard variant="subtle" class="mt-20 overflow-visible rounded-xl sm:mt-16">
            <div class="flex flex-col gap-4 sm:flex-row">
                <!--Portada-->
                <div class="mx-auto -mt-16 w-52 sm:mx-0 sm:-mt-14 sm:w-64 lg:w-80">
                    <UModal :ui="{ content: 'max-w-[500px] bg-transparent overflow-visible ring-0' }">
                        <img :src="`/storage/${manga?.cover}`" class="aspect-[1/1.4142] cursor-pointer rounded-lg" alt="Portada" />
                        <template #content>
                            <UCarousel v-slot="{ item }" arrows :items="covers" class="mx-auto w-full">
                                <img :src="`/storage/${item}`" width="500" class="aspect-[1/1.4142] rounded-lg" />
                            </UCarousel>
                        </template>
                    </UModal>
                    <!--Dirección, estado, idioma, enlaces-->
                    <div class="mt-6 hidden flex-col gap-1 px-1 sm:flex">
                        <div class="flex flex-col items-center justify-center gap-2 md:flex-row lg:gap-3">
                            <div class="flex items-center gap-1 md:gap-2 lg:gap-3">
                                <UIcon :name="readingDirection" class="size-6" />
                                <UIcon :name="status" :class="[{ 'text-primary': status }, 'size-6']" />
                                <USeparator orientation="vertical" class="bg-accented h-6 w-0.5" />
                                <div class="text-xl">{{ language }}</div>
                                <USeparator orientation="vertical" class="bg-accented hidden h-6 w-0.5 md:block" />
                            </div>
                            <div class="flex gap-2 lg:gap-3">
                                <img v-if="manga?.mal" src="/mal.svg" class="size-6 cursor-pointer" @click="redirect('mal')" />
                                <img
                                    v-if="manga?.listadoManga"
                                    src="/listadoManga.svg"
                                    class="size-6 cursor-pointer"
                                    @click="redirect('listadoManga')"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <!--Datos-->
                <div class="w-full">
                    <div class="flex flex-col items-center gap-2 sm:items-start">
                        <!--Nombre-->
                        <p class="text-center text-2xl leading-normal font-bold sm:text-start sm:text-3xl lg:text-4xl">{{ manga?.name }}</p>
                        <div class="ml-1 flex flex-col flex-wrap justify-around gap-2 md:flex-row md:justify-start">
                            <!--Fecha-->
                            <div class="flex items-center gap-1">
                                <UIcon name="lucide:calendar-days" class="size-5" />
                                <p v-html="dates" />
                            </div>
                            <div class="flex justify-around gap-2 sm:justify-start">
                                <!--Tomos-->
                                <template v-if="manga?.volumes">
                                    <USeparator orientation="vertical" class="bg-accented hidden h-6 md:block" size="sm" />
                                    <div class="flex items-center gap-1">
                                        <UIcon name="lucide:library-big" class="size-5" />
                                        <span class="text-lg font-semibold">{{ manga?.tankoubon }}</span>
                                        <span class="text-lg">({{ manga?.volumes }})</span> tomos
                                    </div>
                                </template>
                                <!--Capítulos-->
                                <template v-if="manga?.chapters">
                                    <USeparator orientation="vertical" class="bg-accented h-6" size="sm" />
                                    <div class="flex items-center gap-1">
                                        <UIcon name="lucide:book-open-text" class="size-5" /><span class="text-lg font-semibold">{{
                                            manga?.chapters
                                        }}</span>
                                        capítulos
                                    </div>
                                </template>
                            </div>
                        </div>
                        <!--Etiquetas-->
                        <div class="flex flex-wrap items-center justify-center gap-1 sm:justify-start">
                            <UBadge
                                v-for="tag of manga?.tags"
                                :key="tag.id"
                                :label="tag.name"
                                :color="tag.type === 'genre' ? 'primary' : 'secondary'"
                                variant="soft"
                                class="select-none"
                            />
                        </div>
                        <!--Sinopsis-->
                        <p class="mt-2 line-clamp-6 text-justify sm:text-start">{{ manga?.sinopsis }}</p>
                        <USeparator class="bg-accented" size="md" />
                    </div>
                    <div class="mt-3 flex flex-col gap-2">
                        <!--Nombres-->
                        <div v-if="manga?.names" class="flex flex-wrap items-center gap-1">
                            Nombres alternativos:
                            <div v-for="name of manga?.names" :key="name.id" class="text-sm">
                                <UBadge variant="outline" color="neutral" class="text-sm">{{ name.name }}</UBadge>
                            </div>
                        </div>
                        <!--Autores-->
                        <div class="flex flex-wrap items-center gap-2">
                            Autores:
                            <div v-for="(author, index) of manga?.people" :key="author.id" class="flex items-center gap-1">
                                <ULink :to="`/people/${author.id}`" class="text-nowrap">{{ author.name }} {{ author.surname }}</ULink>
                                <p class="text-sm text-nowrap">
                                    ({{ authorJobs.find((job) => job.value === author.pivot.job)?.label }})
                                    {{ index !== (manga?.people?.length ?? 0) - 1 ? ',' : '' }}
                                </p>
                            </div>
                        </div>
                        <!--Revista-->
                        <div class="flex items-center gap-1">
                            Revista:
                            <ULink :to="`/magazines/${manga?.magazine?.id}`">{{ manga?.magazine?.name }}</ULink>
                        </div>
                        <!--Dirección, estado, idioma, enlaces-->
                        <div class="mt-6 flex flex-col gap-1 px-1 sm:hidden">
                            <div class="flex items-center justify-center gap-3">
                                <UIcon :name="readingDirection" class="size-6" />
                                <UIcon :name="status" :class="[{ 'text-primary': status }, 'size-6']" />
                                <USeparator orientation="vertical" class="bg-accented h-6 w-0.5" />
                                <div class="text-xl">{{ language }}</div>
                                <USeparator orientation="vertical" class="bg-accented h-6 w-0.5" />
                                <img v-if="manga?.mal" src="/mal.svg" class="size-6 cursor-pointer" @click="redirect('mal')" />
                                <img
                                    v-if="manga?.listadoManga"
                                    src="/listadoManga.svg"
                                    class="size-6 cursor-pointer"
                                    @click="redirect('listadoManga')"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </UCard>
        <UTabs color="neutral" variant="link" :items="mangaTabs" class="mt-8 w-full">
            <template #volumes><VolumesDisplay :volumes="manga?.volumesData" :chapters="manga?.chaptersData" /></template>
            <template #related>
                <MangasDisplay :mangas="manga?.mangasRelated" v-model:display="display" />
            </template>
        </UTabs>
    </Deferred>
</template>
