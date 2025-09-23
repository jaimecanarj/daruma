<script setup lang="ts">
import CleanInputNumber from '@/components/formComponents/CleanInputNumber.vue';
import { VolumeForm } from '@/types';
import { nextTick, useTemplateRef } from 'vue';

const volumes = defineModel<VolumeForm[]>({ default: [] });

defineProps<{ errors: any[] | undefined }>();

const volumeList = useTemplateRef('volumeList');

const addVolume = async () => {
    const newVolume = { name: 'Tomo ', cover: undefined, date: undefined, order: 0, pages: undefined, chapters: [] };

    //Obtengo el orden siguiente al último tomo
    newVolume.order = (volumes.value?.[volumes.value.length - 1]?.order ?? 0) + 1;

    //Obtengo el nombre del último tomo
    const info = extractNameInfo(volumes.value[volumes.value.length - 1]?.name);
    if (info) {
        newVolume.name = `${info.prefix}${info.number + 1}`;
    }

    //Lo añado a la lista de tomos
    volumes.value.push(newVolume);

    //Timeout para que se ejecute el desplazamiento una vez se ha añadido
    await nextTick();
    volumeList.value
        ?.find((vol) => {
            return vol?.$el?.dataset?.order === newVolume.order.toString();
        })
        ?.$el?.scrollIntoView({ behavior: 'smooth', block: 'center' });
};

const deleteVolume = (volumeIndex: number) => {
    volumes.value.splice(volumeIndex, 1);
};

const extractNameInfo = (name?: string) => {
    const match = name?.match(/(\D+)(\d+)/);
    return match ? { prefix: match[1], number: parseInt(match[2]) } : null;
};

const addChapter = (volumeIndex: number) => {
    let newChapterInfo = { prefix: 'Capítulo ', number: 0 };

    // Comprobar capítulos existentes
    if (volumes.value[volumeIndex].chapters.length > 0) {
        const lastChapter = volumes.value[volumeIndex].chapters[volumes.value[volumeIndex].chapters.length - 1];
        const info = extractNameInfo(lastChapter.name);
        if (info) newChapterInfo = info;
    } else if (volumeIndex > 0 && volumes.value[volumeIndex - 1].chapters.length > 0) {
        const prevLastChapter = volumes.value[volumeIndex - 1].chapters[volumes.value[volumeIndex - 1].chapters.length - 1];
        const info = extractNameInfo(prevLastChapter.name);
        if (info) newChapterInfo = info;
    }

    const chapter = {
        name: `${newChapterInfo.prefix}${newChapterInfo.number + 1}: `,
        order: 0,
    };
    volumes.value[volumeIndex].chapters.push(chapter);
};
const deleteChapter = (volumeIndex: number, chapterIndex: number) => {
    volumes.value[volumeIndex].chapters.splice(chapterIndex, 1);
};

const volumeOrderUp = async (volumeIndex: number) => {
    const volume = volumes.value[volumeIndex];
    //En caso del tomo ser el primero o estar solo, no obtengo el anterior
    if (volumes.value.length > 1 && volumeIndex !== 0) {
        const prevVolume = volumes.value[volumeIndex - 1];
        //Si el tomo anterior tiene justamente el orden previo los intercambio
        if (volume.order! - 1 === prevVolume.order) {
            volumes.value[volumeIndex - 1].order = volume.order;
        }
    }

    //Aumento el valor del tomo en 1
    volumes.value[volumeIndex].order = volume.order! - 1;

    //Reordeno la lista por el nuevo orden
    volumes.value = volumes.value.sort((a, b) => a.order! - b.order!);

    //Timeout para que se ejecute el desplazamiento una vez se ha reordenado
    await nextTick();
    volumeList.value
        ?.find((vol) => {
            return vol?.$el?.dataset?.order === volume?.order?.toString();
        })
        ?.$el?.scrollIntoView({ behavior: 'smooth', block: 'center' });
};

