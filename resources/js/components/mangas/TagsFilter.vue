<script setup lang="ts">
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core';
import { computed } from 'vue';

const activeBreakpoint = useBreakpoints(breakpointsTailwind).active();

const props = defineProps<{
    items?: {
        label: string;
        value: number;
        category: 'genre' | 'theme';
        color: string;
    }[];
}>();

const tags = defineModel<{ include: number[]; exclude: number[] }>({ default: { include: [], exclude: [] } });

const genres = props.items?.filter((item) => item.category === 'genre');
const themes = props.items?.filter((item) => item.category === 'theme');

const align = computed(() => {
    switch (activeBreakpoint.value) {
        case 'lg':
            return 'end';
        case 'xl':
        case '2xl':
            return 'center';
        default:
            return 'start';
    }
});

const label = computed(() => {
    const includedLabels = tags.value.include.map((id) => props.items?.find((item) => item.value === id)?.label).filter(Boolean);

    const excludedLabels = tags.value.exclude
        .map((id) => props.items?.find((item) => item.value === id)?.label)
        .filter(Boolean)
        .map((label) => `- ${label}`);

    return [...includedLabels, ...excludedLabels].join(', ');
});

const getVariant = (value: number) => {
    return tags.value?.include?.includes(value) || tags.value?.exclude?.includes(value) ? 'outline' : 'soft';
};

const getColor = (value: number) => {
    if (tags.value?.include?.includes(value)) {
        return 'primary';
    } else if (tags.value?.exclude?.includes(value)) {
        return 'error';
    } else {
        return 'neutral';
    }
};

const toggleTag = (value: number) => {
    const includeIndex = tags.value.include.indexOf(value);
    if (includeIndex !== -1) {
        tags.value = {
            include: tags.value.include.filter((id) => id !== value),
            exclude: [...tags.value.exclude, value],
        };
    } else {
        const excludeIndex = tags.value.exclude.indexOf(value);
        if (excludeIndex !== -1) {
            tags.value = {
                include: tags.value.include,
                exclude: tags.value.exclude.filter((id) => id !== value),
            };
        } else {
            tags.value = {
                include: [...tags.value.include, value],
                exclude: tags.value.exclude,
            };
        }
    }
};
</script>

<template>
    <div class="w-full">
        <div class="m-0.5 flex items-end justify-between">
            <p class="first-letter:capitalize">
                Etiquetas <span v-if="tags.include.length > 0" class="text-primary ml-2 text-sm">+{{ tags.include.length }}</span>
                <span v-if="tags.exclude.length > 0" class="text-error ml-2 text-sm">-{{ tags.exclude.length }}</span>
            </p>
            <UButton
                v-if="tags.include.length > 0 || tags.exclude.length > 0"
                class="p-0.5"
                color="neutral"
                variant="link"
                size="sm"
                icon="lucide:circle-x"
                @click="tags = { include: [], exclude: [] }"
            />
        </div>
        <UPopover v-bind="$attrs" :content="{ align }">
            <UButton
                color="neutral"
                variant="outline"
                icon="lucide:tag"
                trailing-icon="lucide:chevron-down"
                class="focus-visible:ring-primary w-full"
                :ui="{
                    leadingIcon: tags.include.length > 0 || tags.exclude.length > 0 ? '' : 'text-dimmed',
                    trailingIcon: 'text-dimmed',
                }"
            >
                <p v-if="tags.include.length > 0 || tags.exclude.length > 0" class="line-clamp-1 w-full text-start">{{ label }}</p>
                <p v-else class="text-dimmed w-full text-start font-normal">{{ 'Cualquier etiqueta' }}</p>
            </UButton>
            <template #content>
                <div
                    class="w-(--reka-popper-anchor-width) p-4 sm:w-[calc(var(--reka-popper-anchor-width)*2+32px)] xl:w-[calc(var(--reka-popper-anchor-width)*3+64px)]"
                >
                    <USeparator label="Géneros" :ui="{ label: 'text-lg' }" />
                    <div class="mt-3 flex flex-wrap gap-2">
                        <UButton
                            v-for="genre of genres"
                            :key="genre.value"
                            :label="genre.label"
                            size="xs"
                            :variant="getVariant(genre.value)"
                            :color="getColor(genre.value)"
                            @click="toggleTag(genre.value)"
                        />
                    </div>
                    <USeparator label="Temas" :ui="{ label: 'text-lg' }" class="mt-4" />
                    <div class="mt-3 flex flex-wrap gap-2">
                        <UButton
                            v-for="theme of themes"
                            :key="theme.value"
                            :label="theme.label"
                            size="xs"
                            :variant="getVariant(theme.value)"
                            :color="getColor(theme.value)"
                            @click="toggleTag(theme.value)"
                        />
                    </div>
                </div>
            </template>
        </UPopover>
    </div>
</template>
