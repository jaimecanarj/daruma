<script setup lang="ts">
import { DateFormatter, getLocalTimeZone } from '@internationalized/date';
import { DateRange } from 'reka-ui';

defineProps<{ decades?: boolean }>();

const date = defineModel<DateRange | undefined>();

const df = new DateFormatter('es-ES', {
    dateStyle: 'medium',
});

const resetDates = () => {
    date.value = undefined;
};
</script>

<template>
    <UPopover :ui="{ content: 'z-[3]' }">
        <UButton color="neutral" variant="subtle" icon="i-lucide-calendar">
            <template v-if="date?.start">
                <template v-if="date.end">
                    {{ df.format(date.start.toDate(getLocalTimeZone())) }} - {{ df.format(date.end.toDate(getLocalTimeZone())) }}
                </template>

                <template v-else>
                    {{ df.format(date.start.toDate(getLocalTimeZone())) }}
                </template>
            </template>
            <template v-else> Marca una fecha </template>
        </UButton>

        <template #content>
            <div v-if="decades" class="mx-2 flex justify-between pt-2">
                <UButton color="neutral" variant="ghost" class="p-1.5">
                    <UIcon name="lucide:chevron-first" class="size-5" />
                </UButton>

                <UButton color="neutral" variant="ghost" class="p-1.5">
                    <UIcon name="lucide:chevron-last" class="size-5" />
                </UButton>
            </div>
            <UCalendar v-model="date" class="p-2" range />
            <div class="mx-2 flex justify-end pb-2">
                <UButton color="neutral" variant="ghost" class="p-1.5" @click="resetDates">
                    Borrar <UIcon name="lucide:trash-2" class="size-5" />
                </UButton>
            </div>
        </template>
    </UPopover>
</template>
