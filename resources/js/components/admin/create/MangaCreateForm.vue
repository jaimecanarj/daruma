<script setup lang="ts">
import CoverImageSelector from '@/components/CoverImageSelector.vue';
import DatePicker from '@/components/DatePicker.vue';
import MultipleValuesInput from '@/components/MultipleValuesInput.vue';
import MultipleValuesSelect from '@/components/MultipleValuesSelect.vue';
import type { CreatePageProps, MangaCreateForm } from '@/types';
import { alternativeNamesOptions, authorsOptions, languageOptions, relatedMangasOptions } from '@/utils/constants';
import { mangaSchema } from '@/utils/zodSchemas';
import { Deferred, useForm } from '@inertiajs/vue3';
import { computed, useTemplateRef } from 'vue';

const toast = useToast();

const props = defineProps<CreatePageProps>();

//Estructuro las props para que se puedan usar correctamente
const mangas = computed(() =>
    props.mangas ? props.mangas.map((manga) => ({ label: manga.name, value: manga.id })).sort((a, b) => a.label.localeCompare(b.label)) : [],
);
const people = computed(() =>
    props.people
        ? props.people
              .map((person) => ({ label: `${person.name} ${person.surname}`, value: person.id }))
              .sort((a, b) => a.label.localeCompare(b.label))
        : [],
);
const magazines = computed(() =>
    props.magazines
        ? props.magazines.map((magazine) => ({ label: magazine.name, value: magazine.id })).sort((a, b) => a.label.localeCompare(b.label))
        : [],
);
const tags = computed(() =>
    props.tags
        ? props.tags
              .map((tag) => ({ label: tag.name, value: tag.id, category: tag.type, color: tag.type === 'genre' ? 'primary' : 'secondary' }))
              .sort((a, b) => a.label.localeCompare(b.label))
        : [],
);

const form = useForm<MangaCreateForm>({
    cover: undefined,
    name: undefined,
    alternativeNames: [],
    sinopsis: undefined,
    startDate: undefined,
    endDate: undefined,
    authors: [],
    tags: [],
    volumes: undefined,
    tankoubon: undefined,
    chapters: undefined,
    readingDirection: false,
    language: 'es',
    finished: false,
    magazineId: undefined,
    relatedMangas: [],
    mal: undefined,
    listadoManga: undefined,
});

const coverComponent = useTemplateRef('cover');

const onSubmit = async () => {
    form.transform((data) => ({
        ...data,
        readingDirection: data.readingDirection ? 'ltr' : 'rtl',
        startDate: data.startDate?.toString(),
        endDate: data.endDate?.toString(),
    })).post('/manga/store', {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            form.reset();
            coverComponent.value!.clear();
            toast.add({ title: 'Manga creado satisfactoriamente.' });
        },
        onError: () => {
            toast.add({ title: 'Hubo algún problema.' });
        },
    });
};
</script>

