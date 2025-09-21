<script setup lang="ts">
import { DateFormatter, getLocalTimeZone, today, type CalendarDate } from '@internationalized/date';
import { twJoin } from 'tailwind-merge';

const props = defineProps<{ decades?: boolean; filter?: boolean; class?: string }>();

const date = defineModel<CalendarDate | undefined>();

const prevDecade = () => {
    if (!date.value) {
        date.value = today(getLocalTimeZone());
    }
    date.value = date.value.subtract({ years: 10 });
};

const nextDecade = () => {
    if (!date.value) {
        date.value = today(getLocalTimeZone());
    }
    date.value = date.value.add({ years: 10 });
};

const resetDate = () => {
    date.value = undefined;
};
</script>

<template>
    <UPopover v-bind="$attrs">
        <UButton
            v-if="filter"
            color="neutral"
            variant="outline"
            icon="lucide:calendar"
            trailing-icon="lucide:chevron-down"
            class="focus-visible:ring-primary w-full"
            :ui="{
                leadingIcon: date ? 'text-default' : 'text-dimmed',
                trailingIcon: 'text-dimmed',
            }"
        >
            <p class="w-full text-start">
                {{
                    date && typeof date?.toDate === 'function'
                        ? new DateFormatter('es-ES', { dateStyle: 'medium' }).format(date.toDate(getLocalTimeZone()))
                        : ''
                }}
                <span v-if="!date" class="text-dimmed font-normal">{{ 'Cualquier fecha' }}</span>
            </p>
        </UButton>
        <UButton v-else color="neutral" variant="subtle" icon="lucide:calendar" :class="twJoin('focus-visible:ring-primary w-full', props.class)">
            {{
                date && typeof date?.toDate === 'function'
                    ? new DateFormatter('es-ES', { dateStyle: 'medium' }).format(date.toDate(getLocalTimeZone()))
                    : 'Fecha'
            }}
        </UButton>

        <template #content>
            <div v-if="decades" class="mx-2 flex justify-between pt-2">
                <UButton color="neutral" variant="ghost" class="p-1.5" @click="prevDecade">
                    <UIcon name="lucide:chevron-first" class="size-5" />
                </UButton>
                <UButton color="neutral" variant="ghost" class="p-1.5" @click="nextDecade">
                    <UIcon name="lucide:chevron-last" class="size-5" />
                </UButton>
            </div>
            <UCalendar v-model="date" class="p-2" />
            <div class="mx-2 flex justify-end pb-2">
                <UButton color="neutral" variant="ghost" class="p-1.5" @click="resetDate">
                    Borrar <UIcon name="lucide:trash-2" class="size-5" />
                </UButton>
            </div>
        </template>
    </UPopover>
</template>
