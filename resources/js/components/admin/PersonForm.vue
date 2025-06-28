<script setup lang="ts">
import BaseForm from '@/components/admin/BaseForm.vue';
import { Person, PersonForm } from '@/types';
import { personSchema } from '@/utils/zodSchemas';

const props = defineProps<{
    item?: Person;
    purpose: 'create' | 'edit';
}>();

const initialValues: PersonForm = {
    name: props.item?.name,
    kanjiName: props.item?.kanjiName,
    surname: props.item?.surname,
    kanjiSurname: props.item?.kanjiSurname,
};
</script>

<template>
    <BaseForm
        :item="item"
        :purpose="purpose"
        resource-name="Persona"
        resource-gender="feminine"
        resource-route="person"
        :schema="personSchema"
        :initial-values="initialValues"
    >
        <template #default="{ form }">
            <div class="flex w-full flex-col gap-6 md:flex-row">
                <UFormField label="Nombre" name="name" class="w-full" required>
                    <UInput v-model="form.name" class="w-full" />
                </UFormField>

                <UFormField label="Nombre 「漢字」" name="kanjiName" class="w-full">
                    <UInput v-model="form.kanjiName" class="w-full" />
                </UFormField>
            </div>
            <div class="flex w-full flex-col gap-6 md:flex-row">
                <UFormField label="Apellido" name="surname" class="w-full">
                    <UInput v-model="form.surname" class="w-full" />
                </UFormField>

                <UFormField label="Apellido 「漢字」" name="kanjiSurname" class="w-full">
                    <UInput v-model="form.kanjiSurname" class="w-full" />
                </UFormField>
            </div>
        </template>
    </BaseForm>
</template>
