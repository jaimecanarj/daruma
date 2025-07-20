<script setup lang="ts">
import BaseForm from '@/components/admin/BaseForm.vue';
import CoverImageSelector from '@/components/CoverImageSelector.vue';
import DatePicker from '@/components/DatePicker.vue';
import MultipleValuesInput from '@/components/MultipleValuesInput.vue';
import MultipleValuesSelect from '@/components/MultipleValuesSelect.vue';
import type { Manga, MangaForm } from '@/types';
import { alternativeNamesOptions, authorsOptions, languageOptions, relatedMangasOptions } from '@/utils/constants';
import { mangaSchema } from '@/utils/zodSchemas';
import { Deferred } from '@inertiajs/vue3';
import { parseDate } from '@internationalized/date';
import { computed, useTemplateRef } from 'vue';

const props = defineProps<{
    item?: Manga;
    purpose: 'create' | 'edit';
    mangas?: { name: string; id: number }[];
    people?: { name: string; surname: string; id: number }[];
    magazines?: { name: string; id: number }[];
    tags?: { name: string; id: number; type: string }[];
}>();

//Estructuro las props para que se puedan usar correctamente
const mangas = computed(() =>
    props.mangas
        ? props.mangas
              .filter(
                  (manga) =>
                      (!props.item || manga.id !== props.item.id) &&
                      !(baseForm.value?.form as MangaForm)?.relatedMangas?.some((relatedManga) => relatedManga.value === manga.id),
              )
              .map((manga) => ({ label: manga.name, value: manga.id }))
              .sort((a, b) => a.label.localeCompare(b.label))
        : [],
);
const people = computed(() =>
    props.people
        ? props.people
              .map((person) => ({ label: `${person.name} ${person.surname ?? ''}`, value: person.id }))
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

const initialValues: MangaForm = {
    cover: undefined,
    name: props.item?.name,
    alternativeNames: props.item?.names
        ? props.item.names.map((name) => ({
              label: name.name,
              category: name.type,
              color: name.type === 'japanese' ? 'primary' : name.type === 'spanish' ? 'secondary' : 'neutral',
          }))
        : [],
    sinopsis: props.item?.sinopsis ?? undefined,
    startDate: props.item?.startDate ? parseDate(props.item.startDate) : undefined,
    endDate: props.item?.endDate ? parseDate(props.item.endDate) : undefined,
    authors: props.item?.people
        ? props.item.people.map((person) => ({
              label: `${person.name} ${person.surname ?? ''}`,
              value: person.id,
              category: person.pivot.job,
              color: person.pivot.job === 'writer' ? 'primary' : person.pivot.job === 'illustrator' ? 'secondary' : 'info',
          }))
        : [],
    tags: props.item?.tags
        ? props.item.tags.map((tag) => ({
              label: tag.name,
              value: tag.id,
              category: tag.type,
              color: tag.type === 'genre' ? 'primary' : 'secondary',
          }))
        : [],
    volumes: props.item?.volumes ?? undefined,
    tankoubon: props.item?.tankoubon ?? undefined,
    chapters: props.item?.chapters ?? undefined,
    readingDirection: props.item?.readingDirection === 'ltr',
    language: props.item?.language ?? 'es',
    finished: props.item?.finished === 1,
    magazineId: props.item?.magazineId
        ? { label: magazines.value.find((magazine) => magazine.value === props.item?.magazineId)?.label ?? '', value: props.item.magazineId }
        : undefined,
    relatedMangas: props.item?.mangasRelated
        ? props.item.mangasRelated.map((manga) => ({
              label: manga.name,
              value: manga.id,
              category: manga.pivot.relation,
              color:
                  manga.pivot.relation === 'prequel'
                      ? 'primary'
                      : manga.pivot.relation === 'sequel'
                        ? 'secondary'
                        : manga.pivot.relation === 'spin-off'
                          ? 'info'
                          : 'warning',
          }))
        : [],
    mal: props.item?.mal ?? undefined,
    listadoManga: props.item?.listadoManga ?? undefined,
    purpose: props.purpose,
};

const coverComponent = useTemplateRef('cover');

const formTransform = (data: any) => ({
    ...data,
    readingDirection: data.readingDirection ? 'ltr' : 'rtl',
    startDate: data.startDate?.toString(),
    endDate: data.endDate?.toString(),
    cover: data.cover ? data.cover : undefined,
    magazineId: data.magazineId ? data.magazineId.value : undefined,
});

const baseForm = useTemplateRef('baseForm');
</script>

<template>
    <BaseForm
        ref="baseForm"
        :item="item"
        :purpose="purpose"
        resource-name="Manga"
        resource-gender="masculine"
        resource-route="manga"
        update-method="post"
        :schema="mangaSchema"
        :initial-values="initialValues"
        :form-transform="formTransform"
        @success="coverComponent?.clear()"
    >
        <template #default="{ form }">
            <div class="flex flex-col gap-6 md:flex-row">
                <UFormField name="cover" required class="md:basis-2/5">
                    <CoverImageSelector ref="cover" v-model="form.cover" :stored-image="props.item?.cover" />
                </UFormField>
                <div class="flex flex-col gap-6 md:basis-3/5">
                    <UFormField label="Nombre" name="name" required>
                        <UInput v-model="form.name" class="w-full" />
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
                            <UButton icon="lucide:plus" color="neutral" class="size-12 justify-center md:size-auto" disabled />
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
                            <UButton icon="lucide:plus" color="neutral" class="size-12 justify-center md:size-auto" disabled />
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
                            <UButton icon="lucide:plus" color="neutral" class="size-12 justify-center md:size-auto" disabled />
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
                        <USelectMenu v-model="form.magazineId" :items="magazines" placeholder="Selecciona una revista" class="w-full" />
                    </Deferred>
                </UFormField>
                <UFormField label="MAL" name="mal">
                    <UInputNumber v-model="form.mal" orientation="vertical" :format-options="{ useGrouping: false }" />
                </UFormField>
                <UFormField label="ListadoManga" name="listadoManga">
                    <UInputNumber v-model="form.listadoManga" orientation="vertical" :format-options="{ useGrouping: false }" />
                </UFormField>
            </div>
        </template>
    </BaseForm>
</template>
