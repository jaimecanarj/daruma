<script setup lang="ts">
import BaseForm from '@/components/admin/forms/BaseForm.vue';
import { type Tag, TagForm } from '@/types';
import { tagTypes } from '@/utils/constants';
import { tagSchema } from '@/utils/zodSchemas';

const props = defineProps<{
    item?: Tag;
    purpose: 'create' | 'edit';
}>();

const initialValues: TagForm = {
    name: props.item?.name,
    type: props.item?.type,
};
</script>

<template>
    <BaseForm
        :item="item"
        :purpose="purpose"
        resource-name="Etiqueta"
        resource-gender="feminine"
        resource-route="tag"
        :schema="tagSchema"
        :initial-values="initialValues"
    >
        <template #default="{ form }">
            <div class="flex w-full flex-col gap-6 sm:flex-row">
                <UFormField label="Nombre" name="name" class="w-full" required>
                    <UInput v-model="form.name" class="w-full" />
                </UFormField>

                <UFormField label="Tipo" name="type" class="w-full" required>
                    <USelect v-model="form.type" :items="tagTypes" placeholder="Selecciona un tipo" class="w-full" />
                </UFormField>
            </div>
        </template>
    </BaseForm>
</template>
