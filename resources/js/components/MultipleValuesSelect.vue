<script setup lang="ts">
import { BadgeProps } from '@nuxt/ui/components/Badge.vue';
import { ChipProps } from '@nuxt/ui/components/Chip.vue';
import { computed, ref } from 'vue';

const props = defineProps<{
    title: string;
    inputOptions: {
        label: string;
        value: number;
        category?: string;
        color?: string;
    }[];
    typeOptions?: {
        label: string;
        value: string;
        color: string;
    }[];
}>();

const inputValue = ref<{
    label: string;
    value: number;
    category?: string;
    color?: string;
}>();

const inputType = props.typeOptions ? ref<{ label: string; value: string; color: string }>(props.typeOptions[0]) : undefined;

const inputValues = defineModel<{ label: string; value?: number; category?: string; color?: string }[]>();

const inputOptionsFiltered = computed(() =>
    props.inputOptions.filter((option) => !inputValues.value?.some((value) => value.value === option.value && value.category === option.category)),
);

const addInputValue = () => {
    if (inputValue.value && inputValue.value.value >= 0) {
        //Si tiene opciones
        if (props.typeOptions) {
            inputValues.value!.push({
                label: inputValue.value.label,
                value: inputValue.value.value,
                category: inputType!.value.value,
                color: inputType!.value.color,
            });
            //Si el select tiene categoría y color
        } else if (inputValue.value.category) {
            inputValues.value!.push({
                label: inputValue.value.label,
                value: inputValue.value.value,
                category: inputValue.value.category,
                color: inputValue.value.color,
            });

            inputValues.value!.sort((a, b) =>
                (a.category + a.label).localeCompare(b.category + b.label, 'es', {
                    sensitivity: 'base',
                }),
            );
            //Si simplemente se quiere una lista de valores del select
        } else {
            inputValues.value!.push({
                label: inputValue.value.label,
                value: inputValue.value.value,
            });

            inputValues.value!.sort((a, b) => a.label.localeCompare(b.label, 'es', { sensitivity: 'base' }));
        }
        inputValue.value = undefined;
    }
};

const removeInputValue = (index: number) => {
    inputValues.value!.splice(index, 1);
};
</script>

<template>
    <div class="flex items-center gap-2">
        <div class="flex flex-1 flex-col gap-2 md:flex-row">
            <USelectMenu
                v-model="inputValue"
                :placeholder="`Selecciona ${title}`"
                :search-input="{ placeholder: `Busca ${title}...` }"
                :items="inputOptionsFiltered"
                class="w-full"
            >
                <template #leading="{ modelValue, ui }">
                    <UChip
                        v-if="modelValue?.color"
                        :color="modelValue.color as ChipProps['color']"
                        inset
                        standalone
                        :size="ui.itemLeadingChipSize() as ChipProps['size']"
                        :class="ui.itemLeadingChip()"
                    />
                </template>
                <template #item-leading="{ item }">
                    <UChip v-if="item.color" :color="item.color as ChipProps['color']" inset standalone size="lg" />
                </template>
            </USelectMenu>
            <USelectMenu v-if="typeOptions" v-model="inputType" class="min-w-48" :items="typeOptions" :search-input="false">
                <template #leading="{ modelValue, ui }">
                    <UChip
                        v-if="modelValue"
                        :color="modelValue.color as ChipProps['color']"
                        inset
                        standalone
                        :size="ui.itemLeadingChipSize() as ChipProps['size']"
                        :class="ui.itemLeadingChip()"
                    />
                </template>
                <template #item-leading="{ item }">
                    <UChip v-if="item.color" :color="item.color as ChipProps['color']" inset standalone size="lg" class="" />
                </template>
            </USelectMenu>
        </div>
        <UButton icon="lucide:plus" color="neutral" class="h-10 w-10 justify-center md:h-auto md:w-auto" @click="addInputValue" />
    </div>
    <div class="mt-2 flex flex-wrap gap-2">
        <div v-for="(item, index) in inputValues" :key="index">
            <UBadge :color="(item.color as BadgeProps['color']) ?? 'neutral'" variant="soft" size="md">
                <span class="max-w-32 overflow-hidden overflow-ellipsis whitespace-nowrap">
                    {{ item.label }}
                </span>
                <template #trailing>
                    <UIcon name="lucide:x" class="cursor-pointer" @click="removeInputValue(index)" />
                </template>
            </UBadge>
        </div>
    </div>
</template>