const volumeOrderDown = async (volumeIndex: number) => {
    const volume = volumes.value[volumeIndex];
    //En caso del tomo ser el último o estar solo, no obtengo el siguiente
    if (volumes.value.length > 1 && volumeIndex !== volumes.value.length - 1) {
        const nextVolume = volumes.value[volumeIndex + 1];
        //Si el tomo siguiente tiene justamente el orden previo los intercambio
        if (volume.order! + 1 === nextVolume.order) {
            volumes.value[volumeIndex + 1].order = volume.order;
        }
    }

    //Disminuyo el valor del tomo en 1
    volumes.value[volumeIndex].order = volume.order! + 1;

    //Reordeno la lista por el nuevo orden
    volumes.value = volumes.value.sort((a, b) => a.order! - b.order!);

    //Timeout para que se ejecute el desplazamiento una vez se ha reordenado
    await nextTick();
    volumeList.value
        ?.find((vol) => {
            return vol?.$el?.dataset?.order === volume?.order?.toString();
        })
        ?.$el?.scrollIntoView({ behavior: 'smooth', block: 'center' });
};

const volumeOrderChange = async (volumeIndex: number, event: Event) => {
    const input = event?.target as HTMLInputElement;
    const newOrder = parseInt(input.value);

    //Controlar valor vacío
    if (!newOrder) {
        volumes.value[volumeIndex].order = 999;
    } else {
        const volume = volumes.value.splice(volumeIndex, 1)[0];

        //Actualizar orden de tomos subsecuentes al nuevo valor
        let lastCheckedOrder = newOrder;
        volumes.value = volumes.value.map((vol) => {
            if (vol.order! >= newOrder) {
                if (vol.order === lastCheckedOrder) {
                    lastCheckedOrder++;
                    return { ...vol, order: vol.order! + 1 };
                }
            }
            return vol;
        });

        await nextTick(); //Actualiza el valor de volumes.value antes de añadir el tomo

        //Actualizar el orden del tomo y volver a añadirlo
        volumes.value.push({ ...volume, order: newOrder });
    }

    //Reordenar
    volumes.value = volumes.value.sort((a, b) => a.order! - b.order!);

    //Timeout para que se ejecute el desplazamiento una vez se ha reordenado
    await nextTick();
    volumeList.value
        ?.find((vol) => {
            return vol?.$el?.dataset?.order === newOrder.toString();
        })
        ?.$el?.scrollIntoView({ behavior: 'smooth', block: 'center' });
};
</script>

