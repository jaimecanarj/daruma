<script setup lang="ts">
import BaseForm from '@/components/admin/BaseForm.vue';
import DatePicker from '@/components/DatePicker.vue';
import { Magazine, MagazineForm } from '@/types';
import { demographies, frequencies } from '@/utils/constants';
import { magazineSchema } from '@/utils/zodSchemas';
import { parseDate } from '@internationalized/date';

const props = defineProps<{
    item?: Magazine;
    purpose: 'create' | 'edit';
}>();

const initialValues: MagazineForm = {
    name: props.item?.name,
    publisher: props.item?.publisher,
    demography: props.item?.demography,
    date: props.item?.date ? parseDate(props.item?.date) : undefined,
    frequency: props.item?.frequency,
};
</script>

<template>
    <BaseForm
        :item="item"
        :purpose="purpose"
        resource-name="Revista"
        resource-gender="feminine"
        resource-route="magazine"
        :schema="magazineSchema"
        :initial-values="initialValues"
        :form-transform="(data: MagazineForm) => ({ ...data, date: data.date?.toString() })"
    >
        <template #default="{ form }">
            <div class="flex w-full flex-col gap-6 md:flex-row">
                <UFormField label="Nombre" name="name" class="w-full" required>
                    <UInput v-model="form.name" class="w-full" />
                </UFormField>

                <UFormField label="Editorial" name="publisher" class="w-full" required>
                    <UInput v-model="form.publisher" class="w-full" />
                </UFormField>
            </div>
            <div class="flex w-full flex-col gap-6 md:flex-row">
                <UFormField label="Demografía" name="demography" class="w-full" required>
                    <USelect v-model="form.demography" :items="demographies" placeholder="Selecciona una demografía" class="w-full" />
                </UFormField>

                <UFormField label="Periodicidad" name="frequency" class="w-full" required>
                    <USelect v-model="form.frequency" :items="frequencies" placeholder="Selecciona una periodicidad" class="w-full" />
                </UFormField>
            </div>

            <UFormField label="Fecha" name="date" class="md:w-1/2 md:pr-3">
                <DatePicker v-model="form.date" decades />
            </UFormField>
        </template>
    </BaseForm>
</template>