<template>
    <UCard class="bg-muted mx-auto mt-10 w-full md:max-w-3xl">
        <UForm :schema="mangaSchema" class="mt-4 flex flex-col gap-4" :state="form" @submit.prevent="onSubmit">
            <div class="flex flex-col gap-6 md:flex-row">
                <UFormField name="cover" required class="md:basis-2/5">
                    <CoverImageSelector ref="cover" v-model="form.cover" />
                </UFormField>
                <div class="flex flex-col gap-6 md:basis-3/5">
                    <UFormField label="Nombre" name="name" required>
                        <UInput v-model.trim="form.name" class="w-full" />
                    </UFormField>
                    <div class="flex gap-6">
                        <UFormField label="Fecha de inicio" name="date" class="basis-1/2">
                            <DatePicker v-model="form.startDate" decades />
                        </UFormField>
                        <UFormField label="Fecha de fin" name="endDate" class="basis-1/2">
                            <DatePicker v-model="form.endDate" decades />
                        </UFormField>
                    </div>
                    <div class="flex justify-between gap-4">
                        <UFormField label="Tomos" name="volumes">
                            <UInputNumber v-model="form.volumes" orientation="vertical" />
                        </UFormField>
                        <UFormField label="Tankoubon" name="tankoubon">
                            <UInputNumber v-model="form.tankoubon" orientation="vertical" />
                        </UFormField>
                        <UFormField label="Capítulos" name="chapters">
                            <UInputNumber v-model="form.chapters" orientation="vertical" />
                        </UFormField>
                    </div>
                    <UFormField label="Sinopsis" name="sinopsis">
                        <UTextarea v-model="form.sinopsis" class="w-full" />
                    </UFormField>
                    <div class="flex justify-between gap-4">
                        <UFormField label="Idioma" name="language">
                            <USelect v-model="form.language" :items="languageOptions" class="w-24 sm:w-28" />
                        </UFormField>
                        <UFormField label="Dir. lectura" name="readingDirection">
                            <div class="mt-3 flex items-center gap-2">
                                <p class="font-semibold">I</p>
                                <USwitch
                                    vmodel="form.readingDirection"
                                    :ui="{
                                        base: 'data-[state=unchecked]:bg-primary data-[state=checked]:bg-secondary',
                                    }"
                                />
                                <p class="font-semibold">D</p>
                            </div>
                        </UFormField>
                        <UFormField label="Estado" name="finished">
                            <UCheckbox v-model="form.finished" label="Completo" class="mt-3" />
                        </UFormField>
                    </div>
                </div>
            </div>
            <USeparator :ui="{ border: 'border-accented' }" />
            <UFormField label="Nombres alternativos" name="alternativeNames">
                <MultipleValuesInput v-model="form.alternativeNames" :type-options="alternativeNamesOptions" />
            </UFormField>
            <UFormField label="Autores" name="authors" required>
                <Deferred data="people">
                    <template #fallback>
                        <div class="flex items-center gap-2">
                            <div class="flex flex-1 flex-col gap-2 md:flex-row">
                                <USelect disabled class="w-full" />
                                <USelect class="min-w-48" disabled />
                            </div>
                            <UButton icon="lucide:plus" color="neutral" class="h-12 w-12 justify-center md:h-auto md:w-auto" disabled />
                        </div>
                    </template>
                    <MultipleValuesSelect v-model="form.authors" title="un autor" :input-options="people" :type-options="authorsOptions" />
                </Deferred>
            </UFormField>
            <UFormField label="Etiquetas" name="tags">
                <Deferred data="tags">
                    <template #fallback>
                        <div class="flex items-center gap-2">
                            <USelect disabled class="w-full" />
                            <UButton icon="lucide:plus" color="neutral" class="h-12 w-12 justify-center md:h-auto md:w-auto" disabled />
                        </div>
                    </template>
                    <MultipleValuesSelect v-model="form.tags" title="una etiqueta" :input-options="tags" />
                </Deferred>
            </UFormField>
            <UFormField label="Mangas relacionados" name="relatedMangas">
                <Deferred data="mangas">
                    <template #fallback>
                        <div class="flex items-center gap-2">
                            <div class="flex flex-1 flex-col gap-2 md:flex-row">
                                <USelect disabled class="w-full" />
                                <USelect class="min-w-48" disabled />
                            </div>
                            <UButton icon="lucide:plus" color="neutral" class="h-12 w-12 justify-center md:h-auto md:w-auto" disabled />
                        </div>
                    </template>
                    <MultipleValuesSelect
                        v-model="form.relatedMangas"
                        title="un manga"
                        :input-options="mangas"
                        :type-options="relatedMangasOptions"
                    />
                </Deferred>
            </UFormField>
            <USeparator :ui="{ border: 'border-accented' }" />
            <div class="grid grid-cols-3 gap-4">
                <UFormField label="Revista" name="magazineId">
                    <Deferred data="magazines">
                        <template #fallback><USelect disabled class="w-full" /></template>
                        <USelect v-model="form.magazineId" :items="magazines" placeholder="Selecciona una revista" class="w-full" />
                    </Deferred>
                </UFormField>
                <UFormField label="MAL" name="mal">
                    <UInputNumber v-model="form.mal" orientation="vertical" />
                </UFormField>
                <UFormField label="ListadoManga" name="listadoManga">
                    <UInputNumber v-model="form.listadoManga" orientation="vertical" />
                </UFormField>
            </div>
            <UButton type="submit" class="text-md mt-4 justify-center" :disabled="form.processing">
                {{ form.processing ? 'Creando' : 'Crear' }}
            </UButton>
        </UForm>
    </UCard>
</template>