<template>
    <UModal :ui="{ content: 'max-w-[700px]' }">
        <UButton icon="lucide:layout-list" size="xs" :color="errors?.some((error) => error.name.startsWith('volumesData')) ? 'error' : 'neutral'" />
        <template #content>
            <div class="flex max-h-[600px] flex-col items-center gap-6 overflow-y-scroll p-6 sm:max-h-[500px]">
                <div v-if="volumes.length === 0" class="mt-10 flex h-32 flex-col items-center justify-center gap-4">
                    <UIcon name="lucide:book-dashed" class="text-muted size-10" />
                    <p class="text-muted select-none">No hay ningún tomo</p>
                </div>
                <UCard
                    v-for="(volume, index) in volumes"
                    :key="volume.order"
                    ref="volumeList"
                    variant="soft"
                    class="w-full overflow-visible"
                    :data-order="volume.order"
                >
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <div class="flex h-full flex-col sm:w-56">
                            <UFormField :name="`volumesData.${index}.cover`" class="mx-auto">
                                <UFileUpload
                                    v-model="volume.cover"
                                    accept="image/*"
                                    label="Portada"
                                    description="Máximo 5MB"
                                    class="aspect-[1/1.4142] sm:w-full"
                                >
                                    <template #default="{ open }">
                                        <div v-if="volume.coverUrl && !volume.cover" @click.prevent="open(undefined)">
                                            <img :src="`/storage/${volume.coverUrl}`" class="h-full w-full rounded-lg object-cover" alt="Portada" />
                                        </div>
                                    </template>
                                </UFileUpload>
                            </UFormField>
                            <UButtonGroup class="mt-4 hidden justify-center sm:flex">
                                <UButton
                                    icon="lucide:arrow-up"
                                    size="sm"
                                    variant="subtle"
                                    color="neutral"
                                    :disabled="volume.order === 1"
                                    @click="volumeOrderUp(index)"
                                />
                                <UInput
                                    :value="volume.order"
                                    class="w-12"
                                    :ui="{ base: 'text-center text-md font-medium py-1' }"
                                    @change="volumeOrderChange(index, $event)"
                                />
                                <UButton icon="lucide:arrow-down" size="sm" variant="subtle" color="neutral" @click="volumeOrderDown(index)" />
                            </UButtonGroup>
                        </div>
                        <div class="w-full">
                            <div class="flex items-center justify-between">
                                <UFormField :name="`volumesData.${index}.name`">
                                    <UInput v-model="volume.name" variant="none" class="w-full font-bold" :ui="{ base: 'text-lg sm:text-xl' }" />
                                </UFormField>
                                <UFormField :name="`volumesData.${index}.pages`">
                                    <div class="flex items-center gap-2">
                                        <CleanInputNumber
                                            v-model="volume.pages"
                                            :ui="{ base: 'py-1 text-center px-2.5!' }"
                                            class="w-16"
                                            variant="subtle"
                                            :max="9999"
                                        />
                                        <p class="select-none">págs</p>
                                    </div>
                                </UFormField>
                            </div>
                            <USeparator class="my-2" />
                            <div class="flex h-[154px] flex-col overflow-y-scroll">
                                <div v-if="volume.chapters.length === 0" class="flex h-full items-center justify-center gap-4">
                                    <UIcon name="lucide:book-open-text" class="text-muted size-6" />
                                    <p class="text-muted select-none">No hay ningún capítulo</p>
                                </div>
                                <div
                                    v-else
                                    v-for="(chapter, chapterIndex) in volume.chapters"
                                    :key="chapterIndex"
                                    class="mx-1 flex items-center sm:mx-4 sm:gap-2"
                                >
                                    <UIcon name="lucide:circle-small" />
                                    <UInput v-model="chapter.name" variant="none" class="w-full" :ui="{ base: 'px-1 sm:pe-2.5' }" />
                                    <UButton
                                        icon="lucide:trash-2"
                                        size="xs"
                                        variant="ghost"
                                        color="neutral"
                                        @click="deleteChapter(index, chapterIndex)"
                                    />
                                </div>
                            </div>
                            <USeparator class="mt-2 mb-4" />
                            <div
                                v-if="errors?.some((error) => error.name.startsWith(`volumesData.${index}.chapters`))"
                                class="text-error mb-2 px-4 text-sm"
                            >
                                Capítulos sin nombre
                            </div>
                            <div class="flex items-center justify-between">
                                <UButton
                                    trailing-icon="lucide:plus"
                                    variant="solid"
                                    color="neutral"
                                    size="sm"
                                    label="Añadir un capítulo"
                                    class="mx-2"
                                    @click="addChapter(index)"
                                />
                                <UButton color="error" variant="ghost" size="sm" class="p-1.5" @click="deleteVolume(index)">
                                    Borrar <UIcon name="lucide:trash-2" class="size-5" />
                                </UButton>
                            </div>
                        </div>
                        <UButtonGroup class="mt-4 flex justify-center sm:hidden">
                            <UButton
                                icon="lucide:arrow-up"
                                size="sm"
                                variant="subtle"
                                color="neutral"
                                :disabled="volume.order === 1"
                                @click="volumeOrderUp(index)"
                            />
                            <UInput
                                :value="volume.order"
                                class="w-12"
                                :ui="{ base: 'text-center text-md font-medium py-0.5' }"
                                @change="volumeOrderChange(index, $event)"
                            />
                            <UButton icon="lucide:arrow-down" size="sm" variant="subtle" color="neutral" @click="volumeOrderDown(index)" />
                        </UButtonGroup>
                    </div>
                </UCard>
                <UButton trailing-icon="lucide:plus" size="xl" label="Añadir un tomo" class="mx-auto my-6" @click="addVolume" />
            </div>
        </template>
    </UModal>
</template>
