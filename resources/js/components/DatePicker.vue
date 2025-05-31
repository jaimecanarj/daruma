<script setup lang="ts">
import { DateFormatter, getLocalTimeZone, today, type CalendarDate } from '@internationalized/date';

defineProps<{ decades?: boolean }>();

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
</script>

<template>
    <UPopover>
        <UButton color="neutral" variant="subtle" icon="lucide:calendar" class="focus-visible:ring-primary w-full">
            {{
                date && typeof date?.toDate === 'function'
                    ? new DateFormatter('es-ES', { dateStyle: 'medium' }).format(date.toDate(getLocalTimeZone()))
                    : 'Marca una fecha'
            }}
        </UButton>

        <template #content>
            <div v-if="decades" class="mx-2 flex justify-between pt-1">
                <UButton color="neutral" variant="ghost" class="p-1.5" @click="prevDecade">
                    <UIcon name="lucide:chevron-first" class="size-5" />
                </UButton>
                <UButton color="neutral" variant="ghost" class="p-1.5" @click="nextDecade">
                    <UIcon name="lucide:chevron-last" class="size-5" />
                </UButton>
            </div>
            <UCalendar v-model="date" class="p-2" />
        </template>
    </UPopover>
</template>
