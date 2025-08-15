<script setup lang="ts">
import MangasDisplay from '@/components/MangasDisplay.vue';
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
            label: 'Volúmenes',
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
        return `${startDate} - ${endDate}`;
    } else {
        return null;
    }
});

const readingDirection = computed(() => {
    return props.manga?.readingDirection === 'ltr' ? 'lucide:arrow-big-right' : 'lucide:arrow-big-left';
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
                    <img :src="`/storage/${manga?.cover}`" class="aspect-[1/1.4142] rounded-lg" alt="Portada" />
                    <!--Dirección, estado, idioma, enlaces-->
                    <div class="mt-6 hidden flex-col gap-1 px-1 sm:flex">
                        <div class="flex flex-col items-center justify-center gap-2 md:flex-row lg:gap-3">
                            <div class="flex items-center gap-1 md:gap-2 lg:gap-3">
                                <UIcon :name="readingDirection" class="size-6" />
                                <UIcon :name="status" class="size-6" />
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
                <div class="w-full sm:w-fit">
                    <div class="flex flex-col items-center gap-2 sm:items-start">
                        <!--Nombre-->
                        <p class="text-center text-2xl leading-normal font-bold sm:text-start sm:text-3xl lg:text-4xl">{{ manga?.name }}</p>
                        <div class="ml-1 flex flex-wrap justify-around gap-2 sm:flex-row sm:justify-start">
                            <!--Fecha-->
                            <div class="flex items-center gap-1"><UIcon name="lucide:calendar-days" />{{ dates }}</div>
                            <template v-if="manga?.volumes">
                                <!--Volúmenes-->
                                <USeparator orientation="vertical" class="bg-accented hidden h-6 sm:block" size="sm" />
                                <div class="flex items-center gap-1">
                                    <UIcon name="lucide:library-big" />Volúmenes: {{ manga?.tankoubon }} ({{ manga?.volumes }})
                                </div>
                            </template>
                            <!--Capítulos-->
                            <template v-if="manga?.chapters">
                                <USeparator orientation="vertical" class="bg-accented hidden h-6 sm:block" size="sm" />
                                <div class="flex items-center gap-1"><UIcon name="lucide:book-open-text" />Capítulos: {{ manga?.chapters }}</div>
                            </template>
                        </div>
                        <!--Etiquetas-->
                        <div class="flex flex-wrap items-center justify-center gap-1 sm:justify-start">
                            <UBadge
                                v-for="tag of manga?.tags"
                                :key="tag.id"
                                :label="tag.name"
                                :color="tag.type === 'genre' ? 'primary' : 'secondary'"
                                variant="soft"
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
                                <UIcon :name="readingDirection" class="size-5" />
                                <UIcon :name="status" class="size-5" />
                                <USeparator orientation="vertical" class="bg-accented h-6 w-0.5" />
                                <div class="flex items-center gap-1">{{ language }}</div>
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
            <template #volumes></template>
            <template #related>
                <MangasDisplay :mangas="manga?.mangasRelated" v-model:display="display" />
            </template>
        </UTabs>
    </Deferred>
</template>
