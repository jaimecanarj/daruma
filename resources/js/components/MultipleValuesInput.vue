<script setup lang="ts">
import { BadgeProps } from '@nuxt/ui/components/Badge.vue';
import { ChipProps } from '@nuxt/ui/components/Chip.vue';
import { ref } from 'vue';

const props = defineProps<{
    typeOptions?: {
        label: string;
        value: string;
        color: string;
    }[];
}>();

const inputValue = ref<string>('');

const inputType = props.typeOptions ? ref<{ label: string; value: string; color: string }>(props.typeOptions[0]) : undefined;

const inputValues = defineModel<{ label: string; value?: number; category?: string; color?: string }[]>();

const addInputValue = () => {
    if (inputValue.value !== '') {
        //Si tiene opciones
        if (props.typeOptions) {
            inputValues.value!.push({
                label: inputValue.value as string,
                category: inputType!.value.value,
                color: inputType!.value.color,
            });
            //Si no tiene opciones
        } else {
            inputValues.value!.push({
                label: inputValue.value as string,
            });
        }
        inputValue.value = '';
    }
};

const removeInputValue = (index: number) => {
    inputValues.value!.splice(index, 1);
};
</script>

<template>
    <div class="flex items-center gap-2">
        <div class="flex flex-1 flex-col gap-2 md:flex-row">
            <UInput v-model="inputValue" class="w-full" @keydown.enter.prevent="addInputValue" />
            <USelectMenu v-if="typeOptions" v-model="inputType" class="min-w-48" :items="typeOptions" :search-input="false">
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
